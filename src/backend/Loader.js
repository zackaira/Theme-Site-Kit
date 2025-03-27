const Loader = ({ height, width }) => {
	const setHeight = height ? height : false;
	const setWidth = width ? width : false;

	return (
		<div className="kwtsk-loader-wrap">
			<div
				className="kwtsk-loader"
				{...(setHeight || setWidth
					? {
							style: {
								...(setHeight ? { height: setHeight } : {}),
								...(setWidth ? { width: setWidth } : {}),
							},
						}
					: {})}
			>
				<div className="kwtsk-spinner-text"></div>
				<div className="kwtsk-loader-sector kwtsk-loader-sector-blue"></div>
				<div className="kwtsk-loader-sector kwtsk-loader-sector-pink"></div>
				<div className="kwtsk-loader-sector kwtsk-loader-sector-purple"></div>
			</div>
		</div>
	);
};

export default Loader;
