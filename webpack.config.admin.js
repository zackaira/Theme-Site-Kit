const path = require("path");
const { CleanWebpackPlugin } = require("clean-webpack-plugin"); // Delete old files
const TerserPlugin = require("terser-webpack-plugin"); // JS minify
const autoprefixer = require("autoprefixer");
const MiniCSSExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("css-minimizer-webpack-plugin");

const config = {
	entry: {
		settings: "./src/settings.js",
		"settings.min": "./src/settings.js",
		admin: "./src/backend/admin/admin.js",
		"admin.min": "./src/backend/admin/admin.js",
		layouts: "./src/backend/layouts/layouts.js",
		"layouts.min": "./src/backend/layouts/layouts.js",
		frontend: "./src/frontend/frontend.js",
		"frontend.min": "./src/frontend/frontend.js",
		editor: "./src/backend/editor/editor.js",
		"editor.min": "./src/backend/editor/editor.js",
		"mobile-menu.min": "./src/frontend/mobile-menu/mobile-menu.js",
		"code-snippets": "./src/backend/admin/code-snippets.js",
		"code-snippets.min": "./src/backend/admin/code-snippets.js",
		"scroll-animation.min":
			"./src/frontend/scroll-animation/scroll-animation.js",
	},
	output: {
		filename: (pathData) => {
			return pathData.chunk.name === "mobile-menu.min"
				? "pro/[name].js"
				: "[name].js";
		},
		path: path.resolve(__dirname, "dist"),
		library: ["theme-site-kit", "[name]"],
		libraryTarget: "this",
	},
	optimization: {
		minimize: true,
		minimizer: [
			new TerserPlugin({
				// include: /(\.min)\.(js|css)$/,
				exclude: /(?<!\.min)\.(js|css)$/,
				extractComments: false,
			}),
			new OptimizeCSSAssetsPlugin({
				exclude: /(?<!\.min)\.css$/,
			}),
		],
	},
	plugins: [
		// new CleanWebpackPlugin(),
		new MiniCSSExtractPlugin({
			// filename: "[name].css",
			filename: (pathData) => {
				return pathData.chunk.name === "mobile-menu.min"
					? "pro/[name].css"
					: "[name].css";
			},
		}),
	],
	devtool: false, // "cheap-module-source-map", // https://webpack.js.org/configuration/devtool/
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader",
					options: {
						presets: ["@babel/preset-env", "@babel/preset-react"],
					},
				},
			},
			{
				test: /\.css$/,
				use: [
					// "style-loader",
					MiniCSSExtractPlugin.loader,
					"css-loader",
					{
						loader: "postcss-loader",
						options: {
							postcssOptions: {
								plugins: [
									autoprefixer({ overrideBrowserslist: ["last 2 versions"] }),
								],
							},
						},
					},
				],
			},
			{
				test: /\.(png|jpg|jpeg|gif|svg)$/i,
				type: "asset/resource",
				generator: {
					filename: "[hash][ext][query]",
				},
			},
		],
	},
	externals: {
		react: "React",
		"react-dom": "ReactDOM",
		lodash: "lodash",
		"@wordpress/element": "wp.element",
		"@wordpress/i18n": "wp.i18n",
		"@wordpress/data": "wp.data",
		"@wordpress/block-editor": "wp.blockEditor",
		"@wordpress/components": "wp.components",
		"@wordpress/api-fetch": "wp.apiFetch",
	},
};

module.exports = config;
