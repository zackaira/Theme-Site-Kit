import classnames from "classnames";
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { assign, merge } = lodash;
const { createHigherOrderComponent } = wp.compose;

// const isPremium = Boolean(kwtskEObj.isPremium);
const mmEnabled = Boolean(kwtskEObj.kwtskOptions?.mobilemenu?.enabled) || false;

const allowedmmBlockTypes = ["core/navigation"];

/**
 * Add New Attributes to all blocks
 */
function kwtskAddNavAtts(settings, name) {
	if (!mmEnabled) return settings;

	const showNavSettings = allowedmmBlockTypes.includes(name);

	const blockAnimationAtts = showNavSettings
		? {
				kwtskEnableMobileMenu: {
					type: "boolean",
					default: false,
				},
				kwtskPosition: {
					type: "string",
					default: "right",
				},
			}
		: {};

	return assign({}, settings, {
		attributes: merge(settings.attributes, blockAnimationAtts),
	});
}

/**
 * Add New Controls to all blocks
 */
const kwtskNavInspectorControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { Fragment } = wp.element;
		const { InspectorControls } = wp.blockEditor;
		const { PanelBody, ToggleControl, SelectControl } = wp.components;
		const {
			attributes: { kwtskEnableMobileMenu, kwtskPosition },
			setAttributes,
			name,
		} = props;

		const showNavSettings = allowedmmBlockTypes.includes(name);

		if (!mmEnabled) {
			return <BlockEdit {...props} />;
		}

		return (
			<Fragment>
				<BlockEdit {...props} />
				{showNavSettings && (
					<InspectorControls>
						<PanelBody
							title={__("Theme Site Kit Mobile Menu", "theme-site-kit")}
							initialOpen={false}
						>
							<ToggleControl
								checked={kwtskEnableMobileMenu}
								label={__("Enable Mobile Menu", "theme-site-kit")}
								onChange={(newValue) =>
									setAttributes({
										kwtskEnableMobileMenu: newValue,
									})
								}
							/>
							{kwtskEnableMobileMenu && (
								<>
									<SelectControl
										label={__("Mobile Menu Position", "theme-site-kit")}
										value={kwtskPosition}
										options={[
											{ label: __("Right", "theme-site-kit"), value: "right" },
											{ label: __("Left", "theme-site-kit"), value: "left" },
										]}
										onChange={(newValue) =>
											setAttributes({
												kwtskPosition: newValue,
											})
										}
									/>
								</>
							)}
						</PanelBody>
					</InspectorControls>
				)}
			</Fragment>
		);
	};
}, "kwtskNavInspectorControls");

/**
 * Add CSS Classes to the blocks in the editor
 */
const kwtskAddEditorNavAtts = createHigherOrderComponent((BlockListBlock) => {
	return (props) => {
		const {
			attributes: { kwtskEnableMobileMenu, kwtskPosition },
			className,
			name,
		} = props;
		const showNavSettings = allowedmmBlockTypes.includes(name);

		if (!mmEnabled || !showNavSettings) {
			return <BlockListBlock {...props} />;
		}

		const newClassnames = kwtskEnableMobileMenu
			? classnames(className, `kwtsk-mm kwtsk-mm-${kwtskPosition}`)
			: className;

		return <BlockListBlock {...props} className={newClassnames} />;
	};
}, "kwtskAddEditorNavAtts");

/**
 * WP Editor Hooks
 */
addFilter(
	"blocks.registerBlockType",
	"kwtsk/nav-mm-attributes",
	kwtskAddNavAtts,
);
addFilter(
	"editor.BlockEdit",
	"kwtsk/nav-mm-controls",
	kwtskNavInspectorControls,
);
addFilter(
	"editor.BlockListBlock",
	"kwtsk/nav-mm-editor-classes",
	kwtskAddEditorNavAtts,
);
// The frontend classes are added via a PHP filter in class-kwtsk-frontend.php
