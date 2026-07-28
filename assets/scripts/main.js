/**
 * Header interactions adapted from sliceofcactus-astro/public/js/main.js.
 */

(() => {
	'use strict';

	const header = document.getElementById('soc-site-header');

	if (!header) {
		return;
	}

	const menuToggle = document.getElementById('soc-menu-toggle');
	const navigation = document.getElementById('soc-primary-navigation');
	let lastScrollY = Math.max(window.scrollY, 0);
	let scrollTicking = false;

	const updateHeaderOnScroll = () => {
		const currentScrollY = Math.max(window.scrollY, 0);
		const menuIsOpen = header.classList.contains('is-menu-open');

		header.classList.toggle('is-solid', currentScrollY > 60);
		header.classList.toggle(
			'is-hidden',
			!menuIsOpen && currentScrollY > lastScrollY && currentScrollY > 300
		);

		lastScrollY = currentScrollY;
		scrollTicking = false;
	};

	const requestHeaderUpdate = () => {
		if (scrollTicking) {
			return;
		}

		scrollTicking = true;
		window.requestAnimationFrame(updateHeaderOnScroll);
	};

	window.addEventListener('scroll', requestHeaderUpdate, { passive: true });
	updateHeaderOnScroll();

	if (!menuToggle || !navigation) {
		return;
	}

	header.classList.add('has-menu-toggle');

	const menuLinks = navigation.querySelectorAll('a');
	const firstMenuLink = menuLinks.item(0);
	const openLabel = menuToggle.dataset.labelOpen || 'Ouvrir le menu';
	const closeLabel = menuToggle.dataset.labelClose || 'Fermer le menu';

	const setMenuState = (isOpen, restoreFocus = false) => {
		header.classList.toggle('is-menu-open', isOpen);
		header.classList.remove('is-hidden');
		navigation.classList.toggle('is-open', isOpen);
		menuToggle.classList.toggle('is-open', isOpen);
		menuToggle.setAttribute('aria-expanded', String(isOpen));
		menuToggle.setAttribute('aria-label', isOpen ? closeLabel : openLabel);

		if (isOpen && firstMenuLink) {
			window.requestAnimationFrame(() => firstMenuLink.focus());
		} else if (restoreFocus) {
			menuToggle.focus();
		}
	};

	menuToggle.addEventListener('click', () => {
		const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
		setMenuState(!isOpen);
	});

	menuLinks.forEach((menuLink) => {
		menuLink.addEventListener('click', () => setMenuState(false));
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape' || menuToggle.getAttribute('aria-expanded') !== 'true') {
			return;
		}

		setMenuState(false, true);
	});

	const desktopQuery = window.matchMedia('(min-width: 721px)');
	const closeMenuOnDesktop = (event) => {
		if (event.matches) {
			setMenuState(false);
		}
	};

	if (typeof desktopQuery.addEventListener === 'function') {
		desktopQuery.addEventListener('change', closeMenuOnDesktop);
	} else {
		desktopQuery.addListener(closeMenuOnDesktop);
	}
})();

/**
 * Footer interactions adapted from sliceofcactus-astro/src/components/Footer.astro.
 *
 * The magnetic-link behavior used to live here, scoped to .footer; it's now
 * site-wide (see the "Site-wide interactions" block below), matching
 * Astro's own document.querySelectorAll('[data-magnetic]').
 */

(() => {
	'use strict';

	const footer = document.querySelector('.footer');
	const topLink = footer?.querySelector('.footer__top-link');

	if (!topLink) {
		return;
	}

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	topLink.addEventListener('click', (event) => {
		event.preventDefault();
		window.scrollTo({
			top: 0,
			behavior: reducedMotion.matches ? 'auto' : 'smooth',
		});
	});
})();

/**
 * Site-wide interactions adapted from sliceofcactus-astro/public/js/main.js:
 * custom cursor, magnetic buttons and scroll reveal. Preloader, typewriter,
 * filmstrip and hero blob parallax are home-only and live in
 * assets/scripts/front-page.js instead.
 */

(() => {
	'use strict';

	const cursor = document.getElementById('cursor');
	const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (finePointer && cursor) {
		let mouseX = window.innerWidth / 2;
		let mouseY = window.innerHeight / 2;
		let cursorX = mouseX;
		let cursorY = mouseY;

		window.addEventListener('mousemove', (event) => {
			mouseX = event.clientX;
			mouseY = event.clientY;
		});

		const render = () => {
			cursorX += (mouseX - cursorX) * 0.18;
			cursorY += (mouseY - cursorY) * 0.18;
			cursor.style.transform = `translate(${cursorX}px, ${cursorY}px) translate(-50%, -50%)`;
			window.requestAnimationFrame(render);
		};

		render();

		document.querySelectorAll('a, button, [data-magnetic]').forEach((element) => {
			element.addEventListener('mouseenter', () => cursor.classList.add('is-hover'));
			element.addEventListener('mouseleave', () => cursor.classList.remove('is-hover'));
		});
	}

	if (finePointer && !reducedMotion) {
		document.querySelectorAll('[data-magnetic]').forEach((element) => {
			element.addEventListener('mousemove', (event) => {
				const bounds = element.getBoundingClientRect();
				const offsetX = event.clientX - bounds.left - bounds.width / 2;
				const offsetY = event.clientY - bounds.top - bounds.height / 2;

				element.style.transform = `translate(${offsetX * 0.28}px, ${offsetY * 0.28}px)`;
			});

			element.addEventListener('mouseleave', () => {
				element.style.transform = '';
			});
		});
	}

	const revealTargets = document.querySelectorAll('[data-reveal]');

	if (revealTargets.length) {
		const observer = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.style.transitionDelay = `${entry.target.dataset.delay || 0}ms`;
				entry.target.classList.add('is-in');
				observer.unobserve(entry.target);
			});
		}, { threshold: 0.15 });

		revealTargets.forEach((element, index) => {
			if (!element.dataset.delay) {
				element.dataset.delay = String((index % 4) * 90);
			}

			observer.observe(element);
		});
	}
})();
