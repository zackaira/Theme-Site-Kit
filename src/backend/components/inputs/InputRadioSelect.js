const { useState } = wp.element;
const { __ } = wp.i18n;
import { kwtskConvertToSlug } from "../../helpers";

const InputRadioSelect = ({ slug, value, onChange, options = [] }) => {
	const inputName = slug;
	const [selectedValue, setSelectedValue] = useState(value);

	const handleClear = () => {
		const customEvent = {
			target: {
				type: "radio",
				name: inputName,
				value: false,
				checked: false,
				className: "radio-input",
			},
		};
		setSelectedValue(null);
		onChange(customEvent);
	};

	return (
		<>
			{options.map((opt) => {
				const inputId = kwtskConvertToSlug(`${inputName}-${opt.value}`);
				const isChecked = value === opt.value;

				const handleChange = (e) => {
					const checked = e.target.checked;
					// Create a custom event with the structure expected by the parent
					const customEvent = {
						target: {
							type: "radio",
							name: inputName,
							value: opt.value,
							checked: checked,
							className: "radio-input",
						},
					};
					setSelectedValue(opt.value);
					onChange(customEvent);
				};

				return (
					<div key={opt.value} className="kwtsk-radio-block">
						<label
							className={`kwtsk-radio-select ${isChecked ? "selected" : ""}`}
							htmlFor={inputId}
						>
							<input
								id={inputId}
								name={inputName}
								type="radio"
								value={opt.value}
								checked={isChecked}
								onChange={handleChange}
								className="radio-input"
							/>
							<span className="radio-label">{opt.label}</span>
						</label>
					</div>
				);
			})}
			{/* Clear Selection link */}
			{selectedValue && (
				<div className="clear-selection" onClick={handleClear}>
					{__("Clear Selection", "theme-site-kit")}
				</div>
			)}
		</>
	);
};

export default InputRadioSelect;
