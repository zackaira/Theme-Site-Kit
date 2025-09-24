document.addEventListener("DOMContentLoaded", function () {
	// Get scroll animation settings from localized data
	const scrollDuration =
		typeof kwtskScrollObj !== "undefined" && kwtskScrollObj.duration
			? parseInt(kwtskScrollObj.duration)
			: 800; // Default fallback duration

	// Simple smooth scroll function with configurable duration
	function smoothScrollTo(targetElement, duration) {
		const targetPosition = targetElement.offsetTop;
		const startPosition = window.pageYOffset;
		const distance = targetPosition - startPosition;
		const startTime = performance.now();

		function scrollAnimation(currentTime) {
			const elapsed = currentTime - startTime;
			const progress = Math.min(elapsed / duration, 1);

			// Simple easing function
			const easeProgress = progress * (2 - progress); // easeOutQuad

			window.scrollTo(0, startPosition + distance * easeProgress);

			if (progress < 1) {
				requestAnimationFrame(scrollAnimation);
			}
		}

		requestAnimationFrame(scrollAnimation);
	}

	// Select all anchor links that reference an ID on the page
	const scrollLinks = document.querySelectorAll('a[href^="#"]');

	scrollLinks.forEach((link) => {
		link.addEventListener("click", function (e) {
			const targetId = this.getAttribute("href").substring(1);
			const targetElement = document.getElementById(targetId);

			if (targetElement) {
				e.preventDefault(); // Stop default jump
				smoothScrollTo(targetElement, scrollDuration);
			}
		});
	});
});
