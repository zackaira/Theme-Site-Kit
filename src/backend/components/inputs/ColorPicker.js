const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
import { ChromePicker } from "react-color";
import { kwtskConvertToSlug } from "../../helpers";

const ColorPicker = (props) => {
	const colorTitleSlug = kwtskConvertToSlug(props.slug)
		? kwtskConvertToSlug(props.slug)
		: kwtskConvertToSlug(props.title);
	const defaultValue = props.defaultValue ? props.defaultValue : "#BBB";
	const [activeColor, setActiveColor] = useState(defaultValue);
	let allBtns = document.getElementsByClassName("kwtskColorPicker");

	useEffect(() => {
		props.value ? setActiveColor(props.value) : defaultValue;
	}, [props.value]);

	const onButtonFocus = (e) => {
		e.preventDefault();
		[...allBtns].forEach(function (item) {
			item.classList.remove("kwtskButton-active");
			item.removeAttribute("id");
		});

		e.target.closest(".kwtskColorPicker").setAttribute("id", "openColorPicker");
		e.target.closest(".kwtskColorPicker").classList.add("kwtskButton-active");
	};

	window.addEventListener("click", function (e) {
		const isElement = document.getElementById("openColorPicker");

		if (isElement) {
			if (!e.target == isElement || !isElement.contains(e.target)) {
				isElement.removeAttribute("id");
				isElement
					.closest(".kwtskColorPicker")
					.classList.remove("kwtskButton-active");
			}
		}
	});

	const handleColorChange = (newColor) => {
		if (typeof newColor === "object" && newColor !== null) {
			setActiveColor(newColor.hex);
		} else {
			setActiveColor(newColor);
		}
	};

	const changeColor = (newColor) => {
		const colorValue =
			typeof newColor === "object" && newColor !== null
				? newColor.hex
				: newColor;

		// Directly call the onChange prop with a synthetic event object.
		if (props.onChange) {
			props.onChange({
				target: {
					name: colorTitleSlug,
					value: colorValue,
				},
			});
		}
	};

	return (
		<div className="kwtskColorPicker">
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
				<ChromePicker
					color={activeColor}
					onChange={(newColor) => handleColorChange(newColor)}
					disableAlpha={true}
					onChangeComplete={(newColor) => changeColor(newColor)}
				/>
			</div>
		</div>
	);
};

export default ColorPicker;
