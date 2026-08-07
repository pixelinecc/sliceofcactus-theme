/**
 * Single récit — opens the "margin" hero snapshot (.article__snapshot-link,
 * see template-parts/single/recit-article.php) in the shared lightbox
 * (assets/scripts/components/lightbox.js). Single image, no filmstrip.
 */
(() => {
	'use strict';

	const trigger = document.querySelector('.article__snapshot-link[data-lightbox]');

	if (!trigger) {
		return;
	}

	const lightbox = document.getElementById(trigger.dataset.lightbox);

	if (!lightbox) {
		return;
	}

	const items = [
		{
			src: trigger.dataset.full,
			alt: trigger.querySelector('img')?.alt || '',
			caption: trigger.dataset.caption || '',
		},
	];

	const controller = window.SocLightbox.create({
		lightbox,
		image: lightbox.querySelector('.lightbox__fig img'),
		caption: lightbox.querySelector('.lightbox__fig figcaption'),
		closeButton: lightbox.querySelector('.lightbox__close'),
		getItems: () => items,
	});

	if (!controller) {
		return;
	}

	trigger.addEventListener('click', (event) => {
		event.preventDefault();
		controller.open(0);
	});
})();
