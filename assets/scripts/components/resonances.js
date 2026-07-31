/**
 * Résonances rows: prev/next scroll buttons for each row's track.
 *
 * Shared by page-resonances.php (rows mixing Photo/Création/Récit by term)
 * and taxonomy-resonance.php (rows split by post type within one term).
 * Same scrollBy() pattern as the lightbox filmstrip
 * (assets/scripts/components/lightbox.js) — no library, native horizontal
 * scroll with scroll-snap in the CSS handles the rest.
 */

(() => {
	'use strict';

	const buttons = document.querySelectorAll('.resonance-row__nav-btn');

	if (!buttons.length) {
		return;
	}

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	buttons.forEach((button) => {
		const track = document.getElementById(button.dataset.track);

		if (!track) {
			return;
		}

		const direction = button.classList.contains('resonance-row__nav-btn--prev') ? -1 : 1;

		button.addEventListener('click', () => {
			track.scrollBy({
				left: direction * track.clientWidth * 0.8,
				behavior: reduceMotion ? 'auto' : 'smooth',
			});
		});
	});
})();
