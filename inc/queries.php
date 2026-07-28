<?php
/**
 * Reusable editorial queries.
 *
 * Ce module est réservé aux requêtes concernant les récits liés,
 * les collections, les créations, les résonances et la poursuite
 * de l’exploration. Les requêtes seront ajoutées uniquement lorsque
 * leurs règles de sélection seront confirmées.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the photos displayed after a single photo.
 *
 * A manually selected soc_photo_next item is kept first. Remaining positions
 * reproduce the exploratory suggestions of the Astro source.
 *
 * @param int $post_id Optional current photo ID.
 * @param int $limit   Maximum number of results.
 * @return WP_Post[]
 */
function soc_get_photo_suggestions( int $post_id = 0, int $limit = 6 ): array {
	$post_id = $post_id ?: get_the_ID();
	$limit   = max( 0, min( 12, $limit ) );

	if ( ! $post_id || 0 === $limit || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$suggestion_ids = array();
	$manual_next    = get_post_meta( $post_id, 'soc_photo_next', true );

	if ( ! is_array( $manual_next ) ) {
		$manual_next = $manual_next ? array( $manual_next ) : array();
	}

	foreach ( $manual_next as $next_id ) {
		$next_id = absint( $next_id );

		if (
			$next_id
			&& $next_id !== $post_id
			&& 'photo' === get_post_type( $next_id )
			&& 'publish' === get_post_status( $next_id )
		) {
			$suggestion_ids[] = $next_id;
			break;
		}
	}

	$remaining = $limit - count( $suggestion_ids );

	if ( $remaining > 0 ) {
		$random_ids = get_posts(
			array(
				'post_type'           => 'photo',
				'post_status'         => 'publish',
				'posts_per_page'      => $remaining,
				'post__not_in'        => array_merge( array( $post_id ), $suggestion_ids ),
				'orderby'             => 'rand',
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'suppress_filters'    => false,
			)
		);

		$suggestion_ids = array_merge( $suggestion_ids, array_map( 'absint', $random_ids ) );
	}

	return array_values(
		array_filter(
			array_map( 'get_post', $suggestion_ids ),
			static fn( $post ): bool => $post instanceof WP_Post
		)
	);
}

/**
 * Gets the photo series shown on the Photo archive.
 *
 * Mirrors the RUBRIQUES filter of sliceofcactus-astro/src/pages/photo/index.astro:
 * every narration except the two standalone collections (Projet 52, Color Your Life),
 * limited to series with a cover image, most recent project date first.
 *
 * @return WP_Post[]
 */
function soc_get_photo_archive_series(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'slug',
					'terms'    => array( 'projet-52', 'color-your-life' ),
					'operator' => 'NOT IN',
				),
			),
		)
	);

	$photos = array_values(
		array_filter(
			$query->posts,
			static fn( WP_Post $photo ): bool => soc_get_photo_cover_id( $photo->ID ) > 0
		)
	);

	usort(
		$photos,
		static function ( WP_Post $a, WP_Post $b ): int {
			$date_a = function_exists( 'get_field' )
				? get_field( 'soc_photo_date', $a->ID )
				: get_post_meta( $a->ID, 'soc_photo_date', true );
			$date_b = function_exists( 'get_field' )
				? get_field( 'soc_photo_date', $b->ID )
				: get_post_meta( $b->ID, 'soc_photo_date', true );

			return strcmp( (string) $date_b, (string) $date_a );
		}
	);

	return $photos;
}

/**
 * Gets the creations displayed after a single creation.
 *
 * Mirrors the `others` list of sliceofcactus-astro's dessin/[id].astro and
 * coloriage/[id].astro: every other creation sharing the same medium, with
 * no limit or shuffling. A manually selected soc_creation_next item is
 * kept first, as with soc_get_photo_suggestions.
 *
 * @param int $post_id Optional current creation ID.
 * @return WP_Post[]
 */
function soc_get_creation_suggestions( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return array();
	}

	$medium = soc_get_creation_medium( $post_id );

	if ( ! $medium ) {
		return array();
	}

	$suggestion_ids = array();
	$manual_next    = get_post_meta( $post_id, 'soc_creation_next', true );

	if ( ! is_array( $manual_next ) ) {
		$manual_next = $manual_next ? array( $manual_next ) : array();
	}

	foreach ( $manual_next as $next_id ) {
		$next_id = absint( $next_id );

		if (
			$next_id
			&& $next_id !== $post_id
			&& 'creation' === get_post_type( $next_id )
			&& 'publish' === get_post_status( $next_id )
		) {
			$suggestion_ids[] = $next_id;
			break;
		}
	}

	$others = get_posts(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'post__not_in'        => array_merge( array( $post_id ), $suggestion_ids ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'medium',
					'field'    => 'term_id',
					'terms'    => array( $medium->term_id ),
				),
			),
		)
	);

	$suggestion_ids = array_merge( $suggestion_ids, array_map( 'absint', $others ) );

	return array_values(
		array_filter(
			array_map( 'get_post', $suggestion_ids ),
			static fn( $post ): bool => $post instanceof WP_Post
		)
	);
}

/**
 * Gets the creations shown on the Dessin/Coloriage archive.
 *
 * Mirrors sliceofcactus-astro's dessin/index.astro and coloriage/index.astro:
 * every creation of the given medium, most recent first. Coloriages are
 * additionally required to have a cover image, matching Astro's
 * `s.rubrique === 'coloriage' && s.couverture` filter — dessins have no such
 * requirement.
 *
 * @param string $medium_slug Medium term slug: 'dessin' or 'coloriage'.
 * @return WP_Post[]
 */
function soc_get_creation_archive_items( string $medium_slug ): array {
	$term = get_term_by( 'slug', $medium_slug, 'medium' );

	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'medium',
					'field'    => 'term_id',
					'terms'    => array( $term->term_id ),
				),
			),
		)
	);

	$items = $query->posts;

	if ( 'coloriage' === $medium_slug ) {
		$items = array_values(
			array_filter(
				$items,
				static fn( WP_Post $item ): bool => soc_get_creation_cover_id( $item->ID ) > 0
			)
		);
	}

	return $items;
}

/**
 * Gets the récits shown on the Récits archive, most recent first.
 *
 * Mirrors sliceofcactus-astro's recits/index.astro: the JSON array is
 * already most-recent-first, its first item running as "la une".
 *
 * @return WP_Post[]
 */
function soc_get_recit_archive_items(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'recit',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	$recits = $query->posts;

	usort(
		$recits,
		static function ( WP_Post $a, WP_Post $b ): int {
			$date_a = function_exists( 'get_field' )
				? get_field( 'soc_recit_date', $a->ID )
				: get_post_meta( $a->ID, 'soc_recit_date', true );
			$date_b = function_exists( 'get_field' )
				? get_field( 'soc_recit_date', $b->ID )
				: get_post_meta( $b->ID, 'soc_recit_date', true );

			return strcmp( (string) $date_b, (string) $date_a );
		}
	);

	return $recits;
}
