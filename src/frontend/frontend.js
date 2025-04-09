import { __ } from "@wordpress/i18n";
import "./frontend.css";
import SocialIcons from "./components/SocialIcons";

document.addEventListener("DOMContentLoaded", () => {
	// Add Social Icons
	const kwtskSocial = document.getElementById("kwtsk-social-root");

	if (typeof kwtskSocial !== undefined && kwtskSocial !== null) {
		const socialIn = document.querySelector("body");
		if (socialIn.classList.contains("kwtsk-hide-icons")) {
			setTimeout(() => socialIn.classList.remove("kwtsk-hide-icons"), 800);
		}

		ReactDOM.render(
			<SocialIcons
				options={kwtskFObj?.kwtskOptions}
				isPro={kwtskFObj?.isPremium}
			/>,
			kwtskSocial,
		);
	}
});
