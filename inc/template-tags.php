<?php
/**
 * Reusable presentation functions for theme templates.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the controlled creation type label for a creation.
 *
 * The content model allows one creation_type term per creation. Returning an
 * empty string keeps future templates resilient when no term is assigned.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string
 */
function soc_get_creation_type_label( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return '';
	}

	$terms = get_the_terms( $post_id, 'creation_type' );

	if ( ! is_array( $terms ) || empty( $terms ) ) {
		return '';
	}

	return $terms[0]->name;
}

/**
 * Displays the controlled creation type label for a creation.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return void
 */
function soc_the_creation_type( int $post_id = 0 ): void {
	$label = soc_get_creation_type_label( $post_id );

	if ( '' !== $label ) {
		echo esc_html( $label );
	}
}

/**
 * Gets the narrations assigned to a photo.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return WP_Term[]
 */
function soc_get_photo_narrations( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$terms = wp_get_post_terms(
		$post_id,
		'narration',
		array(
			'orderby' => 'term_id',
			'order'   => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	return $terms;
}

/**
 * Gets the short introduction of a photo.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string
 */
function soc_get_photo_intro( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	$intro = function_exists( 'get_field' )
		? get_field( 'soc_photo_intro', $post_id )
		: get_post_meta( $post_id, 'soc_photo_intro', true );

	return is_string( $intro ) ? trim( $intro ) : '';
}

/**
 * Gets the location of a photo series.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return array{name?: string, country?: string, latitude?: float, longitude?: float}
 */
function soc_get_photo_location( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$location = function_exists( 'get_field' ) ? get_field( 'soc_photo_location', $post_id ) : null;

	if ( ! is_array( $location ) || empty( $location['name'] ) ) {
		return array();
	}

	return $location;
}

/**
 * Gets the project year of a photo series.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string Four-digit year, or an empty string when unset.
 */
function soc_get_photo_year( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	$date = function_exists( 'get_field' )
		? get_field( 'soc_photo_date', $post_id )
		: get_post_meta( $post_id, 'soc_photo_date', true );

	return is_string( $date ) && '' !== $date ? substr( $date, 0, 4 ) : '';
}

/**
 * Gets the dominant color of a photo series.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string Hex color, or an empty string when unset.
 */
function soc_get_photo_color( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	$color = function_exists( 'get_field' ) ? get_field( 'soc_photo_color', $post_id ) : null;
	$hex   = is_array( $color ) && ! empty( $color['hex'] ) ? $color['hex'] : '';

	return is_string( $hex ) ? trim( $hex ) : '';
}

/**
 * Prints the mobile browser theme-color meta tag for a single photo.
 *
 * Mirrors the per-series themeColor prop of sliceofcactus-astro/src/layouts/Base.astro,
 * falling back to the same default cactus green when no color is set.
 */
function soc_photo_theme_color_meta(): void {
	if ( ! is_singular( 'photo' ) ) {
		return;
	}

	$color = soc_get_photo_color();

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( '' !== $color ? $color : '#12B26A' ) );
}
add_action( 'wp_head', 'soc_photo_theme_color_meta' );

/**
 * Finds the first assigned narration that has a dedicated template part.
 *
 * A narration without a dedicated template uses the generic photo template.
 * This lets new narration terms exist in WordPress without causing an error
 * or pretending that their visual treatment has already been designed.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string Narration slug, or an empty string for the generic template.
 */
function soc_get_photo_narration_template_slug( int $post_id = 0 ): string {
	foreach ( soc_get_photo_narrations( $post_id ) as $term ) {
		$slug = sanitize_title( $term->slug );

		if ( '' === $slug ) {
			continue;
		}

		$template = sprintf( 'template-parts/single/photo-%s.php', $slug );

		if ( '' !== locate_template( $template, false, false ) ) {
			return $slug;
		}
	}

	return '';
}

/**
 * Adds narration slugs to the body classes of a single photo.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function soc_photo_body_classes( array $classes ): array {
	if ( ! is_singular( 'photo' ) ) {
		return $classes;
	}

	foreach ( soc_get_photo_narrations() as $term ) {
		$term_class = sanitize_html_class( $term->slug );

		if ( '' !== $term_class ) {
			$classes[] = 'soc-narration-' . $term_class;
		}
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'soc_photo_body_classes' );
