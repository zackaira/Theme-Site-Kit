const { useState } = wp.element;
const { __ } = wp.i18n;
import SettingRow from "../SettingRow";
import { kwtskConvertToSlug, kwtskCapitalizeWords } from "../../helpers";

const CptsSelect = ({ title, slug, value, onChange, apiUrl }) => {
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
			/^cpts_post_types_(.+?)_(enable_categories|enable_tags)$/,
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
				[field]: value,
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
				<table className="form-table inner-setting-table" role="presentation">
					<thead>
						<tr>
							<th>{__("Post Type Name", "theme-site-kit")}</th>
							<th>{__("Singular Name", "theme-site-kit")}</th>
							<th className="cpts-slug">{__("Slug", "theme-site-kit")}</th>
							<th className="cpts-cats">
								{__("Add Categories", "theme-site-kit")}
							</th>
							<th className="cpts-tags">{__("Add Tags", "theme-site-kit")}</th>
							<th className="cpts-delete"></th>
						</tr>
					</thead>
					<tbody>
						{Object.entries(postTypes).map(([key, settings], index) => (
							<tr
								key={key}
								className={`cpts-row ${index % 2 === 0 ? "even" : ""}`}
							>
								<td>
									<input
										type="text"
										value={settings.label}
										onChange={(e) =>
											handleInputChange(key, "label", e.target.value)
										}
									/>
								</td>
								<td>
									<input
										type="text"
										value={settings.singular || ""}
										onChange={(e) =>
											handleInputChange(key, "singular", e.target.value)
										}
									/>
								</td>
								<td>
									<code>{settings.slug}</code>
								</td>
								<td className="center">
									<SettingRow
										slug={`cpts_post_types_${key}_enable_categories`}
										value={!!settings.enable_categories}
										inputType="onlytoggle"
										onChange={handleToggleChange}
									/>
								</td>
								<td className="center">
									<SettingRow
										slug={`cpts_post_types_${key}_enable_tags`}
										value={!!settings.enable_tags}
										inputType="onlytoggle"
										onChange={handleToggleChange}
									/>
								</td>
								<td className="center">
									<span
										className={`fa-solid fa-xmark cpts-del ${checking[key] ? "checking" : ""}`}
										onClick={() => handleDelete(key)}
									></span>
								</td>
							</tr>
						))}
					</tbody>
				</table>
			)}
		</div>
	);
};

export default CptsSelect;
