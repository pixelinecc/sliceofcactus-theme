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

	const initPalette = (root) => {
		root.querySelectorAll('.about-palette__swatch').forEach((swatch) => {
			const rgb = getComputedStyle(swatch).backgroundColor.match(/[\d.]+/g);

			if (!rgb || rgb.length < 3) {
				return;
			}

			const [r, g, b] = rgb.map(Number);
			const hex = `#${[r, g, b].map((c) => c.toString(16).padStart(2, '0')).join('')}`.toUpperCase();
			const hexEl = swatch.querySelector('[data-hex]');

			if (hexEl) {
				hexEl.textContent = hex;
			}

			// Perceived luminance (WCAG-ish): picks readable text without
			// needing a hardcoded light/dark list per accent.
			const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
			swatch.classList.add(luminance > 0.6 ? 'about-palette__swatch--on-light' : 'about-palette__swatch--on-dark');
		});
	};

	document.querySelectorAll('[data-palette]').forEach(initPalette);
})();
