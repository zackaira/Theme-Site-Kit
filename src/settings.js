const { render } = wp.element;
import SettingsPage from "./backend/SettingsPage";
import "./backend/settings.css";

document.addEventListener("DOMContentLoaded", function () {
	const kwtskObj = kwtskSObj;
	const element = document.getElementById("kwtsk-root");

	if (element) {
		render(<SettingsPage kwtskObj={kwtskObj} />, element);
	}
});
