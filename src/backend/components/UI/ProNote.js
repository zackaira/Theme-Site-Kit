const { __ } = wp.i18n;

const ProNote = (props) => {
	return (
		<div className={`kwtsk-pronote ${props.nomargin ? "nomargin" : ""}`}>
			{props.title && <h6>{props.title}:</h6>}

			{props.desc && <p>{props.desc}</p>}

			{props.upgradeUrl && (
				<div className="kwtsk-pronote-a">
					<a href={props.upgradeUrl} className="pronote-btn" target="_blank">
						{__("Purchase Pro", "theme-site-kit")}
					</a>
				</div>
			)}
		</div>
	);
};

export default ProNote;
