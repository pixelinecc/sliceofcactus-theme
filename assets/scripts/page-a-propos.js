/**
 * Page À propos — reads each [data-fill] element's own computed background
 * and picks a readable text color for it: "La palette" swatches (hex label
 * included) and "Des chemins de traverse"'s crossing words (background
 * only, no hex). The values only live in CSS (--accent-* custom
 * properties, assets/styles/settings/tokens.css), so this can't be
 * resolved server-side.
 */
(() => {
	'use strict';

	const initPalette = (root) => {
		root.querySelectorAll('[data-fill]').forEach((swatch) => {
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
