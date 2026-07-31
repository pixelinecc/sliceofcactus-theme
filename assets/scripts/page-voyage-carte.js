/**
 * Carte des voyages: Leaflet map wired to server-rendered destinations.
 *
 * Adapted from sliceofcactus-astro/src/pages/voyage-carte.astro. Astro
 * passed destinations to Leaflet as a JSON blob (window.__DESTS__); here
 * each point's popup markup and each country's article list are
 * server-rendered into <template> elements (see page-voyage-carte.php)
 * and just read from the DOM.
 *
 * Country chips filter which markers show on the map and reveal that
 * country's series as a text list above the chips — markers stay one per
 * point (a country has no single lat/lon of its own).
 */

(() => {
	'use strict';

	const mapEl = document.getElementById( 'leaflet-map' );

	if ( ! mapEl || typeof L === 'undefined' ) {
		return;
	}

	const DEFAULT_CENTER = [ 30, 15 ];
	const DEFAULT_ZOOM = 2;

	const map = L.map( 'leaflet-map', {
		center: DEFAULT_CENTER,
		zoom: DEFAULT_ZOOM,
		minZoom: DEFAULT_ZOOM,
		maxZoom: 10,
		scrollWheelZoom: true,
		zoomControl: true,
		dragging: ! L.Browser.mobile,
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

	const markersByCountry = new Map();
	const allMarkers = [];

	document.querySelectorAll( '.soc-dest-popup' ).forEach( ( template ) => {
		const countrySlug = template.dataset.countrySlug || '';
		const lat = Number.parseFloat( template.dataset.lat );
		const lon = Number.parseFloat( template.dataset.lon );

		if ( Number.isNaN( lat ) || Number.isNaN( lon ) ) {
			return;
		}

		const popup = L.popup( { maxWidth: 260, className: 'leaflet-popup-dark' } ).setContent( template.innerHTML );
		const marker = L.marker( [ lat, lon ], { icon: pinIcon } ).addTo( map ).bindPopup( popup );

		allMarkers.push( marker );

		if ( ! markersByCountry.has( countrySlug ) ) {
			markersByCountry.set( countrySlug, [] );
		}

		markersByCountry.get( countrySlug ).push( marker );
	} );

	const articleLists = new Map();

	document.querySelectorAll( '.soc-country-articles' ).forEach( ( template ) => {
		if ( template.dataset.countrySlug ) {
			articleLists.set( template.dataset.countrySlug, template.innerHTML );
		}
	} );

	const articlesEl = document.getElementById( 'destArticles' );

	document.querySelectorAll( '#destChips button[data-country-slug]' ).forEach( ( button ) => {
		button.addEventListener( 'click', () => {
			const slug = button.dataset.countrySlug;

			document.querySelectorAll( '#destChips button' ).forEach( ( other ) => other.classList.remove( 'is-active' ) );
			button.classList.add( 'is-active' );

			const visibleMarkers = slug ? ( markersByCountry.get( slug ) || [] ) : allMarkers;

			allMarkers.forEach( ( marker ) => {
				const visible = visibleMarkers.includes( marker );

				if ( visible && ! map.hasLayer( marker ) ) {
					marker.addTo( map );
				} else if ( ! visible && map.hasLayer( marker ) ) {
					map.removeLayer( marker );
				}
			} );

			if ( ! slug ) {
				map.setView( DEFAULT_CENTER, DEFAULT_ZOOM, { animate: true } );
			} else if ( 1 === visibleMarkers.length ) {
				map.setView( visibleMarkers[ 0 ].getLatLng(), 6, { animate: true } );
			} else if ( visibleMarkers.length > 1 ) {
				map.fitBounds( L.featureGroup( visibleMarkers ).getBounds().pad( 0.2 ) );
			}

			if ( articlesEl ) {
				const content = slug ? articleLists.get( slug ) : '';

				articlesEl.innerHTML = content || '';
				articlesEl.hidden = ! content;
			}
		} );
	} );
} )();
