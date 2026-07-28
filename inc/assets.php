<?php
/**
 * Public and editor assets.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues the public design-system stylesheets.
 *
 * @return void
 */
function soc_enqueue_assets(): void {
	$stylesheet_path = get_theme_file_path( '/assets/styles/main.css' );
	$style_version   = is_readable( $stylesheet_path )
		? (string) filemtime( $stylesheet_path )
		: wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'sliceofcactus',
		get_theme_file_uri( '/assets/styles/main.css' ),
		array(),
		$style_version
	);

	$script_path = get_theme_file_path( '/assets/scripts/main.js' );

	if ( ! is_readable( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'sliceofcactus-interactions',
		get_theme_file_uri( '/assets/scripts/main.js' ),
		array(),
		(string) filemtime( $script_path ),
		true
	);
	wp_script_add_data( 'sliceofcactus-interactions', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'soc_enqueue_assets' );
