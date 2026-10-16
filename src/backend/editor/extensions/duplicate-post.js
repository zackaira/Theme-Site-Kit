const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { useSelect, useDispatch } = wp.data;
const { Fragment, useEffect, useLayoutEffect, useRef, useState, createPortal } =
	wp.element;
const { Button } = wp.components;
const apiFetch = wp.apiFetch;

const duplicateEnabled = Boolean(kwtskEObj?.kwtskOptions?.duplicate?.enabled);

const excludedTypes = [
	"attachment",
	"revision",
	"nav_menu_item",
	"wp_template",
	"wp_template_part",
	"wp_navigation",
	"wp_block",
	"wp_global_styles",
	"custom_css",
	"wp_font_family",
	"wp_font_face",
];

/**
 * Insert a Duplicate button immediately after Move to trash.
 */
function DuplicatePostButton() {
	const [anchor, setAnchor] = useState(null);
	const [isOpen, setIsOpen] = useState(false);
	const [isAbove, setIsAbove] = useState(false);
	const [isDuplicating, setIsDuplicating] = useState(false);
	const buttonRef = useRef(null);
	const popoverRef = useRef(null);
	const closeTimer = useRef(null);
	const { createSuccessNotice } = useDispatch("core/notices");

	const showPopover = () => {
		window.clearTimeout(closeTimer.current);
		setIsOpen(true);
	};

	const hidePopover = () => {
		closeTimer.current = window.setTimeout(() => setIsOpen(false), 180);
	};

	const { postId, postType, status, isDirty, isNew, canDuplicate } = useSelect(
		(select) => {
			const editor = select("core/editor");
			const core = select("core");
			if (!editor) {
				return {};
			}

			const currentType = editor.getCurrentPostType();
			const currentId = editor.getCurrentPostId();
			const canCheck = !!currentType && !!currentId;

			return {
				postId: currentId,
				postType: currentType,
				status: editor.getEditedPostAttribute("status"),
				isDirty: editor.isEditedPostDirty(),
				isNew: editor.isEditedPostNew(),
				canDuplicate:
					canCheck &&
					core.canUser("update", {
						kind: "postType",
						name: currentType,
						id: currentId,
					}) &&
					core.canUser("create", {
						kind: "postType",
						name: currentType,
					}),
			};
		},
		[]
	);

	useEffect(() => {
		let slot = null;
		let frame = 0;

		const place = () => {
			const summary = document.querySelector(".editor-post-summary");
			const trash = summary ? summary.querySelector(".editor-post-trash") : null;

			if (!trash || !trash.parentElement) {
				return;
			}

			if (slot && slot.isConnected && slot.previousElementSibling === trash) {
				return;
			}

			if (!slot || !slot.isConnected) {
				slot = document.createElement("div");
				slot.className = "kwtsk-duplicate-post";
			}

			trash.insertAdjacentElement("afterend", slot);
			setAnchor(slot);
		};

		const schedule = () => {
			window.cancelAnimationFrame(frame);
			frame = window.requestAnimationFrame(place);
		};

		schedule();

		const root =
			document.querySelector(".interface-interface-skeleton") || document.body;
		const observer = new MutationObserver(schedule);
		observer.observe(root, { childList: true, subtree: true });

		return () => {
			window.cancelAnimationFrame(frame);
			observer.disconnect();
			if (slot) {
				slot.remove();
			}
		};
	}, []);

	useLayoutEffect(() => {
		if (!isOpen || !buttonRef.current || !popoverRef.current) {
			return;
		}

		const rect = buttonRef.current.getBoundingClientRect();
		const popover = popoverRef.current;
		const popRect = popover.getBoundingClientRect();
		const center = rect.left + rect.width / 2;
		let top = rect.bottom + 10;
		let above = false;

		if (top + popRect.height > window.innerHeight - 8) {
			top = Math.max(8, rect.top - popRect.height - 10);
			above = true;
		}

		let left = center - popRect.width / 2;
		left = Math.max(8, Math.min(left, window.innerWidth - popRect.width - 8));
		popover.style.top = `${top}px`;
		popover.style.left = `${left}px`;

		const arrow = popover.querySelector(".kwtsk-duplicate-popover__arrow");
		if (arrow) {
			arrow.style.left = `${center - left}px`;
		}

		setIsAbove((current) => (current === above ? current : above));
	}, [isOpen]);

	const canShow =
		anchor &&
		postId &&
		!isNew &&
		status !== "auto-draft" &&
		status !== "trash" &&
		!excludedTypes.includes(postType) &&
		canDuplicate;

	if (!canShow) {
		return null;
	}

	const duplicatePost = async (openEditor) => {
		if (isDuplicating) {
			return;
		}

		if (
			isDirty &&
			!window.confirm(
				__(
					"You have unsaved changes. Duplicate the last saved version?",
					"theme-site-kit"
				)
			)
		) {
			return;
		}

		setIsDuplicating(true);

		try {
			const result = await apiFetch({
				path: "/kwtsk/v1/duplicate-post",
				method: "POST",
				data: { id: postId },
			});

			if (!result?.editUrl) {
				throw new Error(__("Could not duplicate this item.", "theme-site-kit"));
			}

			if (openEditor) {
				window.location.href = result.editUrl;
				return;
			}

			setIsDuplicating(false);
			createSuccessNotice(__("Duplicate created.", "theme-site-kit"), {
				type: "snackbar",
				actions: [
					{
						label: __("Edit duplicate", "theme-site-kit"),
						url: result.editUrl,
					},
				],
			});
		} catch (error) {
			setIsDuplicating(false);
			window.alert(
				error?.message || __("Could not duplicate this item.", "theme-site-kit")
			);
		}
	};

	return (
		<Fragment>
			{createPortal(
				<div
					ref={buttonRef}
					className="kwtsk-duplicate-post__dropdown"
					onMouseEnter={showPopover}
					onMouseLeave={hidePopover}
					onFocus={showPopover}
					onBlur={hidePopover}
				>
					<Button
						variant="secondary"
						className="kwtsk-duplicate-post__button"
						isBusy={isDuplicating}
						disabled={isDuplicating}
						aria-expanded={isOpen}
						onClick={() => duplicatePost(false)}
						__next40pxDefaultSize
					>
						{__("Duplicate", "theme-site-kit")}
					</Button>
				</div>,
				anchor
			)}
			{isOpen &&
				createPortal(
					<div
						ref={popoverRef}
						className={`kwtsk-duplicate-popover kwtsk-duplicate-popover--floating ${
							isAbove ? "is-above" : "is-below"
						}`}
						role="menu"
						onMouseEnter={showPopover}
						onMouseLeave={hidePopover}
					>
						<span className="kwtsk-duplicate-popover__arrow" aria-hidden="true" />
						<div className="kwtsk-duplicate-popover__actions">
							<button
								type="button"
								className="kwtsk-duplicate-popover__btn"
								onClick={() => {
									setIsOpen(false);
									duplicatePost(false);
								}}
							>
								{__("Create Duplicate", "theme-site-kit")}
							</button>
							<button
								type="button"
								className="kwtsk-duplicate-popover__btn is-primary"
								onClick={() => {
									setIsOpen(false);
									duplicatePost(true);
								}}
							>
								{__("Duplicate and Edit", "theme-site-kit")}
							</button>
						</div>
						<p className="kwtsk-duplicate-popover__note">
							{__(
								"Saves a draft copy. Duplicate and Edit opens that draft.",
								"theme-site-kit"
							)}
						</p>
					</div>,
					document.body
				)}
		</Fragment>
	);
}

if (duplicateEnabled && registerPlugin) {
	registerPlugin("kwtsk-duplicate-post", {
		render: DuplicatePostButton,
	});
}
