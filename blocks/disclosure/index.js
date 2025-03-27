import { registerBlockType } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
import json from "./block.json";
import Edit from "./edit";
import save from "./save";

import "./editor.css";
import "./style.css";

const { name } = json;
import icon from "../../assets/images/kwtsk-logo.png";

/**
 * Register the Block
 */
registerBlockType(name, {
	icon: <img src={icon} width="20" height="20" />,
	edit: Edit,
	save,
});
