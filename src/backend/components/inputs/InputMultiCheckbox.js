const { useState } = wp.element;
const { __ } = wp.i18n;
import { kwtskConvertToSlug } from "../../helpers";

const InputMultiCheckbox = ({ slug, value, onChange, options = [] }) => {
	const inputName = slug;
	// Ensure the initial state is an array
	const [selectedValues, setSelectedValues] = useState(
		Array.isArray(value) ? value : [],
	);

	// If options is an object (like your user roles), convert it to an array of { value, label }
	const formattedOptions = Array.isArray(options)
		? options
		: Object.entries(options).map(([key, label]) => ({ value: key, label }));

	// Handle checkbox state change
	const handleCheckboxChange = (optionValue, checked) => {
		let updatedValues;
		if (checked) {
			// Add the value if not already selected
			updatedValues = [...selectedValues, optionValue];
		} else {
			// Remove the value from the selected values
			updatedValues = selectedValues.filter((val) => val !== optionValue);
		}
		setSelectedValues(updatedValues);

		// Create a custom event containing the updated array of selected roles
		const customEvent = {
			target: {
				type: "checkbox",
				name: inputName,
				value: updatedValues,
				checked: checked,
				className: "checkbox-input",
			},
		};
		onChange(customEvent);
	};

	return (
		<>
			{formattedOptions.map((opt) => {
				const inputId = kwtskConvertToSlug(`${inputName}-${opt.value}`);
				const isChecked = selectedValues.includes(opt.value);

				return (
					<div key={opt.value} className="kwtsk-checkboxes-block">
						<label
							className={`kwtsk-checkboxes-label ${isChecked ? "selected" : ""}`}
							htmlFor={inputId}
						>
							<input
								id={inputId}
								name={inputName}
								type="checkbox"
								value={opt.value}
								checked={isChecked}
								onChange={(e) =>
									handleCheckboxChange(opt.value, e.target.checked)
								}
								className="checkboxes-input"
							/>
							<span className="checkboxes-label">{opt.label}</span>
						</label>
					</div>
				);
			})}
		</>
	);
};

export default InputMultiCheckbox;
