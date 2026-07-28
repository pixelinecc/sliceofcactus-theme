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
 * Footer interactions adapted from sliceofcactus-astro/src/components/Footer.astro
 * and the magnetic-link behavior in public/js/main.js.
 */

(() => {
	'use strict';

	const footer = document.querySelector('.footer');

	if (!footer) {
		return;
	}

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const topLink = footer.querySelector('.footer__top-link');

	topLink?.addEventListener('click', (event) => {
		event.preventDefault();
		window.scrollTo({
			top: 0,
			behavior: reducedMotion.matches ? 'auto' : 'smooth',
		});
	});

	const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	if (!finePointer || reducedMotion.matches) {
		return;
	}

	footer.querySelectorAll('[data-magnetic]').forEach((element) => {
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
})();
