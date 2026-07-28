<?php
/**
 * Gutenberg block registration.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers every valid block metadata file found in the theme blocks directory.
 *
 * A block is ignored when its metadata cannot be read or when another provider
 * has already registered the same block name.
 *
 * @return void
 */
function soc_register_blocks(): void {
	$metadata_files = glob( get_theme_file_path( '/blocks/*/block.json' ) );

	if ( ! is_array( $metadata_files ) ) {
		return;
	}

	$registry = WP_Block_Type_Registry::get_instance();

	foreach ( $metadata_files as $metadata_file ) {
		if ( ! is_readable( $metadata_file ) ) {
			continue;
		}

		$metadata = json_decode(
			(string) file_get_contents( $metadata_file ),
			true
		);

		if (
			! is_array( $metadata )
			|| empty( $metadata['name'] )
			|| ! is_string( $metadata['name'] )
			|| $registry->is_registered( $metadata['name'] )
		) {
			continue;
		}

		register_block_type( dirname( $metadata_file ) );
	}
}
add_action( 'init', 'soc_register_blocks' );
