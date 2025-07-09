import 'choices.js/public/assets/styles/choices.min.css';
import Choices from 'choices.js'; 
import "./code-snippets.css";
const { __ } = wp.i18n;

document.addEventListener("DOMContentLoaded", () => {
	/* <fs_premium_only> */
	// Initialize Choices.js for all select elements
	const selects = document.querySelectorAll('#kwtsk_selected_pages, #kwtsk_selected_posts, #kwtsk_selected_wc_pages, #kwtsk_selected_wc_products');
	selects.forEach(select => {
		// Make sure the select has the multiple attribute
		select.setAttribute('multiple', 'multiple');
		
		new Choices(select, {
			removeItemButton: true,
			removeItemIconText: () => `x`,
			searchEnabled: true,
			searchPlaceholderValue: __("Search...", "theme-site-kit"),
			placeholder: true,
			placeholderValue: __("Search...", "theme-site-kit"),
			classNames: {
				containerOuter: 'kwtsk-choices',
			},
			maxItemCount: -1, // Allow unlimited selections
			position: 'bottom'
		});
	});

	// Show/hide selectors based on where option
	function toggleSelectors() {
		const where = document.getElementById('kwtsk_where')?.value;
		
		// Get all select containers
		const containers = {
			pages: document.getElementById('kwtsk_pages_select'),
			posts: document.getElementById('kwtsk_posts_select'),
			woocommerce_pages: document.getElementById('kwtsk_wc_pages_select'),
			woocommerce_products: document.getElementById('kwtsk_wc_products_select')
		};
		
		// Hide all containers first
		Object.values(containers).forEach(container => {
			if (container) {
				container.style.display = 'none';
			}
		});
		
		// Show the appropriate container based on selection
		if (where && containers[where]) {
			containers[where].style.display = 'block';
		}
	}

	// Add event listener for change event
	const whereSelect = document.getElementById('kwtsk_where');
	if (whereSelect) {
		whereSelect.addEventListener('change', toggleSelectors);
		toggleSelectors(); // Initial state
	}

	/* </fs_premium_only> */

	/**
	 * Code Editor
	 */
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
