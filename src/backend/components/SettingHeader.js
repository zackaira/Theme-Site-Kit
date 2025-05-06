const { __ } = wp.i18n;

const SettingHeader = ({ title, description, isPro, isPremium }) => {
	return (
		<div className="kwtsk-header">
			{title && (
				<h3 className="kwtsk-title">
					{title}{" "}
					{isPro && !isPremium && (
						<span className="probanner">
							{__("Premium Feature", "theme-site-kit")}
						</span>
					)}
				</h3>
			)}
			{description && <p>{description}</p>}
		</div>
	);
};

export default SettingHeader;
