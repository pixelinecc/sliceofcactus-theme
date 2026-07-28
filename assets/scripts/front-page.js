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

	const filmstrip = document.getElementById( 'filmstrip' );
	const lightbox = document.getElementById( 'lightbox' );

	if ( ! filmstrip || ! lightbox ) {
		return;
	}

	const frames = Array.from( filmstrip.querySelectorAll( '.frame' ) );
	const lightboxImage = document.getElementById( 'lightboxImg' );
	const lightboxCaption = document.getElementById( 'lightboxCap' );
	const closeButton = document.getElementById( 'lightboxClose' );
	const previousButton = document.getElementById( 'lbPrev' );
	const nextButton = document.getElementById( 'lbNext' );
	let currentIndex = 0;

	const show = ( index ) => {
		currentIndex = ( index + frames.length ) % frames.length;
		const frame = frames[ currentIndex ];

		lightboxImage.src = frame.dataset.full;
		lightboxImage.alt = frame.dataset.caption;
		lightboxCaption.textContent = frame.dataset.caption;
	};

	const open = ( index ) => {
		show( index );
		lightbox.classList.add( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'false' );
	};

	const close = () => {
		lightbox.classList.remove( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'true' );
	};

	frames.forEach( ( frame, index ) => {
		frame.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			open( index );
		} );
	} );

	closeButton?.addEventListener( 'click', close );
	previousButton?.addEventListener( 'click', () => show( currentIndex - 1 ) );
	nextButton?.addEventListener( 'click', () => show( currentIndex + 1 ) );

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
			show( currentIndex - 1 );
		} else if ( event.key === 'ArrowRight' ) {
			show( currentIndex + 1 );
		}
	} );
} )();