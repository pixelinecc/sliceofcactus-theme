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

		const lightboxImage = lightbox.querySelector('.lightbox__fig img');
		const lightboxCaption = lightbox.querySelector('.lightbox__fig figcaption');
		const closeButton = lightbox.querySelector('.lightbox__close');
		const previousButton = lightbox.querySelector('.lightbox__nav--prev');
		const nextButton = lightbox.querySelector('.lightbox__nav--next');
		const focusableControls = [closeButton, previousButton, nextButton].filter(Boolean);
		let currentIndex = 0;
		let previouslyFocused = null;

		const items = cards.map((card) => ({
			src: card.getAttribute('href') || '',
			cap: card.querySelector('.colo-card__title')?.textContent || '',
		}));

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

		const show = (requestedIndex) => {
			currentIndex = (requestedIndex + items.length) % items.length;
			lightboxImage.src = items[currentIndex].src;
			lightboxImage.alt = items[currentIndex].cap;
			lightboxCaption.textContent = `${items[currentIndex].cap} · ${currentIndex + 1} / ${items.length}`;
		};

		const open = (index) => {
			previouslyFocused = document.activeElement;
			show(index);
			lightbox.classList.add('is-open');
			lightbox.setAttribute('aria-hidden', 'false');
			closeButton?.focus();
		};

		const close = () => {
			lightbox.classList.remove('is-open');
			lightbox.setAttribute('aria-hidden', 'true');
			lightboxImage.removeAttribute('src');

			if (previouslyFocused instanceof HTMLElement) {
				previouslyFocused.focus();
			}
		};

		cards.forEach((card, index) => {
			card.addEventListener('click', (event) => {
				event.preventDefault();
				open(index);
			});
		});

		closeButton?.addEventListener('click', close);
		previousButton?.addEventListener('click', () => show(currentIndex - 1));
		nextButton?.addEventListener('click', () => show(currentIndex + 1));
		lightbox.addEventListener('click', (event) => {
			if (event.target === lightbox) {
				close();
			}
		});

		document.addEventListener('keydown', (event) => {
			if (!lightbox.classList.contains('is-open')) {
				return;
			}

			if (event.key === 'Escape') {
				close();
			} else if (event.key === 'ArrowLeft') {
				show(currentIndex - 1);
			} else if (event.key === 'ArrowRight') {
				show(currentIndex + 1);
			} else if (event.key === 'Tab' && focusableControls.length) {
				const firstControl = focusableControls[0];
				const lastControl = focusableControls[focusableControls.length - 1];

				if (event.shiftKey && document.activeElement === firstControl) {
					event.preventDefault();
					lastControl.focus();
				} else if (!event.shiftKey && document.activeElement === lastControl) {
					event.preventDefault();
					firstControl.focus();
				}
			}
		});
	});
})();