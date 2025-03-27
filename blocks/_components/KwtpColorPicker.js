import {
	Dropdown,
	Button,
	ColorIndicator,
	ColorPalette,
} from "@wordpress/components";

const KwtpColorPicker = (props) => {
	const { label, value, onChange, paletteColors } = props;

	return (
		<Dropdown
			className="kwtsk-colorpicker"
			contentClassName="kwtsk-editor-popup kwtsk-colorpicker-popup"
			popoverProps={{ placement: "bottom-start" }}
			renderToggle={({ isOpen, onToggle }) => (
				<Button
					variant="link"
					onClick={onToggle}
					className="kwtsk-colorpicker-btn"
				>
					<ColorIndicator colorValue={value} />
					<span>{label}</span>
				</Button>
			)}
			renderContent={() => (
				<ColorPalette
					colors={paletteColors}
					value={value}
					onChange={onChange}
				/>
			)}
		/>
	);
};

export default KwtpColorPicker;
