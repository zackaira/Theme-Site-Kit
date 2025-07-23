const { useState } = wp.element;
const { __ } = wp.i18n;
import axios from "axios";
import SettingRow from "../SettingRow";
import { kwtskConvertToSlug, kwtskCapitalizeWords } from "../../helpers";

const CptsSelect = ({ title, slug, value, onChange, apiUrl, adminUrl }) => {
	const repeaterInputId = kwtskConvertToSlug(slug || title);
	const [newCptName, setNewCptName] = useState("");
	const [newSingularName, setNewSingularName] = useState("");
	const [validation, setValidation] = useState({ name: "", singular: "" });
	const [checking, setChecking] = useState({});
	const [creatingTemplate, setCreatingTemplate] = useState({});
	const [existingTemplates, setExistingTemplates] = useState({});

	const postTypes = value || {};

	const checkExistingTemplate = async (key, settings) => {
		try {
			const response = await axios.get(
				`${apiUrl}/check-template?post_type=${settings.slug}`,
				{
					headers: {
						'X-WP-Nonce': window.kwtskSObj.nonce
					}
				}
			);
			setExistingTemplates(prev => ({
				...prev,
				[key]: response.data.exists
			}));
		} catch (error) {
			console.error("Error checking template:", error);
		}
	};

	// Check for existing templates when post types change
	React.useEffect(() => {
		Object.entries(postTypes).forEach(([key, settings]) => {
			checkExistingTemplate(key, settings);
		});
	}, [postTypes]);

	const validateText = (text) => /^[a-zA-Z ]+$/.test(text);

	const handleCreateTemplate = async (key, settings) => {
		setCreatingTemplate(prev => ({ ...prev, [key]: true }));

		try {
			const response = await axios.post(
				`${apiUrl}/create-cpt-template`,
				{
					post_type: settings.slug,
					label: settings.label
				},
				{
					headers: {
						'X-WP-Nonce': window.kwtskSObj.nonce
					}
				}
			);

			if (response.data.success) {
				alert(__("Template created successfully!", "theme-site-kit"));
				// Update the existingTemplates state
				setExistingTemplates(prev => ({
					...prev,
					[key]: true
				}));
			} else {
				throw new Error(response.data.message || "Failed to create template");
			}
		} catch (error) {
			console.error("Error creating template:", error);
			alert(__("Failed to create template. Please try again.", "theme-site-kit"));
		} finally {
			setCreatingTemplate(prev => {
				const updated = { ...prev };
				delete updated[key];
				return updated;
			});
		}
	};

	const handleAddPostType = () => {
		const trimmedName = newCptName.trim();
		const trimmedSingular = newSingularName.trim();

		const itemKey = kwtskConvertToSlug(trimmedName, "_");
		const cleanSlug = kwtskConvertToSlug(trimmedName, "-");

		let valid = true;
		const newValidation = { name: "valid", singular: "valid" };

		if (!trimmedName || !validateText(trimmedName)) {
			newValidation.name = "invalid";
			valid = false;
		}
		if (!trimmedSingular || !validateText(trimmedSingular)) {
			newValidation.singular = "invalid";
			valid = false;
		}
		if (postTypes[itemKey]) {
			newValidation.name = "invalid";
			valid = false;
		}

		setValidation(newValidation);

		if (!valid) return;

		const updatedPostTypes = {
			...postTypes,
			[itemKey]: {
				key: itemKey,
				label: kwtskCapitalizeWords(trimmedName),
				slug: cleanSlug,
				singular: trimmedSingular,
				enable_categories: false,
				enable_tags: false,
				has_archive: true,
				category_slug: "",
				tag_slug: "",
				template: "default",
			},
		};

		onChange({
			target: {
				name: "cpts_post_types",
				value: updatedPostTypes,
			},
		});

		setNewCptName("");
		setNewSingularName("");
		setValidation({ name: "", singular: "" });
	};

	const handleToggleChange = (event) => {
		const { name, checked } = event.target;
		const match = name.match(
			/^cpts_post_types_(.+?)_(enable_categories|enable_tags|has_archive)$/,
		);
		if (!match) return;

		const key = match[1];
		const setting = match[2];

		if (!postTypes[key]) return;

		const updated = {
			...postTypes,
			[key]: {
				...postTypes[key],
				[setting]: checked,
			},
		};

		onChange({
			target: {
				name: "cpts_post_types",
				value: updated,
			},
		});
	};

	const handleInputChange = (key, field, value) => {
		const updated = {
			...postTypes,
			[key]: {
				...postTypes[key],
				[field]:
					field === "template"
						? value.replace(/^wp-custom-template-/, "")
						: value,
			},
		};

		onChange({
			target: {
				name: "cpts_post_types",
				value: updated,
			},
		});
	};

	const handleDelete = async (key) => {
		const postType = postTypes[key];
		if (!postType) return;

		setChecking((prev) => ({ ...prev, [key]: true }));

		try {
			const response = await fetch(
				`${apiUrl}/check-post-type?type=${postType.slug}`,
			);

			if (!response.ok) {
				throw new Error("Post type not found or fetch failed");
			}

			const data = await response.json();
			const total = data.count;

			if (total > 0) {
				const forceDelete = window.confirm(
					__(
						`This post type has posts or meta data still saved. Would you like to delete the post type and all its posts? This cannot be undone.`,
						"theme-site-kit",
					),
				);

				if (!forceDelete) {
					return;
				}
			} else {
				// If no posts exist, just confirm normal deletion
				if (
					!window.confirm(
						__(
							"Are you sure you want to delete this post type?",
							"theme-site-kit",
						),
					)
				)
					return;
			}

			// Delete the post type and its posts if any
			const deleteResponse = await fetch(
				`${apiUrl}/delete-post-type`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.kwtskSObj.nonce
					},
					body: JSON.stringify({
						post_type: postType.slug,
						force_delete: total > 0
					})
				}
			);

			if (!deleteResponse.ok) {
				throw new Error("Failed to delete post type");
			}

			const updated = { ...postTypes };
			delete updated[key];

			onChange({
				target: {
					name: "cpts_post_types",
					value: updated,
				},
			});
		} catch (err) {
			console.error("Error checking post count:", err);
			alert(__("An error occurred while checking posts.", "theme-site-kit"));
		} finally {
			setChecking((prev) => {
				const updated = { ...prev };
				delete updated[key];
				return updated;
			});
		}
	};

	return (
		<div className="cpts-select">
			<h4>{__(title || "Custom Post Types", "theme-site-kit")}</h4>

			<div className="cpts-add-new">
				<input
					type="text"
					placeholder={__(
						"Post Type Name (e.g. Case Studies)",
						"theme-site-kit",
					)}
					value={newCptName}
					className={validation.name}
					onChange={(e) => setNewCptName(e.target.value)}
				/>
				<input
					type="text"
					placeholder={__("Singular Name (e.g. Case Study)", "theme-site-kit")}
					value={newSingularName}
					className={validation.singular}
					onChange={(e) => setNewSingularName(e.target.value)}
				/>
				<button type="button" className="button" onClick={handleAddPostType}>
					{__("Add Post Type", "theme-site-kit")}
				</button>
			</div>

			{Object.keys(postTypes).length === 0 ? (
				<p className="no-cpts">
					{__("No custom post types added yet.", "theme-site-kit")}
				</p>
			) : (
				<>
					{Object.entries(postTypes).map(([key, settings], index) => (
						<table
							key={key}
							className={`form-table cpts-table ${index % 2 === 0 ? "even" : ""}`}
							role="presentation"
						>
							<tbody>
								<tr>
									<th className="cpts-label">{settings.label}</th>
									<td>
										<div className="cpts-single-sets">
											<div className="cpts-single-slug">
												<div className="cpts-single">
													{settings.singular}{" "}
													<span>
														{"(" + __("Singular", "theme-site-kit") + ")"}
													</span>
												</div>
												<div className="cpts-slug">
													<code>{settings.slug}</code>
												</div>
											</div>
											<div className="cpts-cats">
												<SettingRow
													slug={`cpts_post_types_${key}_enable_categories`}
													value={!!settings.enable_categories}
													inputType="onlytoggle"
													onChange={handleToggleChange}
												/>{" "}
												<span>- {__("Has Categories", "theme-site-kit")}</span>

												{settings.enable_categories && (
													<div className="cpts-permalink-input">
														<span>{__("URL:", "theme-site-kit")}{" "}</span>
														<input
															type="text"
															placeholder={`${settings.slug}-category`}
															value={settings.category_slug || ""}
															onChange={(e) => handleInputChange(key, "category_slug", e.target.value)}
														/>
													</div>
												)}
											</div>
											<div className="cpts-tags">
												<SettingRow
													slug={`cpts_post_types_${key}_enable_tags`}
													value={!!settings.enable_tags}
													inputType="onlytoggle"
													onChange={handleToggleChange}
												/>{" "}
												<span>- {__("Has Tags", "theme-site-kit")}</span>
												
												{settings.enable_tags && (
													<div className="cpts-permalink-input">
														<span>{__("URL:", "theme-site-kit")}{" "}</span>
														<input
															type="text"
															placeholder={`${settings.slug}-tag`}
															value={settings.tag_slug || ""}
															onChange={(e) => handleInputChange(key, "tag_slug", e.target.value)}
														/>
													</div>
												)}
											</div>
											<div className="cpts-archive">
												<SettingRow
													slug={`cpts_post_types_${key}_has_archive`}
													value={!!settings.has_archive}
													inputType="onlytoggle"
													onChange={handleToggleChange}
												/>{" "}
												<span>
													- {__("Has Archives Page", "theme-site-kit")}
												</span>
											</div>
											<div className="cpts-template">
												<button
													type="button"
													className={`button ${existingTemplates[key] ? 'has-template' : ''}`}
													onClick={() => handleCreateTemplate(key, settings)}
													disabled={creatingTemplate[key]}
												>
													{creatingTemplate[key]
														? __("Creating Template...", "theme-site-kit")
														: existingTemplates[key]
														? __("Recreate Template", "theme-site-kit")
														: __("Create Template", "theme-site-kit")}
												</button>
												{existingTemplates[key] && (
													<span className="template-exists">
														<i className="fa-solid fa-check"></i>
														{__("Template exists", "theme-site-kit")}
													</span>
												)}
											</div>

											<span
												className={`fa-solid fa-xmark cpts-del ${checking[key] ? "checking" : ""}`}
												onClick={() => handleDelete(key)}
												title={__("Delete this post type", "theme-site-kit")}
											></span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					))}
				</>
			)}

			<p className="cpts-note">
				{__(
					"Remember to Save Settings below, and then Save Permalinks after editing your Custom Post Types.",
					"theme-site-kit",
				)}{" "}
				<a href={`${adminUrl}options-permalink.php`} target="_blank">
					{__("Update Permalinks", "theme-site-kit")}
				</a>
			</p>
		</div>
	);
};

export default CptsSelect;
