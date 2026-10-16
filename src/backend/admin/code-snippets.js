import 'choices.js/public/assets/styles/choices.min.css';
import Choices from 'choices.js'; 
import "./code-snippets.css";
const __ = window.wp?.i18n?.__ ? window.wp.i18n.__ : (text) => text;

document.addEventListener("DOMContentLoaded", () => {
	/* <fs_premium_only> */
	// Initialize Choices.js for all select elements.
	// Choices 11 class names are arrays; replacing containerOuter with a string
	// drops the base "choices" class and the library styles never apply.
	const selects = document.querySelectorAll('#kwtsk_selected_pages, #kwtsk_selected_posts, #kwtsk_selected_wc_pages, #kwtsk_selected_wc_products');
	selects.forEach(select => {
		select.setAttribute('multiple', 'multiple');
		
		new Choices(select, {
			removeItemButton: true,
			removeItemIconText: () => `x`,
			searchEnabled: true,
			searchPlaceholderValue: __("Search...", "theme-site-kit"),
			placeholder: true,
			placeholderValue: __("Search...", "theme-site-kit"),
			classNames: {
				containerOuter: ['choices', 'kwtsk-choices'],
			},
			maxItemCount: -1,
			position: 'bottom'
		});
	});

	// Show the selector that matches the current placement, and only let that
	// select submit. The others share the same field name.
	function toggleSelectors() {
		const where = document.getElementById('kwtsk_where')?.value;
		
		const containers = {
			pages: document.getElementById('kwtsk_pages_select'),
			posts: document.getElementById('kwtsk_posts_select'),
			woocommerce_pages: document.getElementById('kwtsk_wc_pages_select'),
			woocommerce_products: document.getElementById('kwtsk_wc_products_select')
		};
		
		Object.entries(containers).forEach(([key, container]) => {
			if (!container) {
				return;
			}

			const active = key === where;
			container.classList.toggle('is-active', active);
			container.querySelectorAll('select').forEach((select) => {
				if (active) {
					select.setAttribute('name', 'kwtsk_selected_items[]');
				} else {
					select.removeAttribute('name');
				}
			});
		});
	}

	const whereSelect = document.getElementById('kwtsk_where');
	if (whereSelect) {
		whereSelect.addEventListener('change', toggleSelectors);
		toggleSelectors();
	}

	/* </fs_premium_only> */

	/**
	 * Code Editor
	 */
	const textarea = document.getElementById("kwtsk_code");
	const langSelect = document.getElementById("kwtsk_code_language");

	const minifySetting = document.getElementById("kwtsk_minify_opt");

	function setMinifyVisibility(lang) {
		if (!minifySetting) {
			return;
		}
		minifySetting.classList.toggle("is-active", lang !== "php");
	}

	if (!textarea || !langSelect || !window.wp?.codeEditor) {
		if (langSelect) {
			setMinifyVisibility(langSelect.value);
		}
		return;
	}

	const settings = wp.codeEditor.defaultSettings || {};
	settings.codemirror = Object.assign({}, settings.codemirror, {
		mode: langSelect.value === "php" ? "php" : langSelect.value,
		lineNumbers: true,
		lineWrapping: true,
	});
	const editorWrapper = wp.codeEditor.initialize(textarea, settings);
	const editor = editorWrapper.codemirror;
	window.kwtEditor = editor;

	// CodeMirror measures the textarea before the metabox layout has settled,
	// so the editor paints unstyled until something forces a refresh.
	const refreshEditor = () => {
		if (editor) {
			editor.refresh();
		}
	};
	refreshEditor();
	window.requestAnimationFrame(refreshEditor);
	window.setTimeout(refreshEditor, 50);

	if (window.jQuery) {
		window.jQuery(document).on("postbox-toggled", (_event, postbox) => {
			if (postbox && postbox.id === "kwtsk_custom_code_box") {
				refreshEditor();
			}
		});
	}

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

	setMinifyVisibility(langSelect.value);

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

		setMinifyVisibility(requestedLang);
	});
});
