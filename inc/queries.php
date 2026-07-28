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
