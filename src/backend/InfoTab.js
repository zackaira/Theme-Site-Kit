const { __ } = wp.i18n;

const InfoTab = ({ adminUrl, isPro }) => {
	return (
		<div className="kwtskInfoTab">
			<div className="kwtsk-header addspace">
				<h3 className="kwtsk-title">
					{__("Welcome to Linkt!", "theme-site-kit")}
				</h3>
				<p>
					{__(
						"Theme Site Kit is designed to be intuitive, with helpful hints provided throughout the settings. For a more comprehensive understanding, you can also watch this video tutorial on how the Theme Site Kit plugin works. Enjoy!",
						"theme-site-kit",
					)}
				</p>

				<a
					href="https://zackaira.com/wordpress-plugins/kwtsk-url-tracking-wordpress-plugin/"
					target="_blank"
					className="kwtsk-button"
				>
					{__("Visit the Plugin Page", "theme-site-kit")}
				</a>
			</div>

			<div className="kwtsk-video addspace kwtsk-hide">
				<h3 className="kwtsk-title">
					{__(
						"Watch our video on using the Theme Site Kit plugin",
						"theme-site-kit",
					)}
				</h3>

				<a
					href="https://www.youtube.com/watch?v=4fCIDCcDgaU"
					target="_blank"
					className="kwtsk-button primary"
				>
					{__("Watch Theme Site Kit Video", "theme-site-kit")}
				</a>
			</div>

			<div className="kwtsk-help">
				<h4 className="kwtsk-title">
					{__("Support & Documentation", "theme-site-kit")}
				</h4>

				<p>
					{__(
						"Please watch the video on setting up and using Linkt, or contact me if you need help with anything regarding the plugin.",
						"theme-site-kit",
					)}
				</p>

				<a
					href="https://zackaira.com/wordpress-plugins/kwtsk-url-tracking-wordpress-plugin/#faqs"
					target="_blank"
					className="kwtsk-button"
				>
					{__("FAQ's", "theme-site-kit")}
				</a>
				<a href="mailto:z@ckaira.com" target="_blank" className="kwtsk-button">
					{__("Email z@ckaira.com", "theme-site-kit")}
				</a>
			</div>
		</div>
	);
};

export default InfoTab;
