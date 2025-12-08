const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
import { HexColorPicker } from "react-colorful";
import { kwtskConvertToSlug } from "../../helpers";

const ColorPicker = (props) => {
	const colorTitleSlug = kwtskConvertToSlug(props.slug)
		? kwtskConvertToSlug(props.slug)
		: kwtskConvertToSlug(props.title);
	const defaultValue = props.defaultValue ? props.defaultValue : "#BBB";
	const [activeColor, setActiveColor] = useState(defaultValue);
	let allBtns = document.getElementsByClassName("kwtskColorPicker");

	useEffect(() => {
		if (props.value) {
			setActiveColor(props.value);
		}
	}, [props.value]);

	useEffect(() => {
		const handleWindowClick = (e) => {
			const isElement = document.getElementById("openColorPicker");

			if (isElement) {
				if (e.target !== isElement && !isElement.contains(e.target)) {
					isElement.removeAttribute("id");
					isElement
						.closest(".kwtskColorPicker")
						.classList.remove("kwtskButton-active");
				}
			}
		};

		window.addEventListener("click", handleWindowClick);

		return () => {
			window.removeEventListener("click", handleWindowClick);
		};
	}, []);

	const onButtonFocus = (e) => {
		e.preventDefault();
		[...allBtns].forEach(function (item) {
			item.classList.remove("kwtskButton-active");
			item.removeAttribute("id");
		});

		e.target.closest(".kwtskColorPicker").setAttribute("id", "openColorPicker");
		e.target.closest(".kwtskColorPicker").classList.add("kwtskButton-active");
	};

	const handleColorChange = (newColor) => {
		// react-colorful returns hex string directly
		setActiveColor(newColor);

		// Directly call the onChange prop with a synthetic event object.
		if (props.onChange) {
			props.onChange({
				target: {
					name: colorTitleSlug,
					value: newColor,
				},
			});
		}
	};

	return (
		<div className="kwtskColorPicker" style={{ display: "block" }}>
			<div className="kwtskColorDisplay">
				<button
					className="kwtskColorBtn"
					style={{ backgroundColor: activeColor }}
					onClick={(e) => e.preventDefault()}
					onFocus={(e) => onButtonFocus(e)}
					// onBlur={(e) => onButtonBlur(e)}
				>
					<span className="kwtskColorBtnTxt">
						{__("Select Color", "theme-site-kit")}
					</span>
				</button>
				<input
					type="text"
					id={colorTitleSlug}
					value={activeColor || ""}
					className="kwtskColorInput"
					disabled
					onChange={props.onChange}
				/>
			</div>

			<div className="kwtskPickColor">
				<HexColorPicker color={activeColor} onChange={handleColorChange} />
			</div>
		</div>
	);
};

export default ColorPicker;
