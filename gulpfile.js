const gulp = require("gulp");
const zip = require("gulp-zip");

function bundle() {
	return gulp
		.src([
			"**/*",
			"!**/.DS_Store",
			"!node_modules/**",
			"!_starter-blocks/**",
			"!blocks/**",
			"!src/**",
			"!bundled/**",
			"!inc/**",
			"!gulpfile.js",
			"!package.json",
			"!package-lock.json",
			"!webpack.config.js",
			"!webpack.config.admin.js",
			"!.editorconfig",
			"!.gitignore",
			"!build/**",
		], { encoding: false })
		.pipe(zip("theme-site-kit.zip"))
		.pipe(gulp.dest("bundled"));
}

exports.bundle = bundle;
