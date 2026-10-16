import "./admin.css";

let kwtskDuplicateToggle = null;
let kwtskDuplicateCloseTimer = null;

const kwtskDuplicateIsInside = (node) => {
	return !!(
		node &&
		node.closest &&
		(node.closest(".kwtsk-duplicate-toggle") ||
			node.closest(".kwtsk-duplicate-popover"))
	);
};

const kwtskCloseDuplicatePopover = () => {
	window.clearTimeout(kwtskDuplicateCloseTimer);
	document.querySelector(".kwtsk-duplicate-popover--list")?.remove();
	if (kwtskDuplicateToggle) {
		kwtskDuplicateToggle.setAttribute("aria-expanded", "false");
		kwtskDuplicateToggle = null;
	}
};

const kwtskScheduleDuplicateClose = () => {
	window.clearTimeout(kwtskDuplicateCloseTimer);
	kwtskDuplicateCloseTimer = window.setTimeout(kwtskCloseDuplicatePopover, 180);
};

const kwtskOpenDuplicatePopover = (toggle) => {
	if (kwtskDuplicateToggle === toggle && document.querySelector(".kwtsk-duplicate-popover--list")) {
		return;
	}

	kwtskCloseDuplicatePopover();

	const labels = window.kwtskAObj || {};
	const popover = document.createElement("div");
	popover.className = "kwtsk-duplicate-popover kwtsk-duplicate-popover--list kwtsk-duplicate-popover--floating";
	popover.setAttribute("role", "menu");

	const arrow = document.createElement("span");
	arrow.className = "kwtsk-duplicate-popover__arrow";
	arrow.setAttribute("aria-hidden", "true");

	const actions = document.createElement("div");
	actions.className = "kwtsk-duplicate-popover__actions";

	const createLink = document.createElement("a");
	createLink.className = "kwtsk-duplicate-popover__btn";
	createLink.href = toggle.dataset.stayUrl || toggle.getAttribute("href");
	createLink.textContent = labels.duplicateCreate || "Create Duplicate";
	createLink.setAttribute("role", "menuitem");

	const editLink = document.createElement("a");
	editLink.className = "kwtsk-duplicate-popover__btn is-primary";
	editLink.href = toggle.dataset.editUrl || toggle.getAttribute("href");
	editLink.textContent = labels.duplicateEdit || "Duplicate and Edit";
	editLink.setAttribute("role", "menuitem");

	const note = document.createElement("p");
	note.className = "kwtsk-duplicate-popover__note";
	note.textContent =
		labels.duplicateHelp ||
		"Saves a draft copy. Duplicate and Edit opens that draft.";

	actions.append(createLink, editLink);
	popover.append(arrow, actions, note);
	document.body.appendChild(popover);

	const rect = toggle.getBoundingClientRect();
	const popoverRect = popover.getBoundingClientRect();
	const toggleCenter = rect.left + rect.width / 2;
	let top = rect.bottom + 10;
	let placement = "below";

	if (top + popoverRect.height > window.innerHeight - 8) {
		top = Math.max(8, rect.top - popoverRect.height - 10);
		placement = "above";
	}

	let left = toggleCenter - popoverRect.width / 2;
	left = Math.max(8, Math.min(left, window.innerWidth - popoverRect.width - 8));

	popover.classList.add(placement === "above" ? "is-above" : "is-below");
	popover.style.top = `${top}px`;
	popover.style.left = `${left}px`;
	arrow.style.left = `${toggleCenter - left}px`;

	kwtskDuplicateToggle = toggle;
	toggle.setAttribute("aria-expanded", "true");
};

document.addEventListener("mouseover", (event) => {
	const toggle = event.target.closest(".kwtsk-duplicate-toggle");
	if (toggle) {
		window.clearTimeout(kwtskDuplicateCloseTimer);
		kwtskOpenDuplicatePopover(toggle);
		return;
	}

	if (event.target.closest(".kwtsk-duplicate-popover")) {
		window.clearTimeout(kwtskDuplicateCloseTimer);
	}
});

document.addEventListener("mouseout", (event) => {
	if (!kwtskDuplicateIsInside(event.target)) {
		return;
	}

	if (kwtskDuplicateIsInside(event.relatedTarget)) {
		return;
	}

	kwtskScheduleDuplicateClose();
});

document.addEventListener("focusin", (event) => {
	const toggle = event.target.closest(".kwtsk-duplicate-toggle");
	if (toggle) {
		kwtskOpenDuplicatePopover(toggle);
	}
});

document.addEventListener("keydown", (event) => {
	if (event.key === "Escape") {
		kwtskCloseDuplicatePopover();
	}
});

window.addEventListener(
	"scroll",
	() => {
		kwtskCloseDuplicatePopover();
	},
	true
);

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
