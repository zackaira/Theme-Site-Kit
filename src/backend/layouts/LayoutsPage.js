import React, { useState, useEffect } from "react";
const { __ } = wp.i18n;
const { Button } = wp.components;
import axios from "axios";
import LayoutItem from "./LayoutItem";

const LayoutsPage = ({ kwtskObj, svgOn }) => {
	const [collections, setCollections] = useState([]);
	const [installedPlugins, setInstalledPlugins] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);
	const [activeCollection, setActiveCollection] = useState(null);
	const [previewLayout, setPreviewLayout] = useState(null);
	const [importingLayoutId, setImportingLayoutId] = useState(null);
	const [importedPages, setImportedPages] = useState({});
	const isPremium = Boolean(kwtskObj.isPremium);
	const upgradeUrl = kwtskObj.upgradeUrl;
	const [pluginStatuses, setPluginStatuses] = useState({});

	const [categories, setCategories] = useState([]);
	const [tags, setTags] = useState([]);
	const [selectedCategory, setSelectedCategory] = useState("");
	const [selectedTag, setSelectedTag] = useState("");

	// Fetch layouts
	useEffect(() => {
		axios
			.get(`${kwtskObj.apiUrl}kwtsk/v1/layouts`)
			.then((response) => {
				setCollections(response.data.layouts);
				setCategories(response.data.categories);
				setTags(response.data.tags);
				setLoading(false);
			})
			.catch((error) => {
				console.error("Error fetching layouts:", error);
				setError("Error fetching layouts");
				setLoading(false);
			});
	}, [kwtskObj.apiUrl]);

	// Fetch installed plugins list.
	useEffect(() => {
		axios
			.get(`${kwtskObj.apiUrl}kwtsk/v1/installed-plugins`, {
				headers: {
					"X-WP-Nonce": kwtskObj.nonce,
				},
			})
			.then((response) => {
				setInstalledPlugins(response.data);
			})
			.catch((error) => {
				console.error("Error fetching installed plugins:", error);
			});
	}, [kwtskObj.apiUrl, kwtskObj.nonce]);

	useEffect(() => {
		if (previewLayout) {
			document.body.classList.add("kwtsk-modal-open");
		} else {
			document.body.classList.remove("kwtsk-modal-open");
		}
		return () => document.body.classList.remove("kwtsk-modal-open");
	}, [previewLayout]);

	if (loading) {
		return <div>{__("Loading layouts...", "theme-site-kit")}</div>;
	}
	if (error) {
		return <div>{error}</div>;
	}

	const filteredCollections = collections.filter((collection) => {
		if (
			selectedCategory &&
			collection.collectionCategory !== selectedCategory
		) {
			return false;
		}
		return true;
	});

	const importLayout = async (layout) => {
		const confirmed = window.confirm(
			__(
				"This layout will be imported and added as a new page. Continue?",
				"theme-site-kit",
			),
		);
		if (!confirmed) return;

		setImportingLayoutId(layout.id);

		try {
			const response = await axios.post(
				`${kwtskObj.apiUrl}kwtsk/v1/import-layout`,
				layout,
				{
					headers: {
						"X-WP-Nonce": kwtskObj.nonce,
						"Content-Type": "application/json",
					},
				},
			);
			const newPage = response.data;
			setImportedPages((prev) => ({
				...prev,
				[layout.id]: newPage.post_id,
			}));
			console.log("Layout imported successfully", newPage);
		} catch (err) {
			console.error(err);
			alert(
				__(
					"Failed to create page. Please try again or check your permissions.",
					"theme-site-kit",
				),
			);
		} finally {
			setImportingLayoutId(null);
		}
	};

	// Helper function to get required plugins for the active collection.
	function activeCollRequiredPlugins() {
		const activeColl = collections.find(
			(c) => c.collectionName === activeCollection,
		);
		return activeColl && activeColl.requiredPlugins
			? activeColl.requiredPlugins
			: [];
	}

	function getPluginStatus(plugin) {
		// Look for this plugin in the installed plugins list.
		const installed = installedPlugins.find((p) => p.slug === plugin.slug);
		let currentStatus = pluginStatuses[plugin.slug];
		if (!currentStatus) {
			if (installed) {
				currentStatus = installed.active ? "activated" : "activate";
			} else {
				currentStatus = "not_installed";
			}
		}
		return currentStatus;
	}

	const reqPlugins = activeCollRequiredPlugins();
	const allActivated =
		reqPlugins.length > 0 &&
		reqPlugins.every((plugin) => getPluginStatus(plugin) === "activated");

	let requiredPluginsDisplay = null;
	if (reqPlugins.length > 0 && !allActivated) {
		requiredPluginsDisplay = (
			<div className="kwtsk-required-plugins">
				<h4>{__("Required Plugins", "theme-site-kit")}</h4>
				<p>
					{__(
						"To use these layouts, please install the following plugins:",
						"theme-site-kit",
					)}
				</p>
				<ul>
					{reqPlugins.map((plugin) => {
						const status = getPluginStatus(plugin);
						let buttonContent;
						if (status === "installing") {
							buttonContent = (
								<span>{__("Installing Plugin...", "theme-site-kit")}</span>
							);
						} else if (status === "activating") {
							buttonContent = (
								<span>{__("Activating Plugin...", "theme-site-kit")}</span>
							);
						} else if (status === "activated") {
							buttonContent = (
								<span>{__("Already installed", "theme-site-kit")}</span>
							);
						} else if (status === "activate") {
							buttonContent = (
								<Button
									onClick={async () => {
										setPluginStatuses((prev) => ({
											...prev,
											[plugin.slug]: "activating",
										}));
										try {
											const res = await axios.post(
												`${kwtskObj.apiUrl}kwtsk/v1/install-plugin`,
												{ slug: plugin.slug },
												{
													headers: {
														"X-WP-Nonce": kwtskObj.nonce,
														"Content-Type": "application/json",
													},
												},
											);
											setPluginStatuses((prev) => ({
												...prev,
												[plugin.slug]: "activated",
											}));
										} catch (e) {
											setPluginStatuses((prev) => ({
												...prev,
												[plugin.slug]: undefined,
											}));
										}
									}}
									className="kwtsk-req-btn activate"
								>
									{__("Activate", "theme-site-kit")}
								</Button>
							);
						} else {
							// Default: not installed.
							buttonContent = (
								<Button
									onClick={async () => {
										setPluginStatuses((prev) => ({
											...prev,
											[plugin.slug]: "installing",
										}));
										try {
											const res = await axios.post(
												`${kwtskObj.apiUrl}kwtsk/v1/install-plugin`,
												{ slug: plugin.slug },
												{
													headers: {
														"X-WP-Nonce": kwtskObj.nonce,
														"Content-Type": "application/json",
													},
												},
											);
											const message = res.data.message.toLowerCase();
											if (message.includes("installed but inactive")) {
												setPluginStatuses((prev) => ({
													...prev,
													[plugin.slug]: "activate",
												}));
											} else if (
												message.includes("already installed") ||
												message.includes("activated")
											) {
												setPluginStatuses((prev) => ({
													...prev,
													[plugin.slug]: "activated",
												}));
											}
										} catch (e) {
											setPluginStatuses((prev) => ({
												...prev,
												[plugin.slug]: undefined,
											}));
										}
									}}
									className="kwtsk-req-btn"
								>
									{__("Install Now", "theme-site-kit")}
								</Button>
							);
						}
						return (
							<li key={plugin.slug}>
								<span>{plugin.name}</span> {buttonContent}
							</li>
						);
					})}
				</ul>
			</div>
		);
	}

	let content;
	if (activeCollection === null) {
		content = (
			<>
				<div className="kwtsk-page-title">
					<h2>{__("Import Page Layouts", "theme-site-kit")}</h2>
					<p>
						{__(
							"Welcome to the Import Page Layouts tool — your shortcut to quickly building new pages using professionally designed Gutenberg layouts. Instead of building from scratch, simply browse the different layout collections below.",
							"theme-site-kit",
						)}
					</p>
					{!svgOn && (
						<p className="kwtsk-svg-warning">
							{__(
								"Please ensure SVG Uploads are enabled, as some of these layouts include SVG images.",
								"theme-site-kit",
							)}{" "}
							<a
								href={`${kwtskObj.adminUrl}options-general.php?page=theme-site-kit-settings&tab=extras`}
							>
								{__("Go to SVG Uploads", "")}
							</a>
						</p>
					)}
				</div>
				<div className="kwtsk-filters">
					<select
						value={selectedCategory}
						onChange={(e) => setSelectedCategory(e.target.value)}
					>
						<option value="">{__("All Categories")}</option>
						{categories.map((cat) => (
							<option key={cat} value={cat}>
								{cat}
							</option>
						))}
					</select>
				</div>
				<div className="kwtsk-collections">
					{filteredCollections.length > 0 ? (
						filteredCollections.map((collection) => (
							<div key={collection.collectionName} className="kwtsk-collection">
								<h3>{collection.collectionName}</h3>
								<div className="kwtsk-collection-content">
									<div className="kwtsk-collection-preview">
										<img
											src={collection.previewImage}
											alt={collection.collectionName}
										/>

										{collection.isProLayout && !isPremium && (
											<a
												href={upgradeUrl}
												className="fa-solid fa-web-awesome kwtsk-pro-icon"
											></a>
										)}
									</div>
									<div className="kwtsk-collection-info">
										<Button
											onClick={() =>
												setActiveCollection(collection.collectionName)
											}
											className="kwtsk-collection-btn"
										>
											{__("View Layout Collection", "theme-site-kit")}
										</Button>
									</div>
								</div>
							</div>
						))
					) : (
						<div className="kwtsk-no-layouts">
							{__("No layout collections available.", "theme-site-kit")}
						</div>
					)}
				</div>
			</>
		);
	} else {
		let childLayouts = collections.find(
			(c) => c.collectionName === activeCollection,
		)?.layouts;
		if (selectedTag) {
			childLayouts = childLayouts.filter(
				(layout) => layout.tags && layout.tags.includes(selectedTag),
			);
		}
		const activeCollectionObj = collections.find(
			(c) => c.collectionName === activeCollection,
		);

		content = (
			<div className="kwtsk-layouts-collection">
				<div className="kwtsk-collection-header">
					<h2>{activeCollection}</h2>
					<Button
						onClick={() => setActiveCollection(null)}
						className="kwtsk-btn"
					>
						{"<<"} {__("Go Back", "theme-site-kit")}
					</Button>
					<div className="kwtsk-tags">
						{tags.map((tag) => (
							<button
								key={tag}
								className={`kwtsk-tag${selectedTag === tag ? " active" : ""}`}
								onClick={() => setSelectedTag(selectedTag === tag ? "" : tag)}
							>
								{tag}
							</button>
						))}
					</div>
				</div>
				{requiredPluginsDisplay}
				<div className="kwtsk-layout-items">
					{childLayouts && childLayouts.length > 0 ? (
						childLayouts.map((layout, index) => (
							<LayoutItem
								key={layout.id || index}
								layout={layout}
								importLayout={importLayout}
								handlePreviewLayout={setPreviewLayout}
								isImporting={
									importingLayoutId !== null && importingLayoutId === layout.id
								}
								importedPageId={importedPages[layout.id]}
								isProLayout={activeCollectionObj?.isProLayout}
								isPremium={isPremium}
								upgradeUrl={upgradeUrl}
								adminUrl={kwtskObj.adminUrl}
							/>
						))
					) : (
						<div>
							{__("No layouts available in this collection.", "theme-site-kit")}
						</div>
					)}
				</div>
			</div>
		);
	}

	return (
		<div className="kwtsk-page-layouts">
			{content}

			{previewLayout && (
				<div
					className="kwtsk-layout-modal-overlay"
					onClick={() => setPreviewLayout(null)}
				>
					<div
						className="kwtsk-layout-modal-content"
						onClick={(e) => e.stopPropagation()}
					>
						<button
							className="kwtsk-layout-modal-close"
							onClick={() => setPreviewLayout(null)}
						>
							×
						</button>
						<div className="kwtsk-layout-modal-header">
							<div>
								<h4>{previewLayout.title}</h4>
								<p>{previewLayout.description}</p>
							</div>
							{isPremium ? (
								<Button
									onClick={() => importLayout(previewLayout)}
									className="kwtsk-btn"
								>
									{__("Import Layout", "theme-site-kit")}
								</Button>
							) : (
								<a href={upgradeUrl} className="kwtsk-btn">
									{__("Upgrade to Pro", "theme-site-kit")}
								</a>
							)}
						</div>
						<div className="kwtsk-layout-modal-img">
							<img src={previewLayout.previewImage} alt={previewLayout.title} />
						</div>
					</div>
				</div>
			)}
		</div>
	);
};

export default LayoutsPage;
