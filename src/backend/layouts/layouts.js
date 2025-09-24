const { createRoot, render } = wp.element;
import LayoutsPage from "./LayoutsPage";
import "./layouts.css";

document.addEventListener("DOMContentLoaded", function () {
	const kwtskObj = kwtskLObj;
	const element = document.getElementById("kwtsk-layouts-root");
	const canSvg = Boolean(kwtskObj.canSvg);

	if (element) {
		if (createRoot) {
			const root = createRoot(element);
			root.render(<LayoutsPage kwtskObj={kwtskObj} svgOn={canSvg} />);
		} else {
			render(<LayoutsPage kwtskObj={kwtskObj} svgOn={canSvg} />, element);
		}
	}
});
