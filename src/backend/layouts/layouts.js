const { render } = wp.element;
import LayoutsPage from "./LayoutsPage";
import "./layouts.css";

document.addEventListener("DOMContentLoaded", function () {
	const kwtskObj = kwtskLObj;
	const element = document.getElementById("kwtsk-layouts-root");
	const canSvg = Boolean(kwtskObj.canSvg);

	if (element) {
		render(<LayoutsPage kwtskObj={kwtskObj} svgOn={canSvg} />, element);
	}
});
