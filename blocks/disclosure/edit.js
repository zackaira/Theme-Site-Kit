import { __ } from "@wordpress/i18n";
import {
	RichText,
	AlignmentToolbar,
	BlockControls,
	InspectorControls,
	BlockAlignmentToolbar,
	useBlockProps,
} from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	RangeControl,
	TextControl,
	__experimentalUnitControl as UnitControl,
} from "@wordpress/components";

const Edit = (props) => {
	const {
		isSelected,
		attributes: {
			alignment,
			align,
			maxWidth,
			disclosureDesign,
			author,
			authorLink,
			showName,
			showAvatar,
			showTitle,
			disclosureTitle,
			disclosureCopy,
			discVertPad,
			discHorizPad,
		},
		setAttributes,
	} = props;

	const blockProps = useBlockProps({
		className: `align-${alignment} ${align}align`,
	});

	return (
		<div {...blockProps}>
			{isSelected && (
				<InspectorControls>
					<PanelBody
						title={__("Disclosure Settings", "theme-site-kit")}
						initialOpen={true}
					>
						<UnitControl
							label={__("Max Width", "theme-site-kit")}
							value={maxWidth}
							onChange={(value) => setAttributes({ maxWidth: value })}
							units={[
								{ value: "px", label: "px", default: 600 },
								{ value: "%", label: "%", default: 100 },
							]}
							isResetValueOnUnitChange
						/>
					</PanelBody>
					<PanelBody
						title={__("Disclosure Design", "theme-site-kit")}
						initialOpen={false}
					>
						<SelectControl
							label={__("Design", "theme-site-kit")}
							value={disclosureDesign}
							options={[
								{ label: "Default", value: "one" },
								{ label: "Minimal", value: "two" },
							]}
							onChange={(value) =>
								setAttributes({
									disclosureDesign: value === undefined ? "one" : value,
								})
							}
						/>

						<RangeControl
							label={__("Vertical Padding", "theme-site-kit")}
							value={discVertPad}
							onChange={(value) =>
								setAttributes({
									discVertPad: value === undefined ? 20 : value,
								})
							}
							min={0}
							max={100}
						/>
						<RangeControl
							label={__("Horizontal Padding", "theme-site-kit")}
							value={discHorizPad}
							onChange={(value) =>
								setAttributes({
									discHorizPad: value === undefined ? 20 : value,
								})
							}
							min={0}
							max={100}
						/>
					</PanelBody>
				</InspectorControls>
			)}
			{
				<BlockControls>
					<AlignmentToolbar
						value={alignment}
						onChange={(value) =>
							setAttributes({
								alignment: value,
							})
						}
					/>
					<BlockAlignmentToolbar
						value={align}
						controls={["left", "center", "right"]}
						onChange={(value) => setAttributes({ align: value })}
					/>
				</BlockControls>
			}
			<div className={`kwtsk- ${disclosureDesign}`}></div>
		</div>
	);
};

export default Edit;
