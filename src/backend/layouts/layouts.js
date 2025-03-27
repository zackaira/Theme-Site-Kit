const { render } = wp.element;
import LayoutsPage from "./LayoutsPage";
import "./layouts.css";

document.addEventListener("DOMContentLoaded", function () {
	const kwtskObj = kwtskLObj;
	const element = document.getElementById("kwtsk-layouts-root");

	if (element) {
		render(<LayoutsPage kwtskObj={kwtskObj} />, element);
	}
});
