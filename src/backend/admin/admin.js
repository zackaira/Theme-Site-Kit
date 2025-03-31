import "./admin.css";

document.addEventListener("DOMContentLoaded", () => {
	const kwtskRemFsLinks = document.querySelectorAll(
		".fs-submenu-item.theme-site-kit",
	);
	if (kwtskRemFsLinks) {
		kwtskRemFsLinks.forEach((item) => {
			item.closest("li").remove();
		});
	}
});
