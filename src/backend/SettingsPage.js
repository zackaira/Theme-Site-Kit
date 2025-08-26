const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
import axios from "axios";
import SettingRow from "./components/SettingRow";
import SettingHeader from "./components/SettingHeader";
import SettingGroup from "./components/SettingGroup";
import Loader from "./Loader";
import SocialIcons from "../frontend/components/SocialIcons";
import GeneralPage from "./components/GeneralPage";
import { kwtskGroupSettings, kwtskSocialOptions } from "./helpers";

const SettingsPage = ({ kwtskObj }) => {
	const url = `${kwtskObj.apiUrl}kwtsk/v1`;
	const nonce = kwtskObj.nonce;
	const adminUrl = kwtskObj.adminUrl;
	const accountUrl = kwtskObj.accountUrl;
	const upgradeUrl = kwtskObj.upgradeUrl;
	const isPremium = Boolean(kwtskObj.isPremium);
	const postTypes = kwtskObj.post_types;
	const publishedPages = kwtskObj.publishedPages;
	// const pageTemplates = kwtskObj.pageTemplates;
	const [templatePages, setTemplatePages] = useState(publishedPages);
	const userRoles = kwtskObj.userRoles;

	const [loader, setLoader] = useState(false);
	const [loadSetting, setLoadSetting] = useState(true);
	const getInitialTab = () => {
		const params = new URLSearchParams(window.location.search);
		return params.get("tab") || "general";
	};
	const [activeTab, setActiveTab] = useState(getInitialTab());
	const kwtskDefaults = kwtskObj.kwtskDefaults;
	// const wcActive = Boolean(kwtskObj.wcActive);
	const [kwtskOptions, setKwtskOptions] = useState({});
	const [showSocialPreview, setShowSocialPreview] = useState(false);

	const [creatingPage, setCreatingPage] = useState(false);

	const changeTab = (tabId = "general") => {
		setActiveTab(tabId);

		const params = new URLSearchParams(window.location.search);
		params.set("tab", tabId);
		window.history.replaceState(null, "", "?" + params.toString());
	};

	console.log(kwtskOptions);

	// setState dynamically for each setting
	const handleChange = ({
		target: { type, checked, name, value, className },
	}) => {
		if (
			type === "checkbox" &&
			(className === "checkbox-single" ||
				className === "toggle-switch-checkbox")
		) {
			value = checked;
		}

		// Match pattern: cpts_post_types_{slug}_{setting}
		const cptMatch = name.match(
			/^cpts_post_types_(.+)_(enable_categories|enable_tags)$/,
		);

		if (cptMatch) {
			const postType = cptMatch[1];
			const settingName = cptMatch[2];

			setKwtskOptions((prevOptions) => ({
				...prevOptions,
				cpts: {
					...prevOptions.cpts,
					post_types: {
						...(prevOptions.cpts?.post_types || {}),
						[postType]: {
							...(prevOptions.cpts?.post_types?.[postType] || {}),
							[settingName]: value,
						},
					},
				},
			}));
			return;
		}

		// Check if the name contains an underscore (indicating a nested setting)
		const underscoreIndex = name.indexOf("_");
		if (underscoreIndex > -1) {
			const settingGroup = name.substring(0, underscoreIndex);
			const settingName = name.substring(underscoreIndex + 1);

			setKwtskOptions({
				...kwtskOptions,
				[settingGroup]: {
					// Use an empty object if the group isn’t defined yet
					...(kwtskOptions[settingGroup] || {}),
					[settingName]: value,
				},
			});
		} else {
			// For settings with no group
			setKwtskOptions({
				...kwtskOptions,
				[name]: value,
			});
		}
	};

	useEffect(() => {
		kwtskGroupSettings();
	}, [kwtskOptions]);

	// Submit form
	const handleSubmit = (e) => {
		e.preventDefault();
		setLoader(true);

		axios
			.post(
				url + "/settings",
				{ kwtskOptions },
				{
					// Add Nonce to prevent this working elsewhere
					headers: {
						"content-type": "application/json",
						"X-WP-NONCE": nonce,
					},
				},
			)
			.then((res) => {
				// console.log(res.data);
				// const kwtskOptions = JSON.parse(res.data.kwtskOptions);
				setLoader(false);
			});
	};

	const confirmDelete = (e) => {
		const deleteBtn = document.getElementsByClassName("kwtsk-delete");
		deleteBtn[0].classList.add("show-confirm");
		setTimeout(function () {
			deleteBtn[0].classList.remove("show-confirm");
		}, 2500);
	};

	const handleDeleteOptions = (e) => {
		e.preventDefault();
		if (
			window.confirm(
				__("Are you sure you want to delete all settings?", "theme-site-kit"),
			)
		) {
			setLoader(true);
			setLoadSetting(true);
			axios
				.delete(url + "/delete", {
					headers: {
						"X-WP-NONCE": nonce,
					},
				})
				.then((res) => {
					setLoader(false);
					location.reload();
				});
		}
	};

	// Get Settings from db
	useEffect(() => {
		axios
			.get(url + "/settings")
			.then((res) => {
				const kwtskOptions = res?.data
					? JSON.parse(res.data)
					: console.log("Theme Site Kit Options Empty");

				// setState dynamically for all settings
				if (kwtskOptions) {
					for (const key in kwtskOptions) {
						setKwtskOptions((prevState) => ({
							...prevState,
							[key]: kwtskOptions[key] ? kwtskOptions[key] : "",
						}));
					}
				} else {
					setKwtskOptions(kwtskDefaults); // Set settings to kwtskDefaults if not found
					// document.querySelector(".kwtskSaveBtn").click();
				}
				// console.log(kwtskOptions);
			})
			.then(() => {
				setLoadSetting(false);
			});
	}, []);

	const handleCreateTemplatePage = (e) => {
		e.preventDefault();
		setCreatingPage(true);

		axios
			.post(
				url + "/create-template-page",
				{},
				{ headers: { "X-WP-NONCE": nonce } },
			)
			.then((res) => {
				const { page_id, page_title } = res.data;
				// store the new template ID in your state
				setKwtskOptions((prev) => ({
					...prev,
					maintenance: { ...prev.maintenance, template: page_id },
				}));
				// 2) add it to the dropdown options
				setTemplatePages((prev) => ({
					...prev,
					[page_id]: page_title,
				}));
			})
			.catch((err) => {
				console.error(err);
				alert("Could not create template page.");
			})
			.finally(() => setCreatingPage(false));
	};

	return (
		<React.Fragment>
			<div className="kwtsk-settings">
				<div className="kwtskSettingBar">
					<h2>{__("Theme Site Kit Settings", "theme-site-kit")}</h2>
					<div className="kwtskSettingBarOptions">
						{isPremium && (
							<a
								href={accountUrl}
								className="fa-solid fa-user kwtsk-account"
								title={__("My Account", "theme-site-kit")}
							></a>
						)}

						<a
							href="https://kairaweb.com/documentation/"
							className="fa-solid fa-life-ring kwtsk-docs"
							title={__("Documentation", "theme-site-kit")}
							target="_blank"
						></a>
					</div>
				</div>

				<div
					className={`kwtsk-settings-content ${activeTab === "general" ? "hide-save" : ""}`}
				>
					<form id="kwtsk-settings-form" onSubmit={(e) => handleSubmit(e)}>
						<div className="kwtsk-tabs">
							<ul>
								<li>
									<a
										id="kwtsktab-general"
										className={`kwtsk-tab ${activeTab === "general" ? "active" : ""}`}
										onClick={() => changeTab("general")}
									>
										{__("General", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-disable_comments"
										className={`kwtsk-tab ${activeTab === "disable_comments" ? "active" : ""}`}
										onClick={() => changeTab("disable_comments")}
									>
										{__("Disable Comments", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-maintenance_mode"
										className={`kwtsk-tab ${activeTab === "maintenance_mode" ? "active" : ""}`}
										onClick={() => changeTab("maintenance_mode")}
									>
										{__("Maintenance Mode", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-social_links"
										className={`kwtsk-tab ${activeTab === "social_links" ? "active" : ""}`}
										onClick={() => changeTab("social_links")}
									>
										{__("Social Links", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-mobile_menu"
										className={`kwtsk-tab ${activeTab === "mobile_menu" ? "active" : ""}`}
										onClick={() => changeTab("mobile_menu")}
									>
										{__("Mobile Menu", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-cpts"
										className={`kwtsk-tab ${activeTab === "cpts" ? "active" : ""}`}
										onClick={() => changeTab("cpts")}
									>
										{__("Custom Post Types", "theme-site-kit")}
									</a>
								</li>
								<li>
									<a
										id="kwtsktab-extras"
										className={`kwtsk-tab ${activeTab === "extras" ? "active" : ""}`}
										onClick={() => changeTab("extras")}
									>
										{__("Extras", "theme-site-kit")}
									</a>
								</li>
							</ul>

							<div className="kwtsk-content-wrap">
								<div className="kwtsk-content-wrap-inner">
									{(loadSetting || loader) && <Loader />}

									<div
										id="kwtsk-content-general"
										className={`kwtsk-content ${
											activeTab === "general" ? "active" : ""
										}`}
									>
										<GeneralPage
											adminUrl={adminUrl}
											changeTab={() => changeTab("disable_comments")}
										/>
									</div>

									<div
										id="kwtsk-content-disable_comments"
										className={`kwtsk-content ${
											activeTab === "disable_comments" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Disable Comments", "theme-site-kit")}
											description={__(
												"Easily turn off comments across your entire site or selectively disable them per post or page. Keep your content clean and focused, without unnecessary clutter or spam.",
												"theme-site-kit",
											)}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__("Disable Comments", "theme-site-kit")}
													slug="disablecomments_disable"
													value={kwtskOptions.disablecomments?.disable}
													inputType="radio"
													options={[
														{
															value: "everywhere",
															label: __("Everywhere", "theme-site-kit"),
														},
														{
															value: "post_types",
															label: __("Post Types Only", "theme-site-kit"),
														},
													]}
													onChange={handleChange}
													customClass={"kwtsk-disable-comments"}
													notitle
												/>

												{kwtskOptions.disablecomments?.disable ===
													"post_types" && (
													<>
														{postTypes && (
															<tr>
																<th scope="row">
																	<h4>
																		{__(
																			"Disable Comments on:",
																			"theme-site-kit",
																		)}
																	</h4>
																</th>
																<td>
																	<table
																		className="form-table inner-setting-table"
																		role="presentation"
																	>
																		<tbody>
																			{Object.entries(postTypes).map(
																				([key, postType]) => (
																					<SettingRow
																						key={postType.name}
																						title={postType.label}
																						slug="disablecomments_post_types"
																						value={
																							kwtskOptions.disablecomments?.post_types?.includes(
																								postType.name,
																							) || false
																						}
																						inputType="toggle"
																						onChange={(e) => {
																							let updated =
																								kwtskOptions.disablecomments
																									?.post_types || [];
																							if (e.target.checked) {
																								updated = [
																									...updated,
																									postType.name,
																								];
																							} else {
																								updated = updated.filter(
																									(pt) => pt !== postType.name,
																								);
																							}
																							handleChange({
																								target: {
																									name: "disablecomments_post_types",
																									value: updated,
																								},
																							});
																						}}
																					/>
																				),
																			)}
																		</tbody>
																	</table>
																</td>
															</tr>
														)}
													</>
												)}
											</tbody>
										</table>
									</div>

									<div
										id="kwtsk-content-maintenance_mode"
										className={`kwtsk-content ${
											activeTab === "maintenance_mode" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Maintenance Mode", "theme-site-kit")}
											description={__(
												"Enable maintenance mode to temporarily hide your site from visitors while you build your website or make needed updates.",
												"theme-site-kit",
											)}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__(
														"Enable Maintenance Mode",
														"theme-site-kit",
													)}
													slug={`maintenance_enabled`}
													value={kwtskOptions.maintenance?.enabled}
													inputType="toggle"
													onChange={handleChange}
												/>

												{kwtskOptions.maintenance?.enabled && (
													<>
														<SettingRow
															title={__("Choose Mode", "linkt")}
															slug="maintenance_mode"
															value={kwtskOptions.maintenance?.mode}
															inputType="select"
															emptyOption={true}
															emptyLabel={__("Disabled", "theme-site-kit")}
															options={{
																maintenance: __(
																	"Maintenance Mode",
																	"theme-site-kit",
																),
																coming_soon: __(
																	"Coming Soon",
																	"theme-site-kit",
																),
															}}
															onChange={handleChange}
															note={
																kwtskOptions.maintenance?.mode
																	? kwtskOptions.maintenance?.mode ===
																		"maintenance"
																		? __(
																				"Maintenance Mode (returning HTTP 503 code)",
																				"theme-site-kit",
																			)
																		: __(
																				"Coming Soon mode (returning HTTP 200 code)",
																				"theme-site-kit",
																			)
																	: __(
																			"Choose between Coming Soon mode (returning HTTP 200 code) or Maintenance Mode (returning HTTP 503 code).",
																			"theme-site-kit",
																		)
															}
														/>

														{(kwtskOptions.maintenance?.mode ===
															"maintenance" ||
															kwtskOptions.maintenance?.mode ===
																"coming_soon") && (
															<>
																{isPremium && (
																	<SettingRow
																		title={__(
																			"Choose Template",
																			"theme-site-kit",
																		)}
																		slug="maintenance_template"
																		value={kwtskOptions.maintenance?.template}
																		inputType="select"
																		emptyOption={true}
																		emptyLabel={__(
																			"Default Basic Template",
																			"theme-site-kit",
																		)}
																		options={templatePages}
																		onChange={handleChange}
																		note={
																			kwtskOptions.maintenance?.template ? (
																				<>
																					<a
																						href={`${adminUrl}post.php?post=${kwtskOptions.maintenance.template}&action=edit`}
																						target="_blank"
																						rel="noopener noreferrer"
																					>
																						{__(
																							"Edit Template Page",
																							"theme-site-kit",
																						)}
																					</a>{" "}
																					<div>
																						{__(
																							"This page will be hidden, you can only edit it from here.",
																							"theme-site-kit",
																						)}
																					</div>
																				</>
																			) : (
																				<>
																					{creatingPage ? (
																						__(
																							"Creating page...",
																							"theme-site-kit",
																						)
																					) : (
																						<>
																							<a
																								href="#"
																								onClick={(e) => {
																									e.preventDefault();
																									handleCreateTemplatePage(e);
																								}}
																							>
																								{__(
																									"Create a New Template Page",
																									"theme-site-kit",
																								)}
																							</a>{" "}
																							<div>
																								{__(
																									"Remember to save your settings afterwards!",
																									"theme-site-kit",
																								)}
																							</div>
																						</>
																					)}
																				</>
																			)
																		}
																	/>
																)}

																{!kwtskOptions.maintenance?.template && (
																	<SettingGroup
																		label={__(
																			"Edit Template Page",
																			"theme-site-kit",
																		)}
																	>
																		{isPremium && (
																			<SettingRow
																				title={__(
																					"Background Color",
																					"theme-site-kit",
																				)}
																				slug="maintenance_bgcolor"
																				value={
																					kwtskOptions.maintenance?.bgcolor
																				}
																				inputType="colorpicker"
																				defaultValue="#f5f5f5"
																				onChange={handleChange}
																			/>
																		)}

																		<SettingRow
																			title={__("Title", "theme-site-kit")}
																			slug="maintenance_title"
																			value={kwtskOptions.maintenance?.title}
																			inputType="text"
																			onChange={handleChange}
																			placeholder={
																				kwtskOptions.maintenance?.mode ===
																				"coming_soon"
																					? __("Coming Soon", "theme-site-kit")
																					: __(
																							"Maintenance Mode",
																							"theme-site-kit",
																						)
																			}
																		/>
																		{isPremium && (
																			<SettingRow
																				title={__(
																					"Title Color",
																					"theme-site-kit",
																				)}
																				slug="maintenance_titlecolor"
																				value={
																					kwtskOptions.maintenance?.titlecolor
																				}
																				inputType="colorpicker"
																				defaultValue="#333"
																				onChange={handleChange}
																			/>
																		)}

																		<SettingRow
																			title={__("Text", "theme-site-kit")}
																			slug="maintenance_text"
																			value={kwtskOptions.maintenance?.text}
																			inputType="text"
																			onChange={handleChange}
																			placeholder={
																				kwtskOptions.maintenance?.mode ===
																				"coming_soon"
																					? __(
																							"Our website is launching soon. Stay tuned!",
																							"theme-site-kit",
																						)
																					: __(
																							"We are currently performing scheduled maintenance. Please check back soon.",
																							"theme-site-kit",
																						)
																			}
																		/>
																		{isPremium && (
																			<SettingRow
																				title={__(
																					"Text Color",
																					"theme-site-kit",
																				)}
																				slug="maintenance_textcolor"
																				value={
																					kwtskOptions.maintenance?.textcolor
																				}
																				inputType="colorpicker"
																				defaultValue="#666"
																				onChange={handleChange}
																			/>
																		)}
																	</SettingGroup>
																)}

																<SettingRow
																	title={__("Who Can Access the Site", "linkt")}
																	slug="maintenance_access"
																	value={kwtskOptions.maintenance?.access}
																	inputType="select"
																	options={{
																		loggedin: __(
																			"Logged In Users",
																			"theme-site-kit",
																		),
																		custom: __("Custom", "theme-site-kit"),
																	}}
																	onChange={handleChange}
																/>
																{kwtskOptions.maintenance?.access ===
																	"custom" && (
																	<>
																		<SettingRow
																			title={__(
																				"Select User Roles",
																				"theme-site-kit",
																			)}
																			slug="maintenance_userroles"
																			value={
																				kwtskOptions.maintenance?.userroles
																			}
																			inputType="multicheckbox"
																			options={userRoles}
																			onChange={handleChange}
																		/>
																	</>
																)}
															</>
														)}
														{!isPremium && (
															<>
																<br />
																<br />
																<SettingRow
																	slug="maintenance_pronote"
																	desc={__(
																		"Unlock full design control with Theme Site Kit Pro - customize default template colors, build your own Maintenance Mode page using the intuitive WordPress block editor, or instantly import our pre-designed page layouts to use for your 'Maintenance Mode' template.",
																		"theme-site-kit",
																	)}
																	inputType="pronote"
																	upgradeUrl={upgradeUrl}
																/>
															</>
														)}
													</>
												)}
											</tbody>
										</table>
									</div>

									<div
										id="kwtsk-content-social_links"
										className={`kwtsk-content ${
											activeTab === "social_links" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Social Links", "theme-site-kit")}
											description={__(
												"Add slick, always-visible floating icons for your social media profiles and contact options. Positioned neatly along the side of your site, they make it easy for visitors to connect with you anytime.",
												"theme-site-kit",
											)}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__("Enable Social Links", "theme-site-kit")}
													slug={`social_enabled`}
													value={kwtskOptions.social?.enabled}
													inputType="toggle"
													onChange={handleChange}
												/>

												{kwtskOptions.social?.enabled && (
													<>
														<SettingRow
															title={__("Show Preview", "theme-site-kit")}
															slug={`social_preview`}
															value={showSocialPreview}
															inputType="toggle"
															onChange={() =>
																setShowSocialPreview((state) => !state)
															}
														/>

														<SettingRow
															title={__("Position", "linkt")}
															slug="social_position"
															value={kwtskOptions.social?.position}
															inputType="select"
															options={{
																"right-top": __("Right Top", "theme-site-kit"),
																"right-bottom": __(
																	"Right Bottom",
																	"theme-site-kit",
																),
																"left-top": __("Left Top", "theme-site-kit"),
																"left-bottom": __(
																	"Left Bottom",
																	"theme-site-kit",
																),
																"bottom-left": __(
																	"Bottom Left",
																	"theme-site-kit",
																),
																"bottom-center": __(
																	"Bottom Center",
																	"theme-site-kit",
																),
																"bottom-right": __(
																	"Bottom Right",
																	"theme-site-kit",
																),
															}}
															onChange={handleChange}
														/>

														{kwtskOptions.social?.position !==
															"bottom-center" && (
															<SettingRow
																title={__("Offset", "theme-site-kit")}
																slug="social_offset"
																value={kwtskOptions.social?.offset}
																placeholder="150"
																inputType="number"
																onChange={handleChange}
																suffix="px"
															/>
														)}

														<SettingGroup
															label={__("Edit Social Icons", "theme-site-kit")}
														>
															<SettingRow
																title={__("Icon Size", "linkt")}
																slug="social_iconsize"
																value={kwtskOptions.social?.iconsize}
																inputType="select"
																options={{
																	xsmall: __("Extra Small", "theme-site-kit"),
																	small: __("Small", "theme-site-kit"),
																	medium: __("Medium", "theme-site-kit"),
																	large: __("Large", "theme-site-kit"),
																	xlarge: __("Extra Large", "theme-site-kit"),
																}}
																onChange={handleChange}
															/>
															<SettingRow
																title={__("Icon Spacing", "theme-site-kit")}
																slug="social_spacing"
																value={kwtskOptions.social?.spacing}
																inputType="range"
																defaultValue={8}
																min={1}
																max={40}
																suffix="px"
																onChange={handleChange}
															/>

															<SettingRow
																title={__("Icon Style", "linkt")}
																slug="social_style"
																value={kwtskOptions.social?.style}
																inputType="select"
																options={{
																	square: __("Square", "theme-site-kit"),
																	rounded: __("Rounded", "theme-site-kit"),
																	rounder: __("More Rounded", "theme-site-kit"),
																	round: __("Round", "theme-site-kit"),
																}}
																onChange={handleChange}
															/>

															<SettingRow
																title={__("Show Background", "theme-site-kit")}
																slug="social_showbg"
																value={kwtskOptions.social?.showbg}
																inputType="toggle"
																onChange={handleChange}
															/>

															{kwtskOptions.social?.showbg && (
																<SettingRow
																	title={__(
																		"Background Color",
																		"theme-site-kit",
																	)}
																	slug="social_iconbgcolor"
																	value={kwtskOptions.social?.iconbgcolor}
																	inputType="colorpicker"
																	defaultValue="#FFF"
																	onChange={handleChange}
																/>
															)}

															<SettingRow
																title={__("Label on Hover", "theme-site-kit")}
																slug="social_showtext"
																value={kwtskOptions.social?.showtext}
																inputType="toggle"
																onChange={handleChange}
															/>
															{isPremium && (
																<SettingRow
																	title={__(
																		"Icon Original Color",
																		"theme-site-kit",
																	)}
																	slug="social_iconorigcolor"
																	value={kwtskOptions.social?.iconorigcolor}
																	inputType="toggle"
																	onChange={handleChange}
																/>
															)}

															{(!kwtskOptions.social?.iconorigcolor ||
																kwtskOptions.social?.showtext) && (
																<SettingRow
																	title={
																		kwtskOptions.social?.iconorigcolor &&
																		kwtskOptions.social?.showtext
																			? __("Text Color", "theme-site-kit")
																			: __(
																					"Icon & Text Color",
																					"theme-site-kit",
																				)
																	}
																	slug="social_iconcolor"
																	value={kwtskOptions.social?.iconcolor}
																	inputType="colorpicker"
																	defaultValue="#AF2DBF"
																	onChange={handleChange}
																/>
															)}

															{!isPremium && (
																<SettingRow
																	slug="social_mini_pronote"
																	desc={__(
																		"Use original brand colors in Theme Site Kit Pro.",
																		"theme-site-kit",
																	)}
																	inputType="pronote"
																	mini
																/>
															)}
														</SettingGroup>

														{!isPremium &&
															kwtskOptions.social?.icons.length >= 4 && (
																<SettingRow
																	title={__(
																		"Upgrade to Theme Site Kit Pro",
																		"theme-site-kit",
																	)}
																	desc={__(
																		"Unlock unlimited social icons and more features with Theme Site Kit Pro.",
																		"theme-site-kit",
																	)}
																	upgradeUrl={upgradeUrl}
																	inputType="pronote"
																/>
															)}

														<SettingRow
															slug="social_icons"
															value={kwtskOptions.social?.icons}
															inputType="socialicons"
															options={kwtskSocialOptions}
															onChange={handleChange}
															notitle
															isPro={isPremium}
														/>
													</>
												)}
											</tbody>
										</table>
									</div>

									<div
										id="kwtsk-content-mobile_menu"
										className={`kwtsk-content ${
											activeTab === "mobile_menu" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Custom Mobile Menu", "theme-site-kit")}
											description={__(
												"Upgrade your site's mobile experience with an enhanced core Navigation Block extension. This feature creates a smooth, modern slide-out menu that looks great and works even better on mobile devices.",
												"theme-site-kit",
											)}
											// link={"https://www.youtube.com/watch?v=dQw4w9WgXcQ"}
											// linkText={__("Watch Video", "theme-site-kit")}
											isPro
											isPremium={isPremium}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__(
														"Enable Custom Mobile Menu",
														"theme-site-kit",
													)}
													slug={`mobilemenu_enabled`}
													value={kwtskOptions.mobilemenu?.enabled}
													inputType="toggle"
													onChange={handleChange}
												/>

												{kwtskOptions.mobilemenu?.enabled && (
													<>
														{isPremium ? (
															<>
																<SettingRow
																	title={__("Mobile Menu Colors", "linkt")}
																	slug="mobilemenu_style"
																	value={kwtskOptions.mobilemenu?.style}
																	inputType="select"
																	options={{
																		dark: __("Dark", "theme-site-kit"),
																		light: __("Light", "theme-site-kit"),
																		custom: __("Custom", "theme-site-kit"),
																	}}
																	onChange={handleChange}
																/>

																{kwtskOptions.mobilemenu?.style ===
																	"custom" && (
																	<>
																		<SettingRow
																			title={__(
																				"Background Color",
																				"theme-site-kit",
																			)}
																			slug="mobilemenu_bgcolor"
																			value={kwtskOptions.mobilemenu?.bgcolor}
																			inputType="colorpicker"
																			defaultValue="#1d2327"
																			onChange={handleChange}
																		/>
																		<SettingRow
																			title={__("Text Color", "theme-site-kit")}
																			slug="mobilemenu_textcolor"
																			value={kwtskOptions.mobilemenu?.textcolor}
																			inputType="colorpicker"
																			defaultValue="#b4b4b4"
																			onChange={handleChange}
																		/>

																		<SettingRow
																			title={__(
																				"Selected Text Color",
																				"theme-site-kit",
																			)}
																			slug="mobilemenu_selectedcolor"
																			value={kwtskOptions.mobilemenu?.selectedcolor}
																			inputType="colorpicker"
																			defaultValue="#FFF"
																			onChange={handleChange}
																		/>
																	</>
																)}

																<SettingRow
																	title={__("Menu Width", "theme-site-kit")}
																	slug="mobilemenu_width"
																	value={kwtskOptions.mobilemenu?.width}
																	inputType="text"
																	onChange={handleChange}
																	placeholder="250px"
																/>
															</>
														) : (
															<SettingRow
																title={__(
																	"Upgrade to Theme Site Kit Pro",
																	"theme-site-kit",
																)}
																desc={__(
																	"Theme Site Kit Pro introduces a refined slide-out mobile navigation design for the core editor's Navigation block, offering a cleaner and more intuitive user experience.",
																	"theme-site-kit",
																)}
																inputType="pronote"
																upgradeUrl={upgradeUrl}
															/>
														)}
													</>
												)}
											</tbody>
										</table>
									</div>

									<div
										id="kwtsk-content-cpts"
										className={`kwtsk-content ${
											activeTab === "cpts" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Custom Post Types", "theme-site-kit")}
											description={__(
												"Create and Manage custom Post Types on your WordPress site.",
												"theme-site-kit",
											)}
											isPro
											isPremium={isPremium}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__(
														"Enable Custom Post Types",
														"theme-site-kit",
													)}
													slug={`cpts_enabled`}
													value={kwtskOptions.cpts?.enabled}
													inputType="toggle"
													onChange={handleChange}
													note={
														kwtskOptions.cpts?.enabled &&
														__(
															"Once you've added your custom Post Type(s), you will need to refresh the page to view them in your WordPress admin.",
															"theme-site-kit",
														)
													}
												/>
												{kwtskOptions.cpts?.enabled && (
													<>
														{isPremium ? (
															<>
																<SettingRow
																	title={__(
																		"Custom Post Types",
																		"theme-site-kit",
																	)}
																	slug={`cpts_post_types`}
																	value={kwtskOptions.cpts?.post_types}
																	inputType="cptsoptions"
																	onChange={handleChange}
																	apiUrl={url}
																	adminUrl={adminUrl}
																	// pageTemplates={pageTemplates}
																/>
															</>
														) : (
															<SettingRow
																title="Upgrade to Theme Site Kit Pro"
																desc="Theme Site Kit Pro lets you add custom post types - special content formats beyond standard posts - to your WordPress dashboard, ideal for case studies, services, portfolios, etc."
																upgradeUrl={upgradeUrl}
																inputType="pronote"
															/>
														)}
													</>
												)}
											</tbody>
										</table>
									</div>

									<div
										id="kwtsk-content-extras"
										className={`kwtsk-content ${
											activeTab === "extras" ? "active" : ""
										}`}
									>
										<SettingHeader
											title={__("Extra Useful Features", "theme-site-kit")}
											description={__(
												"A few thoughtful Extra's to improve your workflow and enhance your frontend experience - small details that make a big difference.",
												"theme-site-kit",
											)}
										/>

										<table className="form-table" role="presentation">
											<tbody>
												<SettingRow
													title={__("Enable SVG Uploads", "theme-site-kit")}
													slug={`svgupload_enabled`}
													value={kwtskOptions.svgupload?.enabled}
													inputType="toggle"
													onChange={handleChange}
													note={
														kwtskOptions.svgupload?.enabled
															? __(
																	"You can now safely upload SVG files.",
																	"theme-site-kit",
																)
															: __(
																	"Allow SVG uploads in the Media Library. SVG files are XML-based vector images that can be scaled to any size without losing quality. They are often used for logos, icons, and other graphics on the web.",
																	"theme-site-kit",
																)
													}
												/>

												<SettingRow
													title={__("Code Snippets", "theme-site-kit")}
													slug={`code_enabled`}
													value={kwtskOptions.code?.enabled}
													inputType="toggle"
													onChange={handleChange}
													note={
														kwtskOptions.code?.enabled
															? __(
																	"You can now add custom code snippets to your site.",
																	"theme-site-kit",
																)
															: __(
																"Once enabled, refresh the page to see Code Snippets in your Dashboard sidebar.",
																"theme-site-kit",
															)
													}
												/>

												<SettingRow
													title={__("Duplicate Posts", "theme-site-kit")}
													slug={`duplicate_comingsoon`}
													value={kwtskOptions.duplicate?.comingsoon}
													inputType="toggle"
													onChange={handleChange}
													comingSoon
													// note={__(
													// 	"Once you've enabled this, refresh the page to then view and access Code Snippets in your Dashboard sidebar.",
													// 	"theme-site-kit",
													// )}
												/>
											</tbody>
										</table>
									</div>
								</div>

								<div className="kwtskSettingBar bottom">
									<div className="kwtskSettingBarMain">
										<button
											type="submit"
											className="button kwtskSaveBtn button-primary"
										>
											{__("Save Settings", "theme-site-kit")}
										</button>
										<div className="kwtskSaveBtnLoader">
											{(loadSetting || loader) && <Loader />}
										</div>
									</div>
									<div className="kwtskSettingBarOptions">
										<div
											className="kwtsk-delete"
											title={__("Reset Settings", "theme-site-kit")}
											onClick={confirmDelete}
										>
											<div className="kwtsk-confirm-delete">
												<a onClick={handleDeleteOptions}>
													{__(
														"Confirm... Reset All Settings!",
														"theme-site-kit",
													)}
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				{showSocialPreview && (
					<SocialIcons options={kwtskOptions.social} isPro={isPremium} />
				)}
			</div>
		</React.Fragment>
	);
};

export default SettingsPage;
