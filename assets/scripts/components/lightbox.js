/**
 * Shared lightbox controller: open/close, keyboard nav, focus trap and
 * optional filmstrip navigation.
 *
 * Item collection stays in each template's own script (masonry sizing,
 * cards, filmstrip frames, week buttons all differ) — only the lightbox
 * chrome behaviour is shared here, matching the already-shared
 * assets/styles/components/lightbox.css.
 */

window.SocLightbox = (() => {
	'use strict';

	const create = (config) => {
		const {
			lightbox,
			image,
			caption,
			closeButton,
			prevButton,
			nextButton,
			getItems,
		} = config;

		if (!lightbox || !image || typeof getItems !== 'function') {
			return null;
		}

		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		let strip = config.strip || null;
		let currentIndex = 0;
		let previouslyFocused = null;

		const getFocusableControls = () => [
			closeButton,
			prevButton,
			nextButton,
			strip?.prevButton,
			...(strip?.buttons || []),
			strip?.nextButton,
		].filter(Boolean);

		const show = (requestedIndex) => {
			const items = getItems();

			if (!items.length) {
				return;
			}

			currentIndex = (requestedIndex + items.length) % items.length;

			const item = items[currentIndex];

			image.src = item.src;
			image.alt = item.alt || '';

			if (caption) {
				caption.textContent = item.caption || '';
			}

			if (strip?.buttons?.length) {
				strip.buttons.forEach((button, index) => {
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
			}
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
			image.removeAttribute('src');

			if (previouslyFocused instanceof HTMLElement) {
				previouslyFocused.focus();
			}
		};

		const bindStripButtons = () => {
			strip?.buttons?.forEach((button, index) => {
				button.addEventListener('click', () => show(index));
			});
		};

		const setStrip = (buttons) => {
			if (!strip) {
				return;
			}

			strip.buttons = buttons;
			bindStripButtons();
		};

		closeButton?.addEventListener('click', close);
		prevButton?.addEventListener('click', () => show(currentIndex - 1));
		nextButton?.addEventListener('click', () => show(currentIndex + 1));

		bindStripButtons();

		strip?.prevButton?.addEventListener('click', () => {
			strip.track?.scrollBy({
				left: -strip.track.clientWidth * 0.8,
				behavior: reduceMotion ? 'auto' : 'smooth',
			});
		});

		strip?.nextButton?.addEventListener('click', () => {
			strip.track?.scrollBy({
				left: strip.track.clientWidth * 0.8,
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
			} else if (event.key === 'Tab') {
				const focusableControls = getFocusableControls();

				if (!focusableControls.length) {
					return;
				}

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

		return { open, close, show, setStrip };
	};

	return { create };
})();
