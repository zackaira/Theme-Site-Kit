const { __ } = wp.i18n;
import { kwtskConvertToSlug } from "../../helpers";

const InputToggleSwitch = ({ slug, title, value, onChange, comingSoon }) => {
	const inputTitleSlug = kwtskConvertToSlug(slug)
		? kwtskConvertToSlug(slug)
		: kwtskConvertToSlug(title);
	const isChecked = value ? true : false;

	if (comingSoon) {
		return (
			<p className="setting-note">
				{title} {__("coming soon...", "theme-site-kit")}
			</p>
		);
	}

	return (
		<label className="toggle-switch">
			<input
				id={inputTitleSlug}
				name={inputTitleSlug}
				type="checkbox"
				onChange={onChange}
				checked={isChecked}
				className="toggle-switch-checkbox"
			/>
			<span className="toggle-switch-slider"></span>
		</label>
	);
};

export default InputToggleSwitch;
