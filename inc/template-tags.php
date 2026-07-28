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
