/**
 * Color Your Life: spectrum chip filter.
 *
 * Adapted from sliceofcactus-astro/src/pages/color-your-life.astro.
 */

(() => {
	'use strict';

	const cards = Array.from( document.querySelectorAll( '.cyl-card' ) );
	const chips = Array.from( document.querySelectorAll( '.spectrum button' ) );

	if ( ! cards.length || ! chips.length ) {
		return;
	}

	const filter = ( key ) => {
		cards.forEach( ( card ) => {
			card.classList.toggle( 'is-dim', key !== 'all' && card.dataset.color !== key );
		} );
	};

	chips.forEach( ( chip ) => {
		chip.addEventListener( 'click', () => {
			chips.forEach( ( other ) => other.classList.remove( 'is-active' ) );
			chip.classList.add( 'is-active' );
			filter( chip.dataset.color );
		} );
	} );
})();