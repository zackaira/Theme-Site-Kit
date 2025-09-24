const SettingGroup = ({ label, children, groupClass = "", note = "" }) => {
	return (
		<tr className="kwtsk-row kwtsk-group-row">
			<th>{label}</th>
			<td>
				<div className={`kwtsk-group ${groupClass}`}>
					<a className="kwtsk-group-btn">
						<span className="dashicons dashicons-edit"></span>
					</a>
					{note && <p className="setting-note">{note}</p>}
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
