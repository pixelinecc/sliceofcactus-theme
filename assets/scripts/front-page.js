/**
 * Front page: preloader, typewriter, filmstrip lightbox, hero blob parallax.
 *
 * Adapted from sliceofcactus-astro/src/pages/index.astro and
 * sliceofcactus-astro/public/js/main.js. Site-wide interactions (cursor,
 * magnetic buttons, scroll reveal) live in assets/scripts/main.js instead.
 */

(() => {
	'use strict';

	const preloader = document.getElementById( 'preloader' );
	const preCount = document.getElementById( 'preCount' );

	if ( preloader && preCount ) {
		let count = 0;
		const tick = window.setInterval( () => {
			count++;
			preCount.textContent = String( count ).padStart( 2, '0' );

			if ( count >= 36 ) {
				window.clearInterval( tick );
				window.setTimeout( () => {
					preloader.classList.add( 'is-done' );
					document.body.classList.add( 'is-loaded' );
				}, 350 );
			}
		}, 42 );
	}

	const typewriterEl = document.getElementById( 'tw' );

	if ( typewriterEl ) {
		const phrases = [
			'Atelier d’images',
			'Photo, dessin & récits',
			'Jamais plus de 36 poses par série',
			'Slow & argentique',
		];

		if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			typewriterEl.textContent = phrases[ 0 ];
		} else {
			let phraseIndex = 0;
			let charIndex = 0;
			let deleting = false;

			const tick = () => {
				const full = phrases[ phraseIndex ];

				if ( ! deleting ) {
					typewriterEl.textContent = full.slice( 0, ++charIndex );

					if ( charIndex === full.length ) {
						deleting = true;
						window.setTimeout( tick, 1700 );
						return;
					}

					window.setTimeout( tick, 55 );
					return;
				}

				typewriterEl.textContent = full.slice( 0, --charIndex );

				if ( charIndex === 0 ) {
					deleting = false;
					phraseIndex = ( phraseIndex + 1 ) % phrases.length;
					window.setTimeout( tick, 350 );
					return;
				}

				window.setTimeout( tick, 28 );
			};

			tick();
		}
	}

	const blob = document.querySelector( '.hero__blob' );

	if ( blob && ! ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) ) {
		window.addEventListener( 'scroll', () => {
			blob.style.transform = `translateY(${ window.scrollY * 0.18 }px)`;
		}, { passive: true } );
	}

	const filmstrip = document.querySelector( '.filmstrip[data-lightbox]' );

	if ( ! filmstrip ) {
		return;
	}

	const lightbox = document.getElementById( filmstrip.dataset.lightbox );
	const frames = Array.from( filmstrip.querySelectorAll( '.frame' ) );

	if ( ! lightbox || ! frames.length ) {
		return;
	}

	const items = frames.map( ( frame ) => ( {
		src: frame.dataset.full,
		alt: frame.dataset.caption,
		caption: frame.dataset.caption,
	} ) );

	const controller = window.SocLightbox.create( {
		lightbox,
		image: lightbox.querySelector( '.lightbox__fig img' ),
		caption: lightbox.querySelector( '.lightbox__fig figcaption' ),
		closeButton: lightbox.querySelector( '.lightbox__close' ),
		prevButton: lightbox.querySelector( '.lightbox__nav--prev' ),
		nextButton: lightbox.querySelector( '.lightbox__nav--next' ),
		getItems: () => items,
		strip: {
			track: lightbox.querySelector( '.lightbox__strip' ),
			buttons: Array.from( lightbox.querySelectorAll( '.lightbox__strip__item' ) ),
			prevButton: lightbox.querySelector( '.lightbox__strip-nav--prev' ),
			nextButton: lightbox.querySelector( '.lightbox__strip-nav--next' ),
		},
	} );

	if ( ! controller ) {
		return;
	}

	frames.forEach( ( frame, index ) => {
		frame.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			controller.open( index );
		} );
	} );
} )();