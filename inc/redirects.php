<?php
/**
 * Redirects URLs that duplicate a dedicated Photo page, and legacy URLs
 * left behind by the Création → Atelier rubrique rename.
 *
 * The narration taxonomy is public, so WordPress generates a default
 * archive for every term (/narration/{slug}/) with no template of its own
 * — falling back to the generic WordPress loop. For projet-52 and
 * color-your-life, that archive would show the exact same "photo" posts as
 * the dedicated pages /projet-52/ and /color-your-life/, so there is no
 * reason to keep both alive: redirect to the canonical page instead of
 * building a second template for identical content.
 *
 * Each Projet 52 year is also stored as its own ordinary "photo" post (see
 * soc_get_projet52_years() in inc/queries.php), which likewise gives it its
 * own single URL (e.g. /photos/2025/) rendering the generic contact-sheet
 * template on an unrelated, indexable duplicate of /projet-52/ — redirected
 * the same way.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redirects narration term archives that duplicate a dedicated page.
 *
 * @return void
 */
function soc_redirect_duplicate_narration_archives(): void {
	if ( ! is_tax( 'narration' ) ) {
		return;
	}

	$term = get_queried_object();

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$targets = array(
		'projet-52'       => '/projet-52/',
		'color-your-life' => '/color-your-life/',
	);

	if ( ! isset( $targets[ $term->slug ] ) ) {
		return;
	}

	wp_safe_redirect( home_url( $targets[ $term->slug ] ), 301 );
	exit;
}
add_action( 'template_redirect', 'soc_redirect_duplicate_narration_archives' );

/**
 * Redirects a single Projet 52 photo post (one year) to /projet-52/.
 *
 * @return void
 */
function soc_redirect_projet52_single(): void {
	if ( ! is_singular( 'photo' ) ) {
		return;
	}

	if ( ! has_term( 'projet-52', 'narration', get_queried_object_id() ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/projet-52/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'soc_redirect_projet52_single' );

/**
 * Redirects the old /creations/ URLs (rubrique renamed to "Atelier",
 * slug now /atelier/ — see acf-json/post_type_soc_creation.json) to their
 * /atelier/ equivalent, archive and singles alike.
 *
 * @return void
 */
function soc_redirect_legacy_creation_urls(): void {
	if ( ! is_404() ) {
		return;
	}

	$path = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ), PHP_URL_PATH );

	if ( ! is_string( $path ) || 0 !== strpos( $path, '/creations/' ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/atelier/' . substr( $path, strlen( '/creations/' ) ) ), 301 );
	exit;
}
add_action( 'template_redirect', 'soc_redirect_legacy_creation_urls' );
