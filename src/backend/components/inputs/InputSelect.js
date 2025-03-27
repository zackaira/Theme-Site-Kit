import { kwtskConvertToSlug } from "../../helpers";

const InputSelect = (props) => {
	const selectTitleSlug = kwtskConvertToSlug(props.slug)
		? kwtskConvertToSlug(props.slug)
		: kwtskConvertToSlug(props.title);
	const selectOptions = props.options;

	return (
		<>
			<select
				id={selectTitleSlug}
				name={selectTitleSlug}
				onChange={props.onChange}
				value={props.value || props.defaultValue}
				className="snSelect"
			>
				{Object.entries(selectOptions).map(([key, value]) => (
					<option value={key} key={key}>
						{value}
					</option>
				))}
			</select>
		</>
	);
};

export default InputSelect;
