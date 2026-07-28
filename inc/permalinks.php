<?php
/**
 * Single creation permalinks: /dessin/{slug}/ and /coloriage/{slug}/.
 *
 * Besoin concret : reproduire les URLs Astro des singles dessin/coloriage
 * (dessin/[id].astro, coloriage/[id].astro), déjà utilisées comme référence
 * par template-parts/single/creation-contact-sheet.php (back-link,
 * view-toggle) et par le book-grid de taxonomy-medium.php.
 *
 * Pourquoi le natif ne suffit pas : contrairement à l'archive de taxonomie
 * (medium, rendue publique — voir acf-json/taxonomy_soc_medium.json),
 * WordPress n'a pas de mécanisme natif pour placer un terme de taxonomie
 * dans le permalien d'un post type. C'est le même besoin que résolvent
 * WooCommerce (%product_cat%) ou Custom Post Type UI : une règle de
 * rewrite pour l'URL entrante, plus un filtre post_type_link pour l'URL
 * générée.
 *
 * Coût de maintenance : une règle + un filtre + un flush versionné, dans
 * ce seul fichier. L'ancienne URL /creations/{slug}/ (générée par le CPT
 * lui-même) continue de fonctionner en parallèle ; seule l'URL affichée
 * et générée par get_permalink() change.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes /dessin/{slug}/ and /coloriage/{slug}/ to the matching creation.
 *
 * @return void
 */
function soc_creation_permalink_rewrite_rule(): void {
	add_rewrite_rule(
		'^(?:dessin|coloriage)/([^/]+)/?$',
		'index.php?post_type=creation&name=$matches[1]',
		'top'
	);
}
add_action( 'init', 'soc_creation_permalink_rewrite_rule' );

/**
 * Builds the /dessin/ or /coloriage/ permalink of a creation.
 *
 * @param string  $link Default permalink.
 * @param WP_Post $post Post being linked to.
 * @return string
 */
function soc_creation_permalink( string $link, WP_Post $post ): string {
	if ( 'creation' !== $post->post_type ) {
		return $link;
	}

	$medium = soc_get_creation_medium( $post->ID );
	$slug   = $medium && 'coloriage' === $medium->slug ? 'coloriage' : 'dessin';

	return home_url( "/{$slug}/{$post->post_name}/" );
}
add_filter( 'post_type_link', 'soc_creation_permalink', 10, 2 );

/**
 * Flushes rewrite rules once after the creation permalink rule changes.
 *
 * @return void
 */
function soc_creation_maybe_flush_rewrite_rules(): void {
	$version = '1';

	if ( get_option( 'soc_creation_permalink_version' ) !== $version ) {
		soc_creation_permalink_rewrite_rule();
		flush_rewrite_rules();
		update_option( 'soc_creation_permalink_version', $version );
	}
}
add_action( 'init', 'soc_creation_maybe_flush_rewrite_rules', 20 );