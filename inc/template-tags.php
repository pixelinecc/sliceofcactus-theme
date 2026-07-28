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
 * Gets the number of poses (images) of a photo series.
 *
 * Counts the soc_photo_gallery images, matching the single-photo template,
 * falling back to 1 when only a featured image stands in for the gallery.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return int
 */
function soc_get_photo_pose_count( int $post_id = 0 ): int {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return 0;
	}

	$gallery = function_exists( 'get_field' ) ? (array) get_field( 'soc_photo_gallery', $post_id ) : array();
	$count   = count( array_filter( array_map( 'absint', $gallery ) ) );

	return $count > 0 ? $count : ( has_post_thumbnail( $post_id ) ? 1 : 0 );
}

/**
 * Gets the cover image of a photo series: the featured image if set,
 * otherwise the first soc_photo_gallery image.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return int Attachment ID, or 0 when the series has no image at all.
 */
function soc_get_photo_cover_id( int $post_id = 0 ): int {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return 0;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		return (int) get_post_thumbnail_id( $post_id );
	}

	$gallery = function_exists( 'get_field' ) ? (array) get_field( 'soc_photo_gallery', $post_id ) : array();
	$gallery = array_filter( array_map( 'absint', $gallery ) );

	return ! empty( $gallery ) ? (int) reset( $gallery ) : 0;
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

/**
 * Gets the short introduction of a creation.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string
 */
function soc_get_creation_intro( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return '';
	}

	$intro = function_exists( 'get_field' )
		? get_field( 'soc_creation_intro', $post_id )
		: get_post_meta( $post_id, 'soc_creation_intro', true );

	return is_string( $intro ) ? trim( $intro ) : '';
}

/**
 * Gets the medium (dessin/coloriage) assigned to a creation.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return WP_Term|null
 */
function soc_get_creation_medium( int $post_id = 0 ): ?WP_Term {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return null;
	}

	$terms = get_the_terms( $post_id, 'medium' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Gets the technique label of a creation (its first creation_type term).
 *
 * Stands in for the two-field `technique` object of sliceofcactus-astro's
 * dessin pages: one WP taxonomy term name covers both the drop-cap source
 * and the "Technique : …" sentence.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string
 */
function soc_get_creation_technique_label( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return '';
	}

	$terms = get_the_terms( $post_id, 'creation_type' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0]->name : '';
}

/**
 * Gets the book/carnet info of a creation (coloriage only).
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return array{title?: string, author?: string, publisher?: string, cover?: int}
 */
function soc_get_creation_book( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return array();
	}

	$book = function_exists( 'get_field' ) ? get_field( 'soc_creation_book', $post_id ) : null;

	return is_array( $book ) ? $book : array();
}

/**
 * Gets the ordered gallery images of a creation.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return int[] Attachment IDs.
 */
function soc_get_creation_gallery_ids( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return array();
	}

	$gallery = function_exists( 'get_field' ) ? (array) get_field( 'soc_creation_previews', $post_id ) : array();

	return array_values( array_filter( array_map( 'absint', $gallery ) ) );
}

/**
 * Gets the cover image of a creation: the featured image if set,
 * otherwise the first soc_creation_previews image.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return int Attachment ID, or 0 when the creation has no image at all.
 */
function soc_get_creation_cover_id( int $post_id = 0 ): int {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return 0;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		return (int) get_post_thumbnail_id( $post_id );
	}

	$gallery = soc_get_creation_gallery_ids( $post_id );

	return ! empty( $gallery ) ? (int) reset( $gallery ) : 0;
}

/**
 * Gets the accent color for a medium slug (dessin/coloriage).
 *
 * There is no per-creation color override field (unlike Photo's
 * soc_photo_color): sliceofcactus-astro only varies this per medium,
 * on both single creations and the /dessin, /coloriage archives.
 *
 * @param string $medium_slug Medium term slug.
 * @return string Hex color.
 */
function soc_get_creation_accent_color_for_medium( string $medium_slug ): string {
	return 'coloriage' === $medium_slug ? '#7C3AED' : '#E0592F';
}

/**
 * Gets the accent color of a creation, based on its medium.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string Hex color.
 */
function soc_get_creation_accent_color( int $post_id = 0 ): string {
	$medium = soc_get_creation_medium( $post_id );

	return soc_get_creation_accent_color_for_medium( $medium ? $medium->slug : '' );
}

/**
 * Gets the medium slug of the current /dessin or /coloriage archive.
 *
 * Reads the queried medium term (native taxonomy archive), defaulting to
 * 'dessin' as a defensive fallback.
 *
 * @return string 'dessin' or 'coloriage'.
 */
function soc_get_creation_archive_medium(): string {
	$term = get_queried_object();

	return ( $term instanceof WP_Term && 'coloriage' === $term->slug ) ? 'coloriage' : 'dessin';
}

/**
 * Prints the mobile browser theme-color meta tag for a single creation
 * or the /dessin, /coloriage archives.
 */
function soc_creation_theme_color_meta(): void {
	if ( is_singular( 'creation' ) ) {
		$color = soc_get_creation_accent_color();
	} elseif ( is_tax( 'medium' ) ) {
		$color = soc_get_creation_accent_color_for_medium( soc_get_creation_archive_medium() );
	} else {
		return;
	}

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ) );
}
add_action( 'wp_head', 'soc_creation_theme_color_meta' );

/**
 * Adds the medium slug to the body classes of a single creation or the
 * /dessin, /coloriage archives.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function soc_creation_body_classes( array $classes ): array {
	if ( is_singular( 'creation' ) ) {
		$medium = soc_get_creation_medium();
		$slug   = $medium ? $medium->slug : '';
	} elseif ( is_tax( 'medium' ) ) {
		$slug = soc_get_creation_archive_medium();
	} else {
		return $classes;
	}

	if ( '' !== $slug ) {
		$classes[] = 'soc-medium-' . sanitize_html_class( $slug );
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'soc_creation_body_classes' );
