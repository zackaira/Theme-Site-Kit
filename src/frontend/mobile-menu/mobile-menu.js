import "./mobile-menu.css";

let mobileMenuStarted = false;

function itemHasSubmenu(item) {
	return (
		item.classList.contains("has-child") ||
		item.classList.contains("wp-block-navigation-submenu") ||
		!!item.querySelector(":scope > ul.wp-block-navigation__submenu-container")
	);
}

function initMobileMenu() {
	if (mobileMenuStarted) return;
	if (!document.querySelector("nav.wp-block-navigation.kwtsk-mm")) return;
	mobileMenuStarted = true;

	const isPremium = kwtskMMObj?.isPremium;
	const menuWidth = kwtskMMObj?.menuWidth;

	if (!isPremium) return;

	const customMobileMenus = document.querySelectorAll(
		"nav.wp-block-navigation.kwtsk-mm",
	);
	const body = document.querySelector("body");
	body.classList.add("kwtsk-mmenu");

	// Set the menu width CSS custom property globally if width is provided and not default
	if (menuWidth && menuWidth !== '250px') {
		let formattedWidth = menuWidth;
		
		// If it's just a number (like 500), add 'px'
		if (!isNaN(menuWidth) && !menuWidth.toString().includes('%') && !menuWidth.toString().includes('px')) {
			formattedWidth = menuWidth + 'px';
		}
		
		document.documentElement.style.setProperty('--kwtsk-mmenu-width', formattedWidth);
	}

	if (customMobileMenus.length > 0) {
		customMobileMenus.forEach((menu) => {

			const menuButton = menu.querySelector(
				".wp-block-navigation__responsive-container-open",
			);
			const closeButton = menu.querySelector(
				".wp-block-navigation__responsive-container-close",
			);
			const responsiveContainer = menu.querySelector(
				".wp-block-navigation__responsive-container",
			);
			const responsiveContainerDialog = menu.querySelector(
				".wp-block-navigation__responsive-dialog",
			);
			const responsiveContainerDialogContent = menu.querySelector(
				".wp-block-navigation__responsive-container-content",
			);

			// Remove WordPress default attributes
			menu.removeAttribute("data-wp-interactive");
			menu.removeAttribute("data-wp-context");
			// menuButton.removeAttribute("aria-haspopup");
			menuButton.removeAttribute("data-wp-on-async--click");
			menuButton.removeAttribute("data-wp-on--keydown");
			closeButton.removeAttribute("data-wp-on-async--click");
			closeButton.removeAttribute("data-wp-bind--role");
			responsiveContainer.removeAttribute("data-wp-class--is-menu-open");
			responsiveContainer.removeAttribute("data-wp-on-async--focusout");
			responsiveContainer.removeAttribute("data-wp-watch");
			responsiveContainer.removeAttribute("data-wp-class--has-modal-open");
			responsiveContainer.removeAttribute("data-wp-on--keydown");
			// responsiveContainerDialog.removeAttribute("data-wp-bind--aria-modal");
			// responsiveContainerDialog.removeAttribute("data-wp-bind--aria-label");
			responsiveContainerDialog.removeAttribute("data-wp-bind--role");
			responsiveContainerDialogContent.removeAttribute("data-wp-watch");

			// Check if the menu should slide from the left
			const isLeft = menu.classList.contains("kwtsk-mm-left");

			// Functions to toggle menu open/close via body classes only
			const openMenu = () => {
				document.body.classList.add(
					isLeft ? "menu-open-left" : "menu-open-right",
				);
				responsiveContainer.classList.add("current-menu");
			};
			const closeMenu = () => {
				// Remove the body classes immediately
				document.body.classList.remove("menu-open-left", "menu-open-right");
				// Delay the removal of "current-menu" to let the transition run
				setTimeout(() => {
					responsiveContainer.classList.remove("current-menu");
				}, 300); // match this delay with your CSS transition duration
			};

			// Handle responsive state if needed
			const isHiddenByDefault =
				responsiveContainer.classList.contains("hidden-by-default");
			if (isHiddenByDefault) {
				menu.classList.add("mobile-on");
				closeMenu();
			} else {
				const checkWidth = () => {
					if (window.innerWidth < 600) {
						menu.classList.add("mobile-on");
						closeMenu();
					} else {
						menu.classList.remove("mobile-on");
						closeMenu();
					}
				};
				checkWidth();
				window.addEventListener("resize", checkWidth);
			}

			// Toggle menu open/close on menu button click
			menuButton.addEventListener("click", (e) => {
				e.preventDefault();
				e.stopPropagation();

				const isMenuOpen =
					document.body.classList.contains("menu-open-left") ||
					document.body.classList.contains("menu-open-right");

				if (isMenuOpen) {
					closeMenu();
				} else {
					openMenu();
				}
			});

			// Close menu on close button click
			closeButton.addEventListener("click", (e) => {
				e.preventDefault();
				e.stopPropagation();
				closeMenu();
			});

			// Click outside to close menu
			document.addEventListener("click", (e) => {
				const isMenuOpen =
					document.body.classList.contains("menu-open-left") ||
					document.body.classList.contains("menu-open-right");

				if (isMenuOpen) {
					const clickedInsideMenu = e.target.closest(
						".wp-block-navigation__responsive-container",
					);
					const clickedMenuButton = e.target.closest(
						".wp-block-navigation__responsive-container-open",
					);
					if (!clickedInsideMenu && !clickedMenuButton) {
						closeMenu();
					}
				}
			});

			// Drop Navigation interactivity on submenu items so core click,
			// hover, and focus handlers cannot swallow the chevron click.
			menu
				.querySelectorAll("li.has-child, button.wp-block-navigation-submenu__toggle")
				.forEach((el) => {
					[
						"data-wp-interactive",
						"data-wp-context",
						"data-wp-watch",
						"data-wp-on--focusout",
						"data-wp-on--keydown",
						"data-wp-on--pointerenter",
						"data-wp-on--pointerleave",
						"data-wp-on--click",
						"data-wp-on-async--click",
						"data-wp-bind--aria-expanded",
					].forEach((attr) => el.removeAttribute(attr));
				});

			// Chevron may be the toggle button (hover) or a sibling icon (click).
			// Delegate in the capture phase so the link stacked above it cannot win.
			responsiveContainer.addEventListener(
				"click",
				(e) => {
					let toggle = e.target.closest(
						"button.wp-block-navigation-submenu__toggle, .wp-block-navigation__submenu-icon",
					);

					// If a theme link is stretched over the chevron, a tap on the
					// right edge of that item is still a submenu toggle.
					if (!toggle) {
						const hit = e.target.closest(
							"a, button.wp-block-navigation-item__content",
						);
						const hitItem = hit?.closest("li");
						if (
							!hitItem ||
							!itemHasSubmenu(hitItem) ||
							!responsiveContainer.contains(hitItem)
						) {
							return;
						}
						const rect = hitItem.getBoundingClientRect();
						if (e.clientX < rect.right - 44) {
							return;
						}
						toggle = hitItem.querySelector(
							":scope > .wp-block-navigation__submenu-icon, :scope > button.wp-block-navigation-submenu__toggle",
						);
					}

					if (!toggle || !responsiveContainer.contains(toggle)) {
						return;
					}

					const item = toggle.closest("li");
					if (!item || !itemHasSubmenu(item)) {
						return;
					}

					e.preventDefault();
					e.stopPropagation();

					const isOpen = item.classList.toggle("open-sub");
					const button = item.querySelector(
						":scope > button.wp-block-navigation-submenu__toggle",
					);
					if (button) {
						button.setAttribute("aria-expanded", isOpen ? "true" : "false");
					}
				},
				true,
			);
		});
	}
}

// Footer script: the menu is already parsed, but readyState is still "loading".
// Run now so submenu attributes are removed before the Navigation module hydrates.
initMobileMenu();
document.addEventListener("DOMContentLoaded", initMobileMenu);
