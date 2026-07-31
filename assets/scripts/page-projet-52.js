/**
 * Projet 52: year switching and lightbox.
 *
 * Adapted from sliceofcactus-astro/src/pages/projet-52.astro. Unlike Astro,
 * every year's grid is server-rendered from a real photo gallery (see
 * page-projet-52.php) — this script only toggles which grid is visible and
 * drives the shared lightbox (assets/scripts/components/lightbox.js) over
 * whichever grid the clicked week belongs to.
 */

(() => {
	'use strict';

	const yearButtons = document.querySelectorAll( '#p52Years button[data-year]' );
	const barFill = document.getElementById( 'p52BarFill' );
	const countEl = document.getElementById( 'p52Count' );
	const lightbox = document.getElementById( 'p52-lightbox' );

	if ( ! yearButtons.length || ! lightbox ) {
		return;
	}

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

	const stripTrack = document.getElementById( 'p52lbStrip' );
	let activeButtons = [];

	const controller = window.SocLightbox.create( {
		lightbox,
		image: lightbox.querySelector( '.lightbox__fig img' ),
		caption: lightbox.querySelector( '.lightbox__fig figcaption' ),
		closeButton: lightbox.querySelector( '.lightbox__close' ),
		prevButton: lightbox.querySelector( '.lightbox__nav--prev' ),
		nextButton: lightbox.querySelector( '.lightbox__nav--next' ),
		getItems: () => activeButtons.map( ( button ) => ( {
			src: button.dataset.full,
			alt: button.dataset.caption,
			caption: button.dataset.caption,
		} ) ),
		strip: {
			track: stripTrack,
			buttons: [],
			prevButton: lightbox.querySelector( '.lightbox__strip-nav--prev' ),
			nextButton: lightbox.querySelector( '.lightbox__strip-nav--next' ),
		},
	} );

	if ( ! controller ) {
		return;
	}

	// Rebuilds the thumbnail strip from the clicked week's grid, since a
	// single lightbox is shared across every year instead of one per grid.
	const renderStripFor = ( grid ) => {
		if ( ! stripTrack ) {
			return [];
		}

		stripTrack.innerHTML = '';

		return Array.from( grid.querySelectorAll( '.wk--full' ) ).map( ( weekButton ) => {
			const thumbButton = document.createElement( 'button' );
			const thumbImage = weekButton.querySelector( 'img' );

			thumbButton.type = 'button';
			thumbButton.className = 'lightbox__strip__item';
			thumbButton.setAttribute( 'aria-label', weekButton.dataset.caption || '' );

			if ( thumbImage ) {
				thumbButton.append( thumbImage.cloneNode( true ) );
			}

			stripTrack.append( thumbButton );

			return thumbButton;
		} );
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

			activeButtons = Array.from( grid.querySelectorAll( '.wk--full' ) );
			lightbox.classList.toggle( 'lightbox--filmstrip', activeButtons.length > 1 );
			controller.setStrip( renderStripFor( grid ) );
			controller.open( activeButtons.indexOf( button ) );
		} );
	} );

	const activeButton = document.querySelector( '#p52Years button.is-active' );

	if ( activeButton ) {
		showYear( activeButton.dataset.year );
	}
})();
