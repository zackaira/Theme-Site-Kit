const { createRoot } = wp.element;
import SettingsPage from "./backend/SettingsPage";
import "./backend/settings.css";

document.addEventListener("DOMContentLoaded", function () {
	const kwtskObj = kwtskSObj;
	const element = document.getElementById("kwtsk-root");

	if (element) {
		const root = createRoot(element);
		root.render(<SettingsPage kwtskObj={kwtskObj} />);
	}
});
