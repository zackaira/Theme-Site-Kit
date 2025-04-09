const { __ } = wp.i18n;

/*
 * Convert Text to slug
 */
export const kwtskConvertToSlug = (text, spacer = "_") => {
	const safeSpacer = spacer === "-" ? "-" : "_"; // Allow only "-" or "_"
	return text
		.toLowerCase()
		.trim()
		.replace(/[^\w\s-]/g, "") // Allow letters, numbers, spaces, underscores, hyphens
		.replace(/\s+/g, safeSpacer) // Replace spaces with the spacer
		.replace(new RegExp(`${safeSpacer}{2,}`, "g"), safeSpacer) // Collapse multiple spacers
		.replace(new RegExp(`^${safeSpacer}|${safeSpacer}$`, "g"), ""); // Trim leading/trailing spacers
};

export const kwtskCapitalizeWords = (str) => {
	return str.replace(/\b\w/g, (match) => match.toUpperCase());
};

export const kwtskGroupSettings = () => {
	const groupBtns = document.querySelectorAll(".kwtsk-group-btn");

	if (groupBtns) {
		groupBtns.forEach((btn) => {
			btn.addEventListener("click", () => {
				const btnParent = btn.parentElement;

				groupBtns.forEach((btnItem) => {
					btnItem.parentElement.removeAttribute("id", "openGroup");
					btnItem.parentElement.classList.remove("kwtsk-show");
				});

				// Add / Remove .kwtsk-show class
				if (btnParent.classList.contains("kwtsk-show")) {
					btnParent.removeAttribute("id", "openGroup");
					btnParent.classList.remove("kwtsk-show");
				} else {
					btnParent.setAttribute("id", "openGroup");
					btnParent.classList.add("kwtsk-show");
				}
			});
		});
	}

	// Close on click outside
	window.addEventListener("click", function (e) {
		const openGroup = document.getElementById("openGroup");

		if (openGroup) {
			if (!e.target == openGroup || !openGroup.contains(e.target)) {
				openGroup.removeAttribute("id");
				openGroup.classList.remove("kwtsk-show");
			}
		}
	});
};

export const kwtskSocialOptions = [
	{ value: "email", label: __("Email", "theme-site-kit") },
	{ value: "phone", label: __("Phone", "theme-site-kit") },
	{ value: "whatsapp", label: __("WhatsApp", "theme-site-kit") },
	{
		value: "telegram",
		label: __("Telegram", "theme-site-kit"),
	},
	{
		value: "line",
		label: __("Line", "theme-site-kit"),
	},
	{
		value: "facebook",
		label: __("Facebook", "theme-site-kit"),
	},
	{
		value: "x-twitter",
		label: __("X", "theme-site-kit"),
	},
	{
		value: "instagram",
		label: __("Instagram", "theme-site-kit"),
	},
	{
		value: "linkedin",
		label: __("LinkedIn", "theme-site-kit"),
	},
	{
		value: "pinterest",
		label: __("Pinterest", "theme-site-kit"),
	},
	{
		value: "tiktok",
		label: __("TikTok", "theme-site-kit"),
	},
	{
		value: "bluesky",
		label: __("Bluesky", "theme-site-kit"),
	},
	{
		value: "youtube",
		label: __("YouTube", "theme-site-kit"),
	},
	{
		value: "vimeo",
		label: __("Vimeo", "theme-site-kit"),
	},
	{
		value: "wordpress",
		label: __("WordPress", "theme-site-kit"),
	},
	{
		value: "tumblr",
		label: __("Tumblr", "theme-site-kit"),
	},
	{
		value: "dribbble",
		label: __("Dribbble", "theme-site-kit"),
	},
	{
		value: "airbnb",
		label: __("Airbnb", "theme-site-kit"),
	},
	{
		value: "behance",
		label: __("Behance", "theme-site-kit"),
	},
	{
		value: "product-hunt",
		label: __("Product Hunt", "theme-site-kit"),
	},
	{
		value: "twitch",
		label: __("Twitch", "theme-site-kit"),
	},
	{
		value: "snapchat",
		label: __("SnapChat", "theme-site-kit"),
	},
	{
		value: "figma",
		label: __("Figma", "theme-site-kit"),
	},
	{
		value: "spotify",
		label: __("Spotify", "theme-site-kit"),
	},
	{
		value: "soundcloud",
		label: __("SoundCloud", "theme-site-kit"),
	},
	{
		value: "deezer",
		label: __("Deezer", "theme-site-kit"),
	},
	{
		value: "meetup",
		label: __("Meetup", "theme-site-kit"),
	},
	{
		value: "etsy",
		label: __("Etsy", "theme-site-kit"),
	},
	{
		value: "paypal",
		label: __("PayPal", "theme-site-kit"),
	},
	{
		value: "skype",
		label: __("Skype", "theme-site-kit"),
	},
	{
		value: "reddit-alien",
		label: __("TikTok", "theme-site-kit"),
	},
	{
		value: "mix",
		label: __("Mix", "theme-site-kit"),
	},
	{
		value: "hubspot",
		label: __("HubSpot", "theme-site-kit"),
	},
	{
		value: "strava",
		label: __("Strava", "theme-site-kit"),
	},
	{
		value: "foursquare",
		label: __("Foursquare", "theme-site-kit"),
	},
	{
		value: "github-alt",
		label: __("Github", "theme-site-kit"),
	},
	{
		value: "audible",
		label: __("Audible", "theme-site-kit"),
	},
	{
		value: "aws",
		label: __("AWS", "theme-site-kit"),
	},
	{
		value: "kickstarter-k",
		label: __("Kickstarter", "theme-site-kit"),
	},
	{
		value: "flickr",
		label: __("Flickr", "theme-site-kit"),
	},
	{
		value: "deviantart",
		label: __("DeviantArt", "theme-site-kit"),
	},
	{
		value: "500px",
		label: __("500px", "theme-site-kit"),
	},
	{
		value: "btc",
		label: __("Bitcoin", "theme-site-kit"),
	},
	{
		value: "ethereum",
		label: __("Ethereum", "theme-site-kit"),
	},
];
