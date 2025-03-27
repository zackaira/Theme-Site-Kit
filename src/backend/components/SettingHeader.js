const SettingHeader = ({ title, description }) => {
	return (
		<div className="kwtsk-header">
			{title && <h3 className="kwtsk-title">{title}</h3>}
			{description && <p>{description}</p>}
		</div>
	);
};

export default SettingHeader;
