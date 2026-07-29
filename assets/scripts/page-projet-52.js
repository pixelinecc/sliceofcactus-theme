/**
 * Projet 52: year switching and lightbox.
 *
 * Adapted from sliceofcactus-astro/src/pages/projet-52.astro. Unlike Astro,
 * every year's grid is server-rendered from a real photo gallery (see
 * page-projet-52.php) — this script only toggles which grid is visible and
 * drives a lightbox over the currently visible one, instead of generating
 * placeholder markup from a JS config.
 */

(() => {
	'use strict';

	const yearButtons = document.querySelectorAll( '#p52Years button[data-year]' );
	const barFill = document.getElementById( 'p52BarFill' );
	const countEl = document.getElementById( 'p52Count' );
	const lightbox = document.getElementById( 'p52lb' );

	if ( ! yearButtons.length || ! lightbox ) {
		return;
	}

	const lightboxImage = document.getElementById( 'p52lbImg' );
	const lightboxCaption = document.getElementById( 'p52lbCap' );
	const closeButton = document.getElementById( 'p52lbClose' );
	const previousButton = document.getElementById( 'p52lbPrev' );
	const nextButton = document.getElementById( 'p52lbNext' );

	let current = [];
	let currentIndex = 0;

	const showYear = ( year ) => {
		document.querySelectorAll( '[data-p52-grid]' ).forEach( ( grid ) => {
			grid.hidden = grid.id !== `p52Grid-${ year }`;
		} );

		yearButtons.forEach( ( button ) => {
			const isActive = button.dataset.year === String( year );

			button.classList.toggle( 'is-active', isActive );

			if ( isActive ) {
				button.setAttribute( 'aria-current', 'true' );
				const done = Number.parseInt( button.dataset.done, 10 ) || 0;

				if ( countEl ) {
					countEl.textContent = `${ done } / 52`;
				}

				if ( barFill ) {
					barFill.style.width = `${ ( done / 52 * 100 ).toFixed( 1 ) }%`;
				}
			} else {
				button.removeAttribute( 'aria-current' );
			}
		} );
	};

	const open = ( index ) => {
		const grid = document.querySelector( '[data-p52-grid]:not([hidden])' );

		if ( ! grid ) {
			return;
		}

		current = Array.from( grid.querySelectorAll( '.wk--full' ) );

		if ( ! current.length ) {
			return;
		}

		currentIndex = ( index + current.length ) % current.length;

		const button = current[ currentIndex ];

		lightboxImage.src = button.dataset.full;
		lightboxImage.alt = button.dataset.caption;
		lightboxCaption.textContent = button.dataset.caption;
		lightbox.classList.add( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'false' );
	};

	const close = () => {
		lightbox.classList.remove( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'true' );
	};

	yearButtons.forEach( ( button ) => {
		button.addEventListener( 'click', () => showYear( button.dataset.year ) );
	} );

	document.querySelectorAll( '.wk--full' ).forEach( ( button ) => {
		button.addEventListener( 'click', () => {
			const grid = button.closest( '[data-p52-grid]' );

			if ( ! grid ) {
				return;
			}

			current = Array.from( grid.querySelectorAll( '.wk--full' ) );
			open( current.indexOf( button ) );
		} );
	} );

	closeButton?.addEventListener( 'click', close );
	previousButton?.addEventListener( 'click', () => open( currentIndex - 1 ) );
	nextButton?.addEventListener( 'click', () => open( currentIndex + 1 ) );

	lightbox.addEventListener( 'click', ( event ) => {
		if ( event.target === lightbox ) {
			close();
		}
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( ! lightbox.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( event.key === 'Escape' ) {
			close();
		} else if ( event.key === 'ArrowLeft' ) {
			open( currentIndex - 1 );
		} else if ( event.key === 'ArrowRight' ) {
			open( currentIndex + 1 );
		}
	} );

	const activeButton = document.querySelector( '#p52Years button.is-active' );

	if ( activeButton ) {
		showYear( activeButton.dataset.year );
	}
})();
