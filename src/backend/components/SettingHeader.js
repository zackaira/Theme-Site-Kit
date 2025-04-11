const { __ } = wp.i18n;

const SettingHeader = ({ title, description, ispro }) => {
	return (
		<div className="kwtsk-header">
			{title && (
				<h3 className="kwtsk-title">
					{title}{" "}
					{ispro ? (
						<span className="probanner">
							{__("Premium Feature", "theme-site-kit")}
						</span>
					) : (
						""
					)}
				</h3>
			)}
			{description && <p>{description}</p>}
		</div>
	);
};

export default SettingHeader;
