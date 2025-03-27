const Heading = ({ title, description, nomargin }) => {
	return (
		<div className={`kwtskheading ${nomargin ? "nomargin" : ""}`}>
			{title && <h4 className="kwtskheading-title">{title}</h4>}
			{description && <p className="kwtskheading-desc">{description}</p>}
		</div>
	);
};

export default Heading;
