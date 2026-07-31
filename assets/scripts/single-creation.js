/**
 * Single creation: colo-grid lightbox and wide-image detection.
 *
 * Adapted from the shared inline script of
 * sliceofcactus-astro/src/pages/dessin/[id].astro and coloriage/[id].astro.
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

		cards.forEach((card) => {
			const image = card.querySelector('.colo-card__img img');

			if (!image) {
				return;
			}

			const markOrientation = () => {
				if (image.naturalWidth > image.naturalHeight * 1.05) {
					card.classList.add('colo-card--wide');
				}
			};

			if (image.complete && image.naturalWidth) {
				markOrientation();
			} else {
				image.addEventListener('load', markOrientation, { once: true });
			}
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
