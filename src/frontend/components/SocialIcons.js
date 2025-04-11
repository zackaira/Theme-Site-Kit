const { __ } = wp.i18n;

const SocialIcons = ({ options, isPro }) => {
	const {
		enabled,
		icons,
		offset,
		position,
		style,
		spacing,
		iconbgcolor,
		iconcolor,
		iconsize,
		showbg,
		showtext,
		iconorigcolor,
	} = options;

	if (!enabled || icons.length < 1) return;

	const positionMap = {
		"right-bottom": "bottom",
		"left-bottom": "bottom",
		"bottom-center": "bottom",
		"bottom-left": "left",
		"bottom-right": "right",
	};
	const offsetFrom = positionMap[position] || "top";

	const displayedIcons = isPro ? icons : icons.slice(0, 3);

	return (
		<div
			className={`kwtsk-social-icons ${showbg ? "hasbg" : "nobg"} kwtsk-si-${position} kwtsk-si-${style} kwtsk-si-${iconsize} ${isPro && iconorigcolor ? "kwtsk-colorig" : ""}`}
			style={{
				[offsetFrom]: `${position === "bottom-center" ? "10px" : `${offset}px`}`,
				gap: `${spacing}px`,
			}}
		>
			{displayedIcons.map((icon, index) => {
				const { value, label, url } = icon;
				return (
					<div className="kwtsk-social-icon" key={index}>
						<a
							href={value === "email" ? `mailto:${url}` : url}
							{...(value !== "email" || value !== "phone"
								? { target: "_blank" }
								: {})}
							rel="noopener noreferrer"
							className="kwtsk-social-icon-a"
							style={{
								backgroundColor: iconbgcolor,
								color: iconcolor,
							}}
							{...(!showtext ? { title: label } : {})}
						>
							<i className={`kwtsk-icon ${value}`}></i>
						</a>

						{showtext && (
							<div
								className="kwtsk-social-icon-label"
								style={{
									backgroundColor: iconbgcolor,
									color: iconcolor,
								}}
							>
								<div
									className="corner"
									style={{
										borderColor: iconbgcolor,
									}}
								></div>
								{label}
							</div>
						)}
					</div>
				);
			})}
		</div>
	);
};

export default SocialIcons;
