/**
 * Carte des voyages: Leaflet map wired to server-rendered destinations.
 *
 * Adapted from sliceofcactus-astro/src/pages/voyage-carte.astro. Astro
 * passed destinations to Leaflet as a JSON blob (window.__DESTS__); here
 * each destination's popup markup is server-rendered into a <template>
 * (see page-voyage-carte.php) and just read from the DOM.
 */

(() => {
	'use strict';

	const mapEl = document.getElementById( 'leaflet-map' );

	if ( ! mapEl || typeof L === 'undefined' ) {
		return;
	}

	const popups = new Map();

	document.querySelectorAll( '.soc-dest-popup' ).forEach( ( template ) => {
		const name = template.dataset.destName;

		if ( name ) {
			popups.set( name, template.innerHTML );
		}
	} );

	const map = L.map( 'leaflet-map', {
		center: [ 30, 15 ],
		zoom: 2,
		minZoom: 2,
		maxZoom: 10,
		scrollWheelZoom: true,
		zoomControl: true,
	} );

	L.tileLayer( 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
		subdomains: 'abcd',
		maxZoom: 19,
	} ).addTo( map );

	const pinIcon = L.divIcon( {
		className: '',
		html: '<span style="display:block;width:14px;height:14px;background:#12B26A;border:2px solid #fff;border-radius:50%;box-shadow:0 0 0 3px rgba(18,178,106,.35)"></span>',
		iconSize: [ 14, 14 ],
		iconAnchor: [ 7, 7 ],
	} );

	document.querySelectorAll( '#destChips button[data-dest-name]' ).forEach( ( button ) => {
		const name = button.dataset.destName;
		const lat = Number.parseFloat( button.dataset.lat );
		const lon = Number.parseFloat( button.dataset.lon );

		if ( ! name || Number.isNaN( lat ) || Number.isNaN( lon ) ) {
			return;
		}

		const popup = L.popup( { maxWidth: 260, className: 'leaflet-popup-dark' } ).setContent( popups.get( name ) || '' );
		const marker = L.marker( [ lat, lon ], { icon: pinIcon } ).addTo( map ).bindPopup( popup );

		button.addEventListener( 'click', () => {
			map.setView( marker.getLatLng(), 6, { animate: true } );
			marker.openPopup();
			document.querySelectorAll( '#destChips button' ).forEach( ( other ) => other.classList.remove( 'is-active' ) );
			button.classList.add( 'is-active' );
		} );
	} );
} )();