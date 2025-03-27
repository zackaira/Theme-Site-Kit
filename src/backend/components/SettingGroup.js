const SettingGroup = ({ label, children }) => {
	return (
		<tr className="kwtsk-row kwtsk-group-row">
			<th>{label}</th>
			<td>
				<div className="kwtsk-group">
					<a className="kwtsk-group-btn">
						<span className="dashicons dashicons-edit"></span>
					</a>
					<div className="kwtsk-group-container">
						<table className="form-table" role="presentation">
							<tbody>{children}</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
	);
};

export default SettingGroup;
