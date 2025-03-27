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
				<div className="kwtsk-loader-sector kwtsk-loader-sector-one"></div>
				<div className="kwtsk-loader-sector kwtsk-loader-sector-two"></div>
				<div className="kwtsk-loader-sector kwtsk-loader-sector-three"></div>
			</div>
		</div>
	);
};

export default Loader;
