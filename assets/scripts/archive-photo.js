/**
 * Photo archive: client-side narration filter chips.
 *
 * Adapted from sliceofcactus-astro/src/pages/photo/index.astro.
 * Labels come from data attributes rendered per cell (WP terms already
 * carry their display name) instead of a hardcoded map.
 */

(() => {
	'use strict';

	const grid = document.getElementById('soc-serie-grid');
	const chips = document.getElementById('soc-rub-chips');
	const count = document.getElementById('soc-serie-count');

	if (!grid || !chips) {
		return;
	}

	const cells = Array.from(grid.querySelectorAll('.serie-cell'));

	if (!cells.length) {
		return;
	}

	const labels = new Map();

	cells.forEach((cell) => {
		const slug = cell.dataset.narration;
		const label = cell.dataset.narrationLabel;

		if (slug && label && !labels.has(slug)) {
			labels.set(slug, label);
		}
	});

	let currentFilter = 'toutes';

	const render = () => {
		const visible = currentFilter === 'toutes'
			? cells
			: cells.filter((cell) => cell.dataset.narration === currentFilter);

		cells.forEach((cell) => {
			cell.style.display = 'none';
		});

		visible.forEach((cell, index) => {
			cell.style.display = '';

			const numberElement = cell.querySelector('.serie-cell__no');

			if (numberElement) {
				const number = String(index + 1).padStart(2, '0');
				const total = String(visible.length).padStart(2, '0');
				numberElement.textContent = `${number} / ${total}`;
			}
		});

		if (count) {
			const visibleCount = visible.length;
			count.textContent = `${String(visibleCount).padStart(2, '0')} série${visibleCount > 1 ? 's' : ''}`;
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