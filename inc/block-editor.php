<?php
/**
 * Block editor configuration.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the project category without restricting WordPress core blocks.
 *
 * @param array[] $categories Existing block categories.
 * @return array[]
 */
function soc_register_block_category( array $categories ): array {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'sliceofcactus' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'sliceofcactus',
			'title' => __( 'Slice of Cactus', 'sliceofcactus' ),
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'soc_register_block_category' );
