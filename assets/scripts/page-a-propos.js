/**
 * Page À propos: swaps the sticky visual and big number of "la démarche"
 * to match whichever .about-sequence-item is currently in view.
 */
(() => {
	'use strict';

	const initSequence = (root) => {
		const visual = root.querySelector('[data-visual]');
		const big = root.querySelector('[data-big]');
		const items = [...root.querySelectorAll('.about-sequence-item')];

		if (!visual || !big || !items.length) {
			return;
		}

		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
					if (entry.target.dataset.bg) {
						visual.style.background = entry.target.dataset.bg;
					}
					if (entry.target.dataset.val) {
						big.textContent = entry.target.dataset.val;
					}
				}
			});
		}, { threshold: [0.5], rootMargin: '-20% 0px -20% 0px' });

		items.forEach((item) => observer.observe(item));
	};

	document.querySelectorAll('.about-sequence').forEach(initSequence);
})();
