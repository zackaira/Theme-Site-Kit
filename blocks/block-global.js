/*
 * Convert Text to slug
 */
export const slugify = (str) =>
	str
		.toLowerCase()
		.trim()
		.replace(/[^\w\s-]/g, "")
		.replace(/[\s_-]+/g, "-")
		.replace(/^-+|-+$/g, "");

/*
 * Default Colors for Color Palette
 */
export const colorPickerPalette = [
	{ name: "white", color: "#FFF" },
	{ name: "Grey", color: "#9b9b9b" },
	{ name: "Black", color: "#000" },
	{ name: "Dark", color: "#232323" },
	{ name: "Turqoise", color: "#00b291" },
	{ name: "Emerald", color: "#51d88b" },
	{ name: "Peter River", color: "#3497df" },
	{ name: "Amethyst", color: "#9c56b8" },
	{ name: "Wet Asphalt", color: "#34495d" },
	{ name: "Sunflower", color: "#f1c50f" },
	{ name: "Carrot", color: "#e77e22" },
	{ name: "Alizarin", color: "#e84c3d" },
	{ name: "Clouds", color: "#ecf0f1" },
	{ name: "Concrete", color: "#95a5a5" },
	{ name: "Dusty Pink", color: "#d4afb9" },
	{ name: "Soft Purple", color: "#9cadce" },
	{ name: "Creamy", color: "#f2e8ce" },
	{ name: "Soil", color: "#874c48" },
];

/*
 * RichText Tags to select for RichText
 */
export const elementTags = [
	{ label: "H1", value: "h1" },
	{ label: "H2", value: "h2" },
	{ label: "H3", value: "h3" },
	{ label: "H4", value: "h4" },
	{ label: "H5", value: "h5" },
	{ label: "H6", value: "h6" },
	{ label: "div", value: "div" },
];

/*
 * Minimal RichText Settings
 */
export const minimalRichText = [
	"core/bold",
	"core/italic",
	"core/highlight",
	"core/subscript",
	"core/superscript",
	"core/strikethrough",
];
