/**
 * Single creation: colo-grid lightbox.
 *
 * Adapted from the shared inline script of
 * sliceofcactus-astro/src/pages/dessin/[id].astro and coloriage/[id].astro.
 * Wide-image detection (.colo-card--wide) is computed server-side from the
 * attachment's own metadata (template-parts/single/creation-contact-sheet.php)
 * instead of measured here after image load, to avoid a layout shift.
 */

(() => {
	'use strict';

	const grids = document.querySelectorAll('.colo-grid[data-lightbox]');

	if (!grids.length) {
		return;
	}

	grids.forEach((grid) => {
		const cards = Array.from(grid.querySelectorAll('.colo-card'));
		const lightbox = document.getElementById(grid.dataset.lightbox);

		if (!cards.length || !lightbox) {
			return;
		}

		const items = cards.map((card, index) => {
			const cap = card.querySelector('.colo-card__title')?.textContent || '';

			return {
				src: card.getAttribute('href') || '',
				alt: cap,
				caption: `${cap} · ${index + 1} / ${cards.length}`,
			};
		});

		const controller = window.SocLightbox.create({
			lightbox,
			image: lightbox.querySelector('.lightbox__fig img'),
			caption: lightbox.querySelector('.lightbox__fig figcaption'),
			closeButton: lightbox.querySelector('.lightbox__close'),
			prevButton: lightbox.querySelector('.lightbox__nav--prev'),
			nextButton: lightbox.querySelector('.lightbox__nav--next'),
			getItems: () => items,
			strip: {
				track: lightbox.querySelector('.lightbox__strip'),
				buttons: Array.from(lightbox.querySelectorAll('.lightbox__strip__item')),
				prevButton: lightbox.querySelector('.lightbox__strip-nav--prev'),
				nextButton: lightbox.querySelector('.lightbox__strip-nav--next'),
			},
		});

		if (!controller) {
			return;
		}

		cards.forEach((card, index) => {
			card.addEventListener('click', (event) => {
				event.preventDefault();
				controller.open(index);
			});
		});
	});
})();
