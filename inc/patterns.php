<?php
/**
 * Block pattern registration.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the project pattern category.
 *
 * WordPress discovers valid pattern files placed in the theme /patterns
 * directory. Keeping that native discovery avoids a second registry here.
 *
 * @return void
 */
function soc_register_pattern_category(): void {
	register_block_pattern_category(
		'sliceofcactus',
		array(
			'label' => __( 'Slice of Cactus', 'sliceofcactus' ),
		)
	);
}
add_action( 'init', 'soc_register_pattern_category', 5 );
