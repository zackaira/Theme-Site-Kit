import { kwtskConvertToSlug } from "../../helpers";

const InputText = (props) => {
	const inputTitleSlug = kwtskConvertToSlug(props.slug)
		? kwtskConvertToSlug(props.slug)
		: kwtskConvertToSlug(props.title);

	const prefix = props.prefix ? props.prefix : "";
	const suffix = props.suffix ? props.suffix : "";

	return (
		<>
			{prefix && <span className="prefix">{prefix}</span>}
			{props.inputType === "text" ? (
				<input
					type="text"
					id={inputTitleSlug}
					name={inputTitleSlug}
					placeholder={props.placeholder}
					value={props.value || ""}
					onChange={props.onChange}
					className={`regular-text ${props.className || ""}`}
					{...(props.disabled && { disabled: true })}
				/>
			) : props.inputType === "number" ? (
				<input
					type="number"
					id={inputTitleSlug}
					name={inputTitleSlug}
					placeholder={props.placeholder}
					value={props.value || ""}
					onChange={props.onChange}
					className="small-text"
				/>
			) : (
				<textarea
					id={inputTitleSlug}
					name={inputTitleSlug}
					value={props.value || ""}
					placeholder={props.placeholder}
					onChange={props.onChange}
					className="regular-text"
					rows="5"
				></textarea>
			)}
			{suffix && <span className="suffix">{suffix}</span>}
		</>
	);
};

export default InputText;
