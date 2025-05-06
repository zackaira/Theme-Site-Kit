const { __ } = wp.i18n;

const GeneralPage = ({ adminUrl, changeTab }) => {
	const question = document.querySelector(".ask-feedback");

	return (
		<div className="kwtsk-general-page">
			<h2>{__("Welcome to Theme Site Kit", "theme-site-kit")}</h2>
			<p>
				{__(
					"Your new secret weapon for cleaner, smoother, and just better working WordPress websites.",
					"theme-site-kit",
				)}
			</p>
			<p>
				{__(
					"Offering a range of useful features, Theme Site Kit is designed to help you take your WordPress site to the next level.",
					"theme-site-kit",
				)}
			</p>
			<p>
				{__(
					"And the best part? You can pick and choose which features you want to use, so you can keep your site as light and fast as possible.",
					"theme-site-kit",
				)}
			</p>

			<div className="kwtsk-gp-features">
				<div className="kwtsk-gp-feature">
					<div className="kwtsk-gp-feature-img settings"></div>
					<h3>{__("Site Kit Settings", "theme-site-kit")}</h3>
					<p>
						{__(
							"This plugin includes a range of helpful tools to improve your site, like disabling comments, maintenance mode while building, safely upload SVG formats, floating contact and social links, and more. Head to the top tabs in the plugin settings to explore each feature and adjust the options to suit your website.",
							"theme-site-kit",
						)}
					</p>
					<span
						className="kwtsk-gp-a"
						onClick={() => changeTab("disable_comments")}
					>
						{__("Go to next tab(s)", "theme-site-kit")}
					</span>
				</div>
				<div className="kwtsk-gp-feature">
					<div className="kwtsk-gp-feature-img layouts"></div>
					<h3>{__("Import Page Layouts", "theme-site-kit")}</h3>
					<p>
						{__(
							"Get a head start on your design with a set of pre-built page layouts you can import in one click. Whether you're building a homepage, about page, or contact section and more, browse through the page layouts to preview and import the layouts that best match your site's structure and branding.",
							"theme-site-kit",
						)}
					</p>
					<a
						className="kwtsk-gp-a"
						target="_blank"
						href={`${adminUrl}themes.php?page=theme-site-kit-layouts`}
					>
						{__("View Page Layouts", "theme-site-kit")}
					</a>
				</div>
				<div className="kwtsk-gp-feature">
					<div className="kwtsk-gp-feature-img patterns"></div>
					<h3>{__("Block Patterns", "theme-site-kit")}</h3>
					<p>
						{__(
							"Quickly build stylish sections using custom WordPress patterns designed for speed and simplicity. You'll find them in the core editor under the Patterns tab, just browse through them or search by name, then insert them directly into your page to create beautiful, consistent content a matter of minutes.",
							"theme-site-kit",
						)}
					</p>
					<a
						className="kwtsk-gp-a"
						target="_blank"
						href="https://kairaweb.com/wordpress-plugin/theme-site-kit/"
					>
						{__("View Block Patterns", "theme-site-kit")}
					</a>
				</div>
			</div>

			<p>
				{__(
					"Whether you're a seasoned pro or just starting out, Theme Site Kit is designed to be easy to use.",
					"theme-site-kit",
				)}
			</p>
			<p>
				{__(
					"So go ahead, take a look around and see what you can do with Theme Site Kit.",
					"theme-site-kit",
				)}
			</p>

			<br />
			<br />

			<h2>{__("Discover Our Other Plugins", "theme-site-kit")}</h2>
			<p>
				{__(
					"Explore our other plugins to further enhance your WordPress experience.",
					"theme-site-kit",
				)}
			</p>
			<div className="kwtsk-gp-plugins">
				<a
					className="kwtsk-gp-plugin"
					href="https://kairaweb.com/wordpress-plugin/storecustomizer/"
					target="_blank"
				>
					<div className="kwtsk-gp-plugin-img storcustomizer"></div>
					<h3>{__("StoreCustomizer", "theme-site-kit")}</h3>
					<p>
						{__(
							"Customize your WooCommerce store pages with ease.",
							"theme-site-kit",
						)}
					</p>
					<span className="kwtsk-gp-plugin-a">
						{__("View Plugin", "theme-site-kit")}
					</span>
				</a>
				<a
					className="kwtsk-gp-plugin"
					href="https://kairaweb.com/wordpress-plugin/blockons/"
					target="_blank"
				>
					<div className="kwtsk-gp-plugin-img blockons"></div>
					<h3>{__("Blockons", "theme-site-kit")}</h3>
					<p>
						{__(
							"Advanced blocks and Site Addons for your website.",
							"theme-site-kit",
						)}
					</p>
					<span className="kwtsk-gp-plugin-a">
						{__("View Plugin", "theme-site-kit")}
					</span>
				</a>
				<a
					className="kwtsk-gp-plugin"
					href="https://kairaweb.com/wordpress-plugin/linkt/"
					target="_blank"
				>
					<div className="kwtsk-gp-plugin-img linkt"></div>
					<h3>{__("Linkt", "theme-site-kit")}</h3>
					<p>
						{__(
							"Affiliate Links and Custom Link Tracking & Management.",
							"theme-site-kit",
						)}
					</p>
					<span className="kwtsk-gp-plugin-a">
						{__("View Plugin", "theme-site-kit")}
					</span>
				</a>
			</div>
		</div>
	);
};

export default GeneralPage;
