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
 * Gets the short introduction of a photo series, from its excerpt.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string
 */
function soc_get_photo_intro( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	return trim( wp_strip_all_tags( get_the_excerpt( $post_id ) ) );
}

/**
 * Gets the location of a photo series.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return array{name?: string, latitude?: float, longitude?: float}
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
 * Gets the country term of a photo series (first "pays" term).
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return WP_Term|null
 */
function soc_get_photo_country( int $post_id = 0 ): ?WP_Term {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return null;
	}

	$terms = get_the_terms( $post_id, 'pays' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Gets the soc_photo_period ACF override of a photo series: the editorial
 * month/year the photographs or the trip actually happened, when it differs
 * from the post's publish date.
 *
 * Kept distinct from the WordPress publish date on purpose: a series can be
 * published today about a trip made in 2005 without antedating post_date,
 * which would break SEO signals like datePublished. The month is only
 * returned alongside a valid year — a month picked without a year would be
 * meaningless.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return array{year?: int, month?: int} Empty when no valid year override is set.
 */
function soc_get_photo_period_override( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$period = function_exists( 'get_field' ) ? get_field( 'soc_photo_period', $post_id ) : null;
	$year   = is_array( $period ) ? ( $period['year'] ?? null ) : null;

	if ( ! is_numeric( $year ) || (int) $year < 1826 || (int) $year > 2100 ) {
		return array();
	}

	$override = array( 'year' => (int) $year );
	$month    = $period['month'] ?? null;

	if ( is_numeric( $month ) && (int) $month >= 1 && (int) $month <= 12 ) {
		$override['month'] = (int) $month;
	}

	return $override;
}

/**
 * Gets the editorial year of a photo series: the soc_photo_period override
 * when set, otherwise the post's publish date.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string Four-digit year, or an empty string when unset.
 */
function soc_get_photo_year( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	$override = soc_get_photo_period_override( $post_id );

	return isset( $override['year'] ) ? (string) $override['year'] : get_the_date( 'Y', $post_id );
}

/**
 * Gets the editorial month (1-12) of a photo series, from its soc_photo_period
 * override. Never derived from the publish date: unlike the year, there is
 * no meaningful publish-date fallback for the month of an undated trip.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return int 1-12, or 0 when unset.
 */
function soc_get_photo_month( int $post_id = 0 ): int {
	$override = soc_get_photo_period_override( $post_id );

	return $override['month'] ?? 0;
}

/**
 * Gets the human-readable editorial date label of a photo series, e.g.
 * "Janv. 2005" when the month is known, or just "2005" otherwise (whether
 * from a soc_photo_period override or the fallback publish year).
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string
 */
function soc_get_photo_date_label( int $post_id = 0 ): string {
	$year = soc_get_photo_year( $post_id );

	if ( '' === $year ) {
		return '';
	}

	$month = soc_get_photo_month( $post_id );

	if ( 0 === $month ) {
		return $year;
	}

	$months = soc_get_month_abbreviations();

	return $months[ $month - 1 ] . ' ' . $year;
}

/**
 * Gets the ordered gallery images of a photo series.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return int[] Attachment IDs.
 */
function soc_get_photo_gallery_ids( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$gallery = function_exists( 'get_field' ) ? (array) get_field( 'soc_photo_gallery', $post_id ) : array();

	return array_values( array_filter( array_map( 'absint', $gallery ) ) );
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
	$count   = count( soc_get_photo_gallery_ids( $post_id ) );

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

	$gallery = soc_get_photo_gallery_ids( $post_id );

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
 * Gets the dominant color name of a photo series, e.g. "Bleu océan".
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string
 */
function soc_get_photo_color_name( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return '';
	}

	$color = function_exists( 'get_field' ) ? get_field( 'soc_photo_color', $post_id ) : null;
	$name  = is_array( $color ) && ! empty( $color['name'] ) ? $color['name'] : '';

	return is_string( $name ) ? trim( $name ) : '';
}

/**
 * Converts a hex color to its hue (0-360), for sorting on a color wheel.
 *
 * Ports the inline hue() helper of sliceofcactus-astro's
 * color-your-life.astro.
 *
 * @param string $hex Hex color, with or without a leading #.
 * @return float
 */
function soc_hex_to_hue( string $hex ): float {
	$hex = ltrim( $hex, '#' );

	if ( 6 !== strlen( $hex ) ) {
		return 0.0;
	}

	$r   = hexdec( substr( $hex, 0, 2 ) ) / 255;
	$g   = hexdec( substr( $hex, 2, 2 ) ) / 255;
	$b   = hexdec( substr( $hex, 4, 2 ) ) / 255;
	$max = max( $r, $g, $b );
	$min = min( $r, $g, $b );
	$d   = $max - $min;

	if ( 0.0 === $d ) {
		return 0.0;
	}

	if ( $max === $r ) {
		$h = fmod( ( $g - $b ) / $d, 6 );
	} elseif ( $max === $g ) {
		$h = ( $b - $r ) / $d + 2;
	} else {
		$h = ( $r - $g ) / $d + 4;
	}

	return fmod( ( $h * 60 ) + 360, 360 );
}

/**
 * Darkens a hex color by mixing it toward black.
 *
 * Used to derive --accent-deep from a photo's dominant color (soc_get_photo_color()),
 * matching the ratio of the --accent/--accent-deep pairs already hand-picked
 * per narration in single-photo.css (roughly 35% darker).
 *
 * @param string $hex    Hex color, with or without a leading #.
 * @param float  $amount Fraction to darken by, from 0 to 1.
 * @return string Hex color with a leading #, or an empty string when invalid.
 */
function soc_darken_hex( string $hex, float $amount = 0.35 ): string {
	$hex = ltrim( trim( $hex ), '#' );

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '';
	}

	$amount   = max( 0.0, min( 1.0, $amount ) );
	$darkened = array_map(
		static fn( string $channel ): string
			=> str_pad( dechex( (int) round( hexdec( $channel ) * ( 1 - $amount ) ) ), 2, '0', STR_PAD_LEFT ),
		str_split( $hex, 2 )
	);

	return '#' . implode( '', $darkened );
}

/**
 * Picks a readable text modifier ("on-light" or "on-dark") for a swatch of
 * this hex color, from its perceived (WCAG-ish) luminance. Used wherever a
 * hex value becomes its own background — a récit's soc_recit_palette
 * (template-parts/single/recit-article.php) — so the label text stays
 * legible without a hand-kept light/dark list per color.
 *
 * @param string $hex Hex color, with or without a leading #.
 * @return string "on-light", or "on-dark" as the default/fallback.
 */
function soc_get_readable_text_modifier( string $hex ): string {
	$hex = ltrim( trim( $hex ), '#' );

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return 'on-dark';
	}

	$r         = hexdec( substr( $hex, 0, 2 ) );
	$g         = hexdec( substr( $hex, 2, 2 ) );
	$b         = hexdec( substr( $hex, 4, 2 ) );
	$luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;

	return $luminance > 0.6 ? 'on-light' : 'on-dark';
}

/**
 * Gets the default accent color of a narration (soc_narration_accent ACF
 * field on the narration taxonomy), e.g. the distinct tone given to
 * "voyage" or "lifestyle" series. Lets editors add a narration and give it
 * its own color entirely from the admin, with no theme code change needed.
 *
 * @param WP_Term $term Narration term.
 * @return string Hex color, or an empty string when unset.
 */
function soc_get_narration_accent_color( WP_Term $term ): string {
	$color = function_exists( 'get_field' ) ? get_field( 'soc_narration_accent', 'narration_' . $term->term_id ) : null;

	return is_string( $color ) ? trim( $color ) : '';
}

/**
 * Gets the effective accent color of a photo series: its own soc_photo_color
 * override when set, otherwise its first narration's soc_narration_accent
 * default.
 *
 * @param int $post_id Optional photo ID. Defaults to the current post.
 * @return string Hex color, or an empty string when neither is set.
 */
function soc_get_photo_effective_accent_color( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();
	$color   = soc_get_photo_color( $post_id );

	if ( '' !== $color ) {
		return $color;
	}

	$narrations = soc_get_photo_narrations( $post_id );
	$narration  = ! empty( $narrations ) ? $narrations[0] : null;

	return $narration ? soc_get_narration_accent_color( $narration ) : '';
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

	$color = soc_get_photo_effective_accent_color();

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( '' !== $color ? $color : '#12B26A' ) );
}
add_action( 'wp_head', 'soc_photo_theme_color_meta' );

/**
 * Prints --accent/--accent-deep on the body for a single photo, from its own
 * dominant color (soc_photo_color ACF field) or, failing that, its
 * narration's default accent (soc_narration_accent ACF field on the
 * narration taxonomy). Both custom properties drive the whole-page
 * background wash in assets/styles/base/elements.css, so the override is
 * scoped to this post's body class and !important, otherwise a more
 * specific selector on the same body tag would win instead.
 */
function soc_photo_accent_style(): void {
	if ( ! is_singular( 'photo' ) ) {
		return;
	}

	$color = soc_get_photo_effective_accent_color();

	if ( '' === $color ) {
		return;
	}

	$deep = soc_darken_hex( $color );

	if ( '' === $deep ) {
		return;
	}

	printf(
		'<style>body.postid-%d{--accent:%s !important;--accent-deep:%s !important;}</style>' . "\n",
		get_the_ID(),
		esc_html( '#' . ltrim( $color, '#' ) ),
		esc_html( $deep )
	);
}
add_action( 'wp_head', 'soc_photo_accent_style' );

/**
 * Prints --accent/--accent-deep on the body for a narration term archive
 * (taxonomy-narration.php), from that term's own soc_narration_accent ACF
 * field. Falls back to the default olive tone set in archive-photo.css
 * (body.tax-narration) when the term has no accent of its own.
 */
function soc_narration_archive_accent_style(): void {
	if ( ! is_tax( 'narration' ) ) {
		return;
	}

	$term = get_queried_object();

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$color = soc_get_narration_accent_color( $term );

	if ( '' === $color ) {
		return;
	}

	$deep = soc_darken_hex( $color );

	if ( '' === $deep ) {
		return;
	}

	printf(
		'<style>body.term-%s{--accent:%s !important;--accent-deep:%s !important;}</style>' . "\n",
		esc_attr( sanitize_html_class( $term->slug, (string) $term->term_id ) ),
		esc_html( '#' . ltrim( $color, '#' ) ),
		esc_html( $deep )
	);
}
add_action( 'wp_head', 'soc_narration_archive_accent_style' );

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

	return trim( wp_strip_all_tags( get_the_excerpt( $post_id ) ) );
}

/**
 * Gets the rubrique (dessin/coloriage/couture) assigned to a creation.
 *
 * The creation_type taxonomy carries the rubrique split; medium carries the
 * finer technique (aquarelle, feutres…) — see soc_get_creation_technique_label().
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return WP_Term|null
 */
function soc_get_creation_rubrique( int $post_id = 0 ): ?WP_Term {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return null;
	}

	$terms = get_the_terms( $post_id, 'creation_type' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Gets the technique label of a creation (its first medium term).
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

	$terms = get_the_terms( $post_id, 'medium' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0]->name : '';
}

/**
 * Gets every medium (technique) term of a creation as a JSON string, for
 * the client-side filter chips.
 *
 * A creation can carry several medium terms (e.g. a série mixing feutres
 * and aquarelle) — unlike soc_get_creation_technique_label(), which only
 * ever surfaces the first one for display, the filter chips need every
 * term so a card doesn't drop out of a medium's filtered view.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string JSON array of {slug, label} objects.
 */
function soc_get_creation_mediums_json( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return '[]';
	}

	$terms = get_the_terms( $post_id, 'medium' );

	if ( ! is_array( $terms ) ) {
		return '[]';
	}

	return (string) wp_json_encode(
		array_map(
			static fn( WP_Term $term ): array => array(
				'slug'  => $term->slug,
				'label' => $term->name,
			),
			$terms
		)
	);
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
 * Gets the accent color for a rubrique slug (dessin/coloriage).
 *
 * There is no per-creation color override field (unlike Photo's
 * soc_photo_color): sliceofcactus-astro only varies this per rubrique,
 * on both single creations and the /dessin, /coloriage archives.
 *
 * @param string $rubrique_slug Rubrique (creation_type) term slug.
 * @return string Hex color.
 */
function soc_get_creation_accent_color_for_rubrique( string $rubrique_slug ): string {
	return 'coloriage' === $rubrique_slug ? '#7C3AED' : '#E0592F';
}

/**
 * Gets the accent color of a creation, based on its rubrique.
 *
 * @param int $post_id Optional creation ID. Defaults to the current post.
 * @return string Hex color.
 */
function soc_get_creation_accent_color( int $post_id = 0 ): string {
	$rubrique = soc_get_creation_rubrique( $post_id );

	return soc_get_creation_accent_color_for_rubrique( $rubrique ? $rubrique->slug : '' );
}

/**
 * Gets the rubrique slug of the current /dessin or /coloriage archive.
 *
 * Reads the queried creation_type term (native taxonomy archive), defaulting
 * to 'dessin' as a defensive fallback.
 *
 * @return string 'dessin' or 'coloriage'.
 */
function soc_get_creation_archive_rubrique(): string {
	$term = get_queried_object();

	return ( $term instanceof WP_Term && 'coloriage' === $term->slug ) ? 'coloriage' : 'dessin';
}

/**
 * Gets the native archive URL of a rubrique (dessin/coloriage), i.e. its
 * creation_type term link.
 *
 * @param string $slug 'dessin' or 'coloriage'.
 * @return string
 */
function soc_get_creation_rubrique_archive_link( string $slug ): string {
	$term = get_term_by( 'slug', $slug, 'creation_type' );

	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$link = get_term_link( $term );

	return is_string( $link ) ? $link : '';
}

/**
 * Prints the mobile browser theme-color meta tag for a single creation
 * or the /dessin, /coloriage archives.
 */
function soc_creation_theme_color_meta(): void {
	if ( is_singular( 'creation' ) ) {
		$color = soc_get_creation_accent_color();
	} elseif ( is_tax( 'creation_type' ) ) {
		$color = soc_get_creation_accent_color_for_rubrique( soc_get_creation_archive_rubrique() );
	} else {
		return;
	}

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ) );
}
add_action( 'wp_head', 'soc_creation_theme_color_meta' );

/**
 * Adds the rubrique slug to the body classes of a single creation or the
 * /dessin, /coloriage archives.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function soc_creation_body_classes( array $classes ): array {
	if ( is_singular( 'creation' ) ) {
		$rubrique = soc_get_creation_rubrique();
		$slug     = $rubrique ? $rubrique->slug : '';
	} elseif ( is_tax( 'creation_type' ) ) {
		$slug = soc_get_creation_archive_rubrique();
	} else {
		return $classes;
	}

	if ( '' !== $slug ) {
		$classes[] = 'soc-rubrique-' . sanitize_html_class( $slug );
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'soc_creation_body_classes' );

/**
 * Gets the short French month abbreviations, indexed 0 (janvier) to 11 (décembre).
 *
 * @return string[]
 */
function soc_get_month_abbreviations(): array {
	return array(
		__( 'janv.', 'sliceofcactus' ),
		__( 'févr.', 'sliceofcactus' ),
		__( 'mars', 'sliceofcactus' ),
		__( 'avr.', 'sliceofcactus' ),
		__( 'mai', 'sliceofcactus' ),
		__( 'juin', 'sliceofcactus' ),
		__( 'juil.', 'sliceofcactus' ),
		__( 'août', 'sliceofcactus' ),
		__( 'sept.', 'sliceofcactus' ),
		__( 'oct.', 'sliceofcactus' ),
		__( 'nov.', 'sliceofcactus' ),
		__( 'déc.', 'sliceofcactus' ),
	);
}

/**
 * Formats a Y-m-d date as a short French month label.
 *
 * Ports the frMonth() helper of sliceofcactus-astro's recits pages.
 *
 * @param string $date Y-m-d date string.
 * @return string
 */
function soc_format_recit_date( string $date ): string {
	$parts = explode( '-', $date );

	if ( count( $parts ) < 2 ) {
		return $date;
	}

	list( $year, $month ) = $parts;

	$months = soc_get_month_abbreviations();
	$index  = (int) $month - 1;

	return isset( $months[ $index ] ) ? $months[ $index ] . ' ' . $year : $date;
}

/**
 * Gets the raw date (Y-m-d) of a récit, from its publish date.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return string
 */
function soc_get_recit_date( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'recit' !== get_post_type( $post_id ) ) {
		return '';
	}

	return get_the_date( 'Y-m-d', $post_id );
}

/**
 * Gets the short French month label of a récit's date.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return string
 */
function soc_get_recit_date_label( int $post_id = 0 ): string {
	$date = soc_get_recit_date( $post_id );

	return '' !== $date ? soc_format_recit_date( $date ) : '';
}

/**
 * Gets the hero layout of a récit: plate (inserted in the body), cover
 * (photo as masthead background) or margin (photo tucked beside the text).
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return string
 */
function soc_get_recit_hero_layout( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();
	$layout  = function_exists( 'get_field' ) ? get_field( 'soc_recit_hero_layout', $post_id ) : '';
	$allowed = array( 'plate', 'cover', 'margin' );

	return in_array( $layout, $allowed, true ) ? $layout : 'plate';
}

/**
 * Gets the estimated reading time of a récit, from its word count at a
 * conservative 200 words/minute.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return int Minutes, at least 1 (0 if not a récit).
 */
function soc_get_recit_reading_minutes( int $post_id = 0 ): int {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'recit' !== get_post_type( $post_id ) ) {
		return 0;
	}

	$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );

	return max( 1, (int) ceil( count( $words ) / 200 ) );
}

/**
 * Gets the hero image caption of a récit: the native caption of its
 * featured image.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return string
 */
function soc_get_recit_hero_caption( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || ! has_post_thumbnail( $post_id ) ) {
		return '';
	}

	return trim( wp_get_attachment_caption( get_post_thumbnail_id( $post_id ) ) );
}

/**
 * Gets the creations associated with a récit, published only.
 *
 * Stands in for the single serie_liee link of sliceofcactus-astro's
 * recits/[id].astro: the content model links récits to creations here,
 * not directly to photo series.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return WP_Post[]
 */
function soc_get_recit_related_creations( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'recit' !== get_post_type( $post_id ) ) {
		return array();
	}

	$ids = function_exists( 'get_field' ) ? (array) get_field( 'soc_recit_creations', $post_id ) : array();
	$ids = array_filter( array_map( 'absint', $ids ) );

	return array_values(
		array_filter(
			array_map( 'get_post', $ids ),
			static fn( $post ): bool => $post instanceof WP_Post && 'publish' === $post->post_status
		)
	);
}

/**
 * Gets the photos associated with a récit, published only.
 *
 * @param int $post_id Optional récit ID. Defaults to the current post.
 * @return WP_Post[]
 */
function soc_get_recit_photos( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'recit' !== get_post_type( $post_id ) ) {
		return array();
	}

	$ids = function_exists( 'get_field' ) ? (array) get_field( 'soc_recit_photos', $post_id ) : array();
	$ids = array_filter( array_map( 'absint', $ids ) );

	return array_values(
		array_filter(
			array_map( 'get_post', $ids ),
			static fn( $post ): bool => $post instanceof WP_Post && 'publish' === $post->post_status
		)
	);
}

/**
 * Prints the mobile browser theme-color meta tag for the Récits archive
 * and single récits.
 *
 * Récits use one fixed color (unlike Photo/Création, which vary per
 * narration/medium), matching the constant themeColor prop of
 * sliceofcactus-astro's recits pages.
 */
function soc_recit_theme_color_meta(): void {
	if ( ! is_singular( 'recit' ) && ! is_post_type_archive( 'recit' ) ) {
		return;
	}

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( '#FBEEDA' ) );
}
add_action( 'wp_head', 'soc_recit_theme_color_meta' );

/**
 * Prints the mobile browser theme-color meta tag for the special Photo
 * page templates (Projet 52, Color Your Life, Carte des voyages).
 *
 * Each has one fixed color, matching the constant themeColor prop set on
 * its sliceofcactus-astro page.
 */
function soc_photo_page_template_theme_color_meta(): void {
	$colors = array(
		'page-projet-52.php'       => '#C2542E',
		'page-color-your-life.php' => '#FBEEDA',
		'page-voyage-carte.php'    => '#27513E',
	);

	foreach ( $colors as $template => $color ) {
		if ( is_page_template( $template ) ) {
			printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ) );

			return;
		}
	}
}

/**
 * Gets the front-end URL of the published page assigned a given template
 * file — e.g. the general "Résonances" overview (page-resonances.php),
 * whose slug is picked in wp-admin, not fixed in the theme.
 *
 * @param string $template Template file name, e.g. 'page-resonances.php'.
 * @return string Permalink, or '' if no published page uses that template.
 */
function soc_get_page_url_by_template( string $template ): string {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
			'no_found_rows'  => true,
		)
	);

	if ( empty( $pages ) ) {
		return '';
	}

	$link = get_permalink( $pages[0] );

	return is_string( $link ) ? $link : '';
}
add_action( 'wp_head', 'soc_photo_page_template_theme_color_meta' );
