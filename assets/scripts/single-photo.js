/**
 * Contact-sheet masonry and lightbox.
 *
 * Adapted from sliceofcactus-astro/src/pages/photo/[id].astro.
 */

(() => {
	'use strict';

	const grids = document.querySelectorAll('.soc-photo-sheet__content[data-lightbox]');

	if (!grids.length) {
		return;
	}

	grids.forEach((grid) => {
		const figures = Array.from(grid.querySelectorAll('figure.wp-block-image'));
		const lightbox = document.getElementById(grid.dataset.lightbox);

		if (!figures.length || !lightbox) {
			return;
		}

		const lightboxImage = lightbox.querySelector('.lightbox__fig img');
		const lightboxCaption = lightbox.querySelector('.lightbox__fig figcaption');
		const closeButton = lightbox.querySelector('.lightbox__close');
		const previousButton = lightbox.querySelector('.lightbox__nav--prev');
		const nextButton = lightbox.querySelector('.lightbox__nav--next');
		const strip = lightbox.querySelector('.lightbox__strip');
		const stripButtons = Array.from(lightbox.querySelectorAll('.lightbox__strip__item'));
		const stripPrevButton = lightbox.querySelector('.lightbox__strip-nav--prev');
		const stripNextButton = lightbox.querySelector('.lightbox__strip-nav--next');
		const focusableControls = [
			closeButton,
			previousButton,
			nextButton,
			stripPrevButton,
			...stripButtons,
			stripNextButton,
		].filter(Boolean);
		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		let currentIndex = 0;
		let previouslyFocused = null;
		let resizeFrame = 0;

		const items = figures.map((figure, index) => {
			const image = figure.querySelector('img');
			const imageLink = figure.querySelector('a');
			const number = String(index + 1).padStart(2, '0');
			const filmNumber = Math.floor(index / 36) + 1;
			const frameNumber = String((index % 36) + 1).padStart(2, '0');
			const numberLabel = document.createElement('span');
			const frameLabel = document.createElement('span');
			const openLabel = image?.alt ? `Agrandir : ${image.alt}` : `Agrandir la photo ${number}`;

			figure.classList.add('pose');
			figure.dataset.index = String(index);

			if (imageLink) {
				imageLink.setAttribute('aria-label', openLabel);
			} else {
				figure.tabIndex = 0;
				figure.setAttribute('role', 'button');
				figure.setAttribute('aria-label', openLabel);
			}

			numberLabel.className = 'pose__num';
			numberLabel.textContent = number;
			numberLabel.setAttribute('aria-hidden', 'true');
			frameLabel.className = 'pose__frame';
			frameLabel.textContent = `${filmNumber > 1 ? `P${filmNumber}·` : '36•'}${frameNumber}A`;
			frameLabel.setAttribute('aria-hidden', 'true');
			figure.prepend(numberLabel);
			figure.append(frameLabel);

			if (index % 36 === 0 && figures.length > 36) {
				const separator = document.createElement('div');
				const separatorLabel = document.createElement('span');

				separator.className = `pellicule-sep${index === 0 ? ' pellicule-sep--first' : ''}`;
				separatorLabel.textContent = `Pellicule ${filmNumber}`;
				separator.append(separatorLabel);
				figure.before(separator);
			}

			return {
				alt: image?.alt || '',
				// image.src is the requested 'large' attachment size; image.currentSrc would
				// instead reflect whatever smaller srcset candidate the browser picked to
				// render the small grid thumbnail, which is too low-res for the lightbox.
				src: image?.src || '',
			};
		});

		const resizeItem = (figure) => {
			figure.style.gridRowEnd = 'auto';

			const styles = window.getComputedStyle(grid);
			const rowHeight = Number.parseFloat(styles.gridAutoRows) || 8;
			const gap = Number.parseFloat(styles.rowGap) || 8;
			const height = figure.getBoundingClientRect().height;
			const span = Math.max(1, Math.ceil((height + gap) / (rowHeight + gap)));

			figure.style.gridRowEnd = `span ${span}`;
		};

		const layout = () => {
			figures.forEach(resizeItem);
		};

		figures.forEach((figure) => {
			const image = figure.querySelector('img');

			if (!image) {
				return;
			}

			if (image.complete) {
				resizeItem(figure);
			} else {
				image.addEventListener('load', () => resizeItem(figure), { once: true });
			}
		});

		const show = (requestedIndex) => {
			currentIndex = (requestedIndex + items.length) % items.length;
			lightboxImage.src = items[currentIndex].src;
			lightboxImage.alt = items[currentIndex].alt;
			lightboxCaption.textContent = `Pose ${String(currentIndex + 1).padStart(2, '0')} / ${String(items.length).padStart(2, '0')}`;

			stripButtons.forEach((button, index) => {
				const isActive = index === currentIndex;

				button.classList.toggle('is-active', isActive);

				if (isActive) {
					button.setAttribute('aria-current', 'true');
					button.scrollIntoView({
						behavior: reduceMotion ? 'auto' : 'smooth',
						block: 'nearest',
						inline: 'center',
					});
				} else {
					button.removeAttribute('aria-current');
				}
			});
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

		figures.forEach((figure, index) => {
			figure.addEventListener('click', (event) => {
				event.preventDefault();
				open(index);
			});

			figure.addEventListener('keydown', (event) => {
				if (event.key === 'Enter' || event.key === ' ') {
					event.preventDefault();
					open(index);
				}
			});
		});

		closeButton?.addEventListener('click', close);
		previousButton?.addEventListener('click', () => show(currentIndex - 1));
		nextButton?.addEventListener('click', () => show(currentIndex + 1));
		stripButtons.forEach((button, index) => {
			button.addEventListener('click', () => show(index));
		});
		stripPrevButton?.addEventListener('click', () => {
			strip?.scrollBy({
				left: -strip.clientWidth * 0.8,
				behavior: reduceMotion ? 'auto' : 'smooth',
			});
		});
		stripNextButton?.addEventListener('click', () => {
			strip?.scrollBy({
				left: strip.clientWidth * 0.8,
				behavior: reduceMotion ? 'auto' : 'smooth',
			});
		});
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

		window.addEventListener('resize', () => {
			window.cancelAnimationFrame(resizeFrame);
			resizeFrame = window.requestAnimationFrame(layout);
		});

		window.addEventListener('load', layout, { once: true });
		layout();
	});
})();
