import React, { useState, useEffect } from "react";
const { __ } = wp.i18n;
const { Button } = wp.components;
import axios from "axios";
import LayoutItem from "./LayoutItem";

const LayoutsPage = ({ kwtskObj }) => {
	const [layouts, setLayouts] = useState([]);
	const [loading, setLoading] = useState(true);
	const [error, setError] = useState(null);
	const [activeCollection, setActiveCollection] = useState(null);
	const [previewLayout, setPreviewLayout] = useState(null);
	const [importingLayoutId, setImportingLayoutId] = useState(null);
	const [importedPages, setImportedPages] = useState({});

	const [categories, setCategories] = useState([]);
	const [tags, setTags] = useState([]);
	const [selectedCategory, setSelectedCategory] = useState("");
	const [selectedTag, setSelectedTag] = useState("");

	useEffect(() => {
		axios
			.get("https://layouts.kairaweb.com/block-layouts/layouts-api.php", {
				headers: { "X-Auth-Token": "GETLYUTS" },
			})
			.then((response) => {
				setLayouts(response.data.layouts);
				setCategories(response.data.categories);
				setTags(response.data.tags);
				setLoading(false);
			})
			.catch((error) => {
				console.error("Error fetching layouts:", error);
				setError("Error fetching layouts");
				setLoading(false);
			});
	}, []);

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

	const filteredLayouts = layouts.filter((layout) => {
		if (selectedCategory && layout.collectionCategory !== selectedCategory) {
			return false;
		}
		if (selectedTag && !layout.tags.includes(selectedTag)) {
			return false;
		}
		return true;
	});

	// Group after filtering
	const groupedLayouts = filteredLayouts.reduce((g, layout) => {
		const key = layout.collectionName || "Other";
		g[key] = g[key] || [];
		g[key].push(layout);
		return g;
	}, {});

	const handleViewCollection = (collectionName) => {
		setActiveCollection(collectionName);
	};

	const handlePreviewLayout = (layout) => {
		setPreviewLayout(layout);
	};

	const handleClosePreview = () => {
		setPreviewLayout(null);
	};

	const handleBack = () => {
		setActiveCollection(null);
	};

	const importLayout = async (layout) => {
		const confirmed = window.confirm(
			__(
				"This layout will be imported and added as a new page. Continue?",
				"theme-site-kit",
			),
		);
		if (!confirmed) return;

		setImportingLayoutId(layout.id);

		console.log("layout:", layout);

		try {
			const response = await axios.post(
				`${kwtskObj.apiUrl}wp/v2/pages`,
				{
					title: layout.title,
					content: layout.blockContent,
					status: "draft",
				},
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
				[layout.id]: newPage.id,
			}));
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

	let content;
	if (activeCollection) {
		const collectionLayouts = groupedLayouts[activeCollection] || [];
		content = (
			<div className="kwtsk-layouts-collection">
				<div className="kwtsk-collection-header">
					<h2>{activeCollection}</h2>
					<Button onClick={handleBack} className="kwtsk-btn">
						{"<<"} {__("Go Back", "theme-site-kit")}
					</Button>
				</div>
				<div className="kwtsk-layout-items">
					{collectionLayouts.map((layout, index) => (
						<LayoutItem
							key={layout.id || index}
							layout={layout}
							importLayout={importLayout}
							handlePreviewLayout={handlePreviewLayout}
							isImporting={
								importingLayoutId !== null && importingLayoutId === layout.id
							}
							importedPageId={importedPages[layout.id]}
							adminUrl={kwtskObj.adminUrl}
						/>
					))}
				</div>
			</div>
		);
	} else {
		content = (
			<>
				<div className="kwtsk-page-title">
					<h2>{__("Import Page Layouts", "kw=theme-pro")}</h2>
					<p>
						{__(
							"Welcome to the Import Page Layouts tool — your shortcut to quickly building new pages using professionally designed Gutenberg layouts. Instead of building from scratch, simply browse the different layouts, choose a layout you like, import it, and jump straight into editing your content.",
							"theme-site-kit",
						)}
					</p>
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

				{Object.keys(groupedLayouts).length > 0 ? (
					Object.keys(groupedLayouts).map((collectionName) => (
						<div key={collectionName} className="kwtsk-layouts-collection">
							<div className="kwtsk-collection-header">
								<h2>{collectionName}</h2>
								<Button
									onClick={() => handleViewCollection(collectionName)}
									className="kwtsk-btn"
								>
									{__("View Layout Collection", "theme-site-kit")} {">>"}
								</Button>
							</div>
							<div className="kwtsk-layout-items">
								{groupedLayouts[collectionName].map((layout, index) => (
									<LayoutItem
										key={layout.id || index}
										layout={layout}
										importLayout={importLayout}
										handlePreviewLayout={handlePreviewLayout}
										isImporting={
											importingLayoutId !== null &&
											importingLayoutId === layout.id
										}
										importedPageId={importedPages[layout.id]}
										adminUrl={kwtskObj.adminUrl}
									/>
								))}
							</div>
						</div>
					))
				) : (
					<div className="kwtsk-no-layouts">
						{__("No layouts available.", "theme-site-kit")}
					</div>
				)}
			</>
		);
	}

	return (
		<div className="kwtsk-page-layouts">
			{content}

			{previewLayout && (
				<div
					className="kwtsk-layout-modal-overlay"
					onClick={handleClosePreview}
				>
					<div
						className="kwtsk-layout-modal-content"
						onClick={(e) => e.stopPropagation()}
					>
						<button
							className="kwtsk-layout-modal-close"
							onClick={handleClosePreview}
						>
							×
						</button>

						<div className="kwtsk-layout-modal-header">
							<div>
								<h4>{previewLayout.title}</h4>
								<p>{previewLayout.description}</p>
							</div>
							<Button
								onClick={() => importLayout(previewLayout)}
								className="kwtsk-btn"
							>
								{__("Import Layout", "theme-site-kit")}
							</Button>
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
