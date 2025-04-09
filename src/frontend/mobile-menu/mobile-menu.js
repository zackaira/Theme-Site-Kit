import "./mobile-menu.css";

document.addEventListener("DOMContentLoaded", function () {
	const isPremium = kwtskMMObj?.isPremium;

	if (!isPremium) return;

	const customMobileMenus = document.querySelectorAll(
		"nav.wp-block-navigation.kwtsk-mm",
	);
	const body = document.querySelector("body");
	body.classList.add("kwtsk-mmenu");

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

			// Submenu toggle handlers
			const navItems = menu.querySelectorAll("li.wp-block-navigation-item");
			navItems.forEach((item) => {
				const submenuToggle = item.querySelector(
					"button.wp-block-navigation-submenu__toggle",
				);
				if (submenuToggle) {
					const clickHandler = function (e) {
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
						item.classList.toggle("open-sub");
						document.body.classList.toggle("has-menu-open");
					};

					// Remove old event listener if exists, then add the new one
					submenuToggle.removeEventListener("click", clickHandler);
					submenuToggle.addEventListener("click", clickHandler, {
						capture: true,
					});
				}
			});
		});
	}
});
