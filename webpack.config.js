const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const settingsConfig = require("./webpack.config.admin");

module.exports = [
	{
		...settingsConfig,
	},
	// {
	// 	...defaultConfig,
	// },
];
