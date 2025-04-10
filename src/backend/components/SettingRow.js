const { __ } = wp.i18n;
import InputToggleSwitch from "./inputs/InputToggleSwitch";
import InputSelect from "./inputs/InputSelect";
import InputText from "./inputs/InputText";
import InputRange from "./inputs/InputRange";
import InputMultiCheckbox from "./inputs/InputMultiCheckbox";
import ColorPicker from "./inputs/ColorPicker";
import InputRadioSelect from "./inputs/InputRadioSelect";
import SocialIconSelector from "./inputs/SocialIconSelector";
import CptsSelect from "./inputs/CptsSelect";
import SettingTooltip from "./UI/SettingTooltip";
import ProNote from "./UI/ProNote";
import { kwtskConvertToSlug } from "../helpers";

import Heading from "./UI/Heading";

const SettingRow = (props) => {
	const theTitleSlug = props.slug
		? kwtskConvertToSlug(props.slug)
		: kwtskConvertToSlug(props.title);

	let theInput;
	if (props.inputType === "toggle") {
		theInput = <InputToggleSwitch {...props} />;
	} else if (props.inputType === "onlytoggle") {
		return <InputToggleSwitch {...props} />;
	} else if (props.inputType === "select") {
		theInput = <InputSelect {...props} />;
	} else if (props.inputType === "radio") {
		theInput = <InputRadioSelect {...props} />;
	} else if (props.inputType === "multicheckbox") {
		theInput = <InputMultiCheckbox {...props} />;
	} else if (props.inputType === "socialicons") {
		return (
			<tr>
				<td colSpan={2}>
					<div className={props.customClass}>
						<SocialIconSelector {...props} />
					</div>
				</td>
			</tr>
		);
	} else if (props.inputType === "cptsoptions") {
		return (
			<tr>
				<td colSpan={2}>
					<div>
						<CptsSelect {...props} />
					</div>
				</td>
			</tr>
		);
	} else if (props.inputType === "range") {
		theInput = <InputRange {...props} />;
	} else if (props.inputType === "colorpicker") {
		theInput = <ColorPicker {...props} />;
	} else if (props.inputType === "heading") {
		return (
			<tr className="kwtsk-row heading">
				<td colSpan={2}>
					<Heading {...props} />
				</td>
			</tr>
		);
	} else if (props.inputType === "pronote") {
		return (
			<tr className="kwtsk-row pronote">
				<td colSpan={2}>
					<ProNote {...props} />
				</td>
			</tr>
		);
	} else {
		theInput = <InputText {...props} />;
	}

	return (
		<tr className="kwtsk-row">
			{!props.notitle && (
				<th scope="row">
					<label htmlFor={props.parent != "" ? theTitleSlug : props.value}>
						{props.title || " "}
					</label>
				</th>
			)}
			<td {...(props.notitle ? { colSpan: 2 } : {})}>
				<div className="kwtsk-row-cols">
					<div className={`kwtsk-row-col-left ${props.customClass || ""}`}>
						{theInput}

						{props.note ? <p className="setting-note">{props.note}</p> : ""}
						{props.standOutNote && (
							<>
								<p className="stand-out-note">
									{props.standOutNote}
									<a
										href="options-permalink.php"
										className="stand-out-note-link"
										target="_blank"
									>
										{__("Update the Permalinks", "theme-site-kit")}
									</a>
								</p>
							</>
						)}
					</div>
					<div className="kwtsk-row-col-right">
						{props.tooltip && <SettingTooltip tooltip={props.tooltip} />}

						{props.documentation && (
							<a
								href={props.documentation}
								target="_blank"
								className="kwtskdoclink"
								title={__("Documentation", "theme-site-kit")}
							></a>
						)}
					</div>
				</div>
			</td>
		</tr>
	);
};

export default SettingRow;
