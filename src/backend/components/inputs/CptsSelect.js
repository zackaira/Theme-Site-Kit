const { useState } = wp.element;
const { __ } = wp.i18n;
import SettingRow from "../SettingRow";
import { kwtskConvertToSlug, kwtskCapitalizeWords } from "../../helpers";

const CptsSelect = ({ title, slug, value, onChange, apiUrl, adminUrl }) => {
	const repeaterInputId = kwtskConvertToSlug(slug || title);
	const [newCptName, setNewCptName] = useState("");
	const [newSingularName, setNewSingularName] = useState("");
	const [validation, setValidation] = useState({ name: "", singular: "" });
	const [checking, setChecking] = useState({});

	const postTypes = value || {};

	const validateText = (text) => /^[a-zA-Z ]+$/.test(text);

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
				template: "",
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

		// Set checking state for this key.
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
				alert(
					__(
						`Let's keep the database clean and make sure you delete all ${total} post(s) in "${postType.label}" before removing this post type from the Dashboard.`,
						"theme-site-kit",
					),
				);
				return;
			}

			if (
				!window.confirm(
					__(
						"Are you sure you want to delete this post type?",
						"theme-site-kit",
					),
				)
			)
				return;

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
			// Remove checking state for this key.
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
											</div>
											<div className="cpts-tags">
												<SettingRow
													slug={`cpts_post_types_${key}_enable_tags`}
													value={!!settings.enable_tags}
													inputType="onlytoggle"
													onChange={handleToggleChange}
												/>{" "}
												<span>- {__("Has Tags", "theme-site-kit")}</span>
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
											{/* <div className="cpts-template">
												<select
													value={settings.template || ""}
													onChange={(e) =>
														handleInputChange(key, "template", e.target.value)
													}
													className="cpts-template-select"
												>
													<option value="">
														{__("Select A Template", "theme-site-kit")}
													</option>
													{blockTemplates?.map((template) => (
														<option key={template.slug} value={template.slug}>
															{template.title}
														</option>
													))}
												</select>{" "}
												<span>
													- {__("Post Type Template", "theme-site-kit")}
												</span>
												<div>
													{__(
														"Select a template to use for this post type, or create your own one in the Site Editor. ",
														"theme-site-kit",
													)}
													<a
														href={`${adminUrl}site-editor.php?p=%2Ftemplate`}
														className="cpts-edit"
														target="_blank"
														rel="noopener noreferrer"
													>
														{__("Create a New Template", "theme-site-kit")}
													</a>
													<br />
													<small style={{ opacity: 0.8 }}>
														{__(
															"To auto-assign this template, name it exactly:",
															"theme-site-kit",
														)}{" "}
														<code>{`single-${settings.slug}`}</code>
													</small>
												</div>
											</div> */}

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
					"Remember to Save Permalinks after adding or making changes to Custom Post Types.",
					"theme-site-kit",
				)}{" "}
				<a href={`${adminUrl}options-permalink.php`} target="_blank">
					{__("Go to Permalinks", "theme-site-kit")}
				</a>
			</p>
		</div>
	);
};

export default CptsSelect;
