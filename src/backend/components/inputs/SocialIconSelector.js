const { useState, useEffect, useRef } = wp.element;
const { __ } = wp.i18n;
import { ReactSortable } from "react-sortablejs";
import { kwtskConvertToSlug } from "../../helpers";

const SocialIconSelector = (props) => {
	const repeaterInputId =
		kwtskConvertToSlug(props.slug) || kwtskConvertToSlug(props.title);

	// Determine initial icons from props (saved value).
	const initialIcons = Array.isArray(props.value)
		? props.value
		: props.value?.icons || [];
	// Initialize local state from initialIcons.
	const [icons, setIcons] = useState(initialIcons);
	const [newIcon, setNewIcon] = useState("");
	// State to track which icon's label is being edited
	const [editingIndex, setEditingIndex] = useState(null);
	const [editingUrlIndex, setEditingUrlIndex] = useState(null);
	const hiddenInputRef = useRef(null);

	// If the saved icons arrive asynchronously and our state is still empty, update it.
	useEffect(() => {
		const newIcons = Array.isArray(props.value)
			? props.value
			: props.value?.icons || [];
		if (newIcons.length > 0 && icons.length === 0) {
			setIcons(newIcons);
		}
	}, [props.value]);

	// Update the hidden input and trigger parent's onChange when icons change.
	useEffect(() => {
		if (hiddenInputRef.current) {
			hiddenInputRef.current.value = JSON.stringify(icons);
			const event = new Event("change", { bubbles: true });
			hiddenInputRef.current.dispatchEvent(event);
		}
		if (props.onChange) {
			props.onChange({
				target: { name: repeaterInputId, value: icons },
			});
		}
	}, [icons, repeaterInputId]);

	// Custom icon validation: must have a space,
	// first word one of: fas, fab, fa-solid, or fa-brands,
	// and second word beginning with "fa-".
	const isValidIcon = (icon) => {
		const pattern = /^(fas|fab|fa-solid|fa-brands)\s+fa-[\w-]+$/;
		return pattern.test(icon);
	};

	// Compute a label from the icon string (for custom icons, default label is derived from the second word)
	const computeLabelFromIconString = (iconStr) => {
		const parts = iconStr.split(" ");
		return parts[1] ? parts[1].replace("fa-", "") : "";
	};

	const handleAddCustomIcon = (e) => {
		e.preventDefault();
		if (!isValidIcon(newIcon)) {
			return;
		}
		const trimmed = newIcon.trim();
		if (!icons.find((item) => item.value === trimmed)) {
			setIcons([
				...icons,
				{ value: trimmed, label: computeLabelFromIconString(trimmed) },
			]);
		}
		setNewIcon("");
	};

	const handleCustomIconKeyDown = (e) => {
		if (e.key === "Enter") {
			e.preventDefault();
			if (isValidIcon(newIcon)) {
				handleAddCustomIcon(e);
			}
		}
	};

	// For preset icons, compute the icon string (e.g. "fa-brands fa-facebook" or "fa-solid fa-envelope")
	const computeCheckboxIcon = (opt) => {
		const iconPrefix =
			opt.value === "email" || opt.value === "phone" ? "fa-solid" : "fa-brands";
		let linkIcon = opt.value;
		if (linkIcon === "phone") linkIcon = "phone-alt";
		if (linkIcon === "email") linkIcon = "envelope";
		return `${iconPrefix} fa-${linkIcon}`;
	};

	const isOptionSelected = (opt) => {
		const checkboxIcon = computeCheckboxIcon(opt);
		return icons.find((item) => item.value === checkboxIcon) ? true : false;
	};

	const handleCheckboxChange = (opt, checked) => {
		const checkboxIcon = computeCheckboxIcon(opt);
		if (checked) {
			if (!icons.find((item) => item.value === checkboxIcon)) {
				setIcons((prevIcons) => {
					const newIconObj = {
						value: checkboxIcon,
						label: opt.label || computeLabelFromIconString(checkboxIcon),
					};
					const newIcons = [...prevIcons, newIconObj];
					// Set the URL editing state to the new icon's index.
					setEditingUrlIndex(newIcons.length - 1);
					return newIcons;
				});
			}
		} else {
			// Optionally, clear URL editing if the removed icon is being edited.
			const removedIndex = icons.findIndex(
				(iconObj) => iconObj.value === checkboxIcon,
			);
			setIcons(icons.filter((iconObj) => iconObj.value !== checkboxIcon));
			if (removedIndex === editingUrlIndex) {
				setEditingUrlIndex(null);
			}
		}
	};

	const handleRemoveIcon = (index, e) => {
		e.preventDefault();
		e.stopPropagation();
		const newArr = [...icons];
		newArr.splice(index, 1);
		setIcons(newArr);
		if (editingIndex === index) {
			setEditingIndex(null);
		}

		if (editingUrlIndex === index) {
			setEditingUrlIndex(null);
		}
	};

	const handleLabelChange = (index, event) => {
		const newLabel = event.target.value;
		setIcons(
			icons.map((iconObj, i) =>
				i === index ? { ...iconObj, label: newLabel } : iconObj,
			),
		);
	};

	const handleUrlChange = (index, event) => {
		const newUrl = event.target.value;
		setIcons(
			icons.map((iconObj, i) =>
				i === index ? { ...iconObj, url: newUrl } : iconObj,
			),
		);
	};

	return (
		<>
			{/* Sortable icons list */}
			{icons.length > 0 && (
				<>
					<ReactSortable
						tag="div"
						className="kwtsk-selected-icons"
						list={icons}
						setList={setIcons}
						options={{
							animation: 300,
							onStart: (evt) => {
								evt.item.classList.add("dragging");
							},
							onEnd: (evt) => {
								evt.item.classList.remove("dragging");
							},
						}}
					>
						{icons.map((iconObj, index) => (
							<div
								key={`${iconObj.value}-${index}`}
								data-id={iconObj.value}
								className="kwtsk-checkbox-block"
							>
								<div
									className="kwtsk-checkbox-select selected"
									onClick={() => {
										setEditingIndex(index);
										setEditingUrlIndex(index);
									}}
								>
									<span className={`social-link-icon ${iconObj.value}`}></span>
									{editingIndex === index ? (
										<input
											type="text"
											value={iconObj.label}
											onChange={(e) => handleLabelChange(index, e)}
											onBlur={() => setEditingIndex(null)}
											onKeyDown={(e) => {
												if (e.key === "Enter") setEditingIndex(null);
											}}
											className="icon-txt"
											autoFocus
										/>
									) : (
										<span className="checkbox-label">{iconObj.label}</span>
									)}
								</div>
								<button
									className="kwtsk-remove fas fa-xmark"
									onClick={(e) => handleRemoveIcon(index, e)}
								/>
							</div>
						))}
					</ReactSortable>
				</>
			)}

			{editingUrlIndex !== null && (
				<div className="kwtsk-icon-input kwtsk-social-url-input">
					<h4>
						{`${icons[editingUrlIndex]?.label}: ` +
							__("Add Your Link:", "theme-site-kit")}
					</h4>
					<p>
						{__(
							"Need help on adding the correct links for the different icons?",
							"theme-site-kit",
						)}{" "}
						<a href="" target="_blank">
							{__("Read our documentation")}
						</a>
					</p>
					<input
						type="text"
						className="kwtsk-social-icon-url"
						placeholder={
							`${icons[editingUrlIndex]?.label || ""} ` +
							__("URL or Link", "theme-site-kit")
						}
						value={icons[editingUrlIndex]?.url || ""}
						onChange={(e) => handleUrlChange(editingUrlIndex, e)}
						//onBlur={() => setEditingUrlIndex(null)}
					/>
					{(icons[editingUrlIndex]?.value === "fa-solid fa-envelope" ||
						icons[editingUrlIndex]?.value === "fa-solid fa-phone-alt" ||
						icons[editingUrlIndex]?.value === "fa-brands fa-whatsapp" ||
						icons[editingUrlIndex]?.value === "fa-brands fa-line" ||
						icons[editingUrlIndex]?.value === "fa-brands fa-telegram") && (
						<p className="kwtsk-input-note">
							{icons[editingUrlIndex]?.value === "fa-solid fa-envelope" &&
								__(
									'For email link add - "mailto:name@youremail.com"',
									"theme-site-kit",
								)}
							{icons[editingUrlIndex]?.value === "fa-solid fa-phone-alt" &&
								__('For phone link add - "tel:+1234567890"', "theme-site-kit")}
							{icons[editingUrlIndex]?.value === "fa-brands fa-whatsapp" &&
								__(
									'For whatsapp link add - "https://wa.me/27824532805"',
									"theme-site-kit",
								)}
							{icons[editingUrlIndex]?.value === "fa-brands fa-line" &&
								__(
									'For line link add - "https://line.me/ti/p/~username"',
									"theme-site-kit",
								)}
							{icons[editingUrlIndex]?.value === "fa-brands fa-telegram" &&
								__(
									'For telegram link add - "https://t.me/username"',
									"theme-site-kit",
								)}
						</p>
					)}
				</div>
			)}

			{props.options && props.options.length > 0 && (
				<>
					<h4>{__("Select Icons:", "theme-site-kit")}</h4>
					<p>
						{__(
							"Select the icons you'd like to display on your website frontend.",
							"theme-site-kit",
						)}
					</p>
					<div className="kwtsk-checkbox-selects">
						{props.options.map((opt) => {
							const inputGroup = props.slug;
							const inputId = kwtskConvertToSlug(`${inputGroup}-${opt.value}`);
							const checkboxIcon = computeCheckboxIcon(opt);
							const selected = isOptionSelected(opt);
							return (
								<div key={opt.value} className="kwtsk-checkbox-options">
									<label
										className={`kwtsk-checkbox-select ${selected ? "selected" : ""}`}
										htmlFor={inputId}
									>
										<span className={`social-link-icon ${checkboxIcon}`}></span>
										<input
											id={inputId}
											name={`${inputGroup}_${opt.value}`}
											type="checkbox"
											checked={selected}
											onChange={(e) =>
												handleCheckboxChange(opt, e.target.checked)
											}
											className="checkbox-single"
										/>
										<span className="checkbox-label">{opt.label}</span>
									</label>
								</div>
							);
						})}
					</div>
				</>
			)}

			{/* Custom icon input with validation */}
			<h4>{__("Or add your own Custom Icons:", "theme-site-kit")}</h4>
			<p>
				{__(
					"Haven't found the icon you're looking for? Add your own custom icons.",
					"theme-site-kit",
				)}{" "}
				<a href="" target="_blank">
					{__("Add Font Awesome icons")}
				</a>
			</p>
			<div
				className={`kwtsk-icon-input ${
					newIcon ? (isValidIcon(newIcon) ? "valid" : "invalid") : ""
				}`}
			>
				<input
					type="text"
					placeholder={__("fa-brands fa-brave", "theme-site-kit")}
					value={newIcon}
					onChange={(e) => setNewIcon(e.target.value)}
					onKeyDown={handleCustomIconKeyDown}
				/>
				<button className="kwtsk-add" onClick={handleAddCustomIcon}>
					{__("Add Icon", "theme-site-kit")}
				</button>
			</div>

			<input
				type="hidden"
				id={repeaterInputId}
				name={repeaterInputId}
				ref={hiddenInputRef}
			/>
		</>
	);
};

export default SocialIconSelector;
