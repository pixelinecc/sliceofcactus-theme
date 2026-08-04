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

		let resizeFrame = 0;
		const separators = [];

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
				separators.push(separator);
			}

			return {
				alt: image?.alt || '',
				// image.src is the requested 'large' attachment size; image.currentSrc would
				// instead reflect whatever smaller srcset candidate the browser picked to
				// render the small grid thumbnail, which is too low-res for the lightbox.
				src: image?.src || '',
				caption: `Pose ${number} / ${String(figures.length).padStart(2, '0')}`,
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
			separators.forEach(resizeItem);
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

		figures.forEach((figure, index) => {
			figure.addEventListener('click', (event) => {
				event.preventDefault();
				controller.open(index);
			});

			figure.addEventListener('keydown', (event) => {
				if (event.key === 'Enter' || event.key === ' ') {
					event.preventDefault();
					controller.open(index);
				}
			});
		});

		window.addEventListener('resize', () => {
			window.cancelAnimationFrame(resizeFrame);
			resizeFrame = window.requestAnimationFrame(layout);
		});

		window.addEventListener('load', layout, { once: true });
		layout();
	});
})();
