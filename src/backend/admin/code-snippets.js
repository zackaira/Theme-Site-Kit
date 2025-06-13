import "./code-snippets.css";
const { __ } = wp.i18n;

document.addEventListener("DOMContentLoaded", () => {
	// Show/Hide select pages setting
	const whereSetting = document.getElementById("kwtsk_where");
	const specificPages = document.getElementById("kwtsk_specific_pages");

	if (whereSetting && specificPages) {
		const show = ["specific", "all_exclude"].includes(whereSetting.value);

		specificPages.style.display = show ? "block" : "none";

		whereSetting.addEventListener("change", () => {
			const newShow = ["specific", "all_exclude"].includes(whereSetting.value);
			specificPages.style.display = newShow ? "block" : "none";
		});
	}

	// Code Editor
	const textarea = document.getElementById("kwtsk_code");
	const langSelect = document.getElementById("kwtsk_code_language");

	if (!textarea || !langSelect || !window.wp?.codeEditor) return;

	const minifySetting = document.getElementById("kwtsk_minify_opt");
	const settings = wp.codeEditor.defaultSettings;
	const editorWrapper = wp.codeEditor.initialize(textarea, settings);
	const editor = editorWrapper.codemirror;
	window.kwtEditor = editor;

	// Function to set PHP prefix
	function maybeSetPhpPrefix(lang) {
		const phpHeader = "<?php\n";
		if (lang === "php") {
			const current = editor.getValue();
			if (!current.startsWith("<?php")) {
				editor.setValue(phpHeader + current);
			}

			// Make first line read-only
			editor.markText(
				{ line: 0, ch: 0 },
				{ line: 0, ch: phpHeader.length - 1 },
				{
					readOnly: true,
					inclusiveLeft: true,
					inclusiveRight: true,
					className: "cm-noneditable",
				},
			);
		} else {
			// Clear all marks
			editor.getAllMarks().forEach((mark) => mark.clear());
		}
	}

	// Function to update wrapper tags around code
	function updateCodeWrapperTags(lang) {
		const prefixEl = document.querySelector(".code-prefix");
		const suffixEl = document.querySelector(".code-suffix");

		if (!prefixEl || !suffixEl) return;

		prefixEl.classList.remove("css", "javascript", "php");
		suffixEl.classList.remove("css", "javascript", "php");

		switch (lang) {
			case "css":
				prefixEl.textContent = '<style type="text/css">';
				suffixEl.textContent = "</style>";
				break;
			case "javascript":
				prefixEl.textContent = '<script type="text/javascript">';
				suffixEl.textContent = "</script>";
				break;
			case "php":
			default:
				prefixEl.textContent = "";
				suffixEl.textContent = "";
				break;
		}

		prefixEl.classList.add(lang);
		suffixEl.classList.add(lang);
	}

	// On page load
	maybeSetPhpPrefix(langSelect.value);
	updateCodeWrapperTags(langSelect.value);

	if (minifySetting) {
		minifySetting.style.display = langSelect.value === "php" ? "none" : "flex";
	}

	// On language change
	let currentLang = langSelect.value;

	langSelect.addEventListener("change", function () {
		const requestedLang = this.value;
		const hasContent = editor.getValue().trim() !== "";

		if (hasContent) {
			const confirmed = window.confirm(
				__(
					"Changing the language will clear the code editor. Click OK to continue, or Cancel to keep the current language.",
					"theme-site-kit",
				),
			);

			if (!confirmed) {
				this.value = currentLang;
				return;
			}

			editor.setValue("");
			editor.getAllMarks().forEach((mark) => mark.clear());
		}

		currentLang = requestedLang;
		editor.setOption("mode", requestedLang);
		maybeSetPhpPrefix(requestedLang);
		updateCodeWrapperTags(requestedLang);

		if (minifySetting) {
			minifySetting.style.display = requestedLang === "php" ? "none" : "flex";
		}
	});
});
