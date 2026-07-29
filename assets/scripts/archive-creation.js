/**
 * Créations archive: client-side medium (technique) filter chips.
 *
 * No Astro equivalent (new unified /creations/ page). Adapted from
 * assets/scripts/archive-photo.js: labels come from data attributes
 * rendered per card (WP terms already carry their display name) instead
 * of a hardcoded map.
 */

(() => {
	'use strict';

	const grid = document.getElementById('soc-creation-grid');
	const chips = document.getElementById('soc-medium-chips');
	const count = document.getElementById('soc-creation-count');

	if (!grid || !chips) {
		return;
	}

	const cards = Array.from(grid.querySelectorAll('.book-card'));

	if (!cards.length) {
		return;
	}

	const labels = new Map();

	cards.forEach((card) => {
		const slug = card.dataset.medium;
		const label = card.dataset.mediumLabel;

		if (slug && label && !labels.has(slug)) {
			labels.set(slug, label);
		}
	});

	let currentFilter = 'toutes';

	const render = () => {
		const visible = currentFilter === 'toutes'
			? cards
			: cards.filter((card) => card.dataset.medium === currentFilter);

		cards.forEach((card) => {
			card.style.display = 'none';
		});

		visible.forEach((card) => {
			card.style.display = '';
		});

		if (count) {
			const visibleCount = visible.length;
			count.textContent = `${String(visibleCount).padStart(2, '0')} création${visibleCount > 1 ? 's' : ''}`;
		}
	};

	const createChip = (slug, label) => {
		const button = document.createElement('button');

		button.type = 'button';
		button.textContent = label;

		if (slug === currentFilter) {
			button.classList.add('is-active');
		}

		button.addEventListener('click', () => {
			currentFilter = slug;
			chips.querySelectorAll('button').forEach((chip) => chip.classList.remove('is-active'));
			button.classList.add('is-active');
			render();
		});

		return button;
	};

	chips.appendChild(createChip('toutes', 'Toutes'));
	labels.forEach((label, slug) => chips.appendChild(createChip(slug, label)));
})();
