const { __ } = wp.i18n;
const { Button } = wp.components;

const LayoutItem = ({
	layout,
	importLayout,
	handlePreviewLayout,
	isImporting,
	importedPageId,
	isPremium,
	upgradeUrl,
	adminUrl,
}) => {
	return (
		<div className="kwtsk-layout">
			<div className="kwtsk-layout-img">
				<img src={layout.previewImage} alt={layout.title} />
			</div>
			<div className="kwtsk-layout-content">
				<h3>{layout.title}</h3>
				<p>{layout.description}</p>
				<div className="kwtsk-layout-btns">
					{isPremium ? (
						<>
							{importedPageId ? (
								<a
									href={`${adminUrl}post.php?post=${importedPageId}&action=edit&editor-mode=visual`}
									className="kwtsk-btn"
									target="_blank"
									rel="noopener noreferrer"
								>
									{__("Edit Page", "theme-site-kit")}
								</a>
							) : (
								<Button
									onClick={() => importLayout(layout)}
									className={`kwtsk-btn ${isImporting ? "importing" : ""}`}
									disabled={isImporting}
								>
									{__("Import Layout", "theme-site-kit")}
								</Button>
							)}
						</>
					) : (
						<a href={upgradeUrl} className="kwtsk-btn">
							{__("Upgrade to Pro", "theme-site-kit")}
						</a>
					)}

					<Button
						onClick={() => handlePreviewLayout(layout)}
						className="kwtsk-btn"
					>
						{__("Preview Layout", "your-textdomain")}
					</Button>
				</div>
			</div>
		</div>
	);
};

export default LayoutItem;
