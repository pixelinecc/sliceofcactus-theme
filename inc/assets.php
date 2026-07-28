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

	if ( is_singular( 'photo' ) ) {
		$photo_style_path = get_theme_file_path( '/assets/styles/templates/single-photo.css' );

		if ( is_readable( $photo_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-photo',
				get_theme_file_uri( '/assets/styles/templates/single-photo.css' ),
				array( 'sliceofcactus' ),
				(string) filemtime( $photo_style_path )
			);
		}
	}

	if ( is_post_type_archive( 'photo' ) ) {
		$photo_archive_style_path = get_theme_file_path( '/assets/styles/templates/archive-photo.css' );

		if ( is_readable( $photo_archive_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-archive-photo',
				get_theme_file_uri( '/assets/styles/templates/archive-photo.css' ),
				array( 'sliceofcactus' ),
				(string) filemtime( $photo_archive_style_path )
			);
		}
	}

	$script_path = get_theme_file_path( '/assets/scripts/main.js' );

	if ( is_readable( $script_path ) ) {
		wp_enqueue_script(
			'sliceofcactus-interactions',
			get_theme_file_uri( '/assets/scripts/main.js' ),
			array(),
			(string) filemtime( $script_path ),
			true
		);
		wp_script_add_data( 'sliceofcactus-interactions', 'strategy', 'defer' );
	}

	if ( is_singular( 'photo' ) ) {
		$photo_script_path = get_theme_file_path( '/assets/scripts/single-photo.js' );

		if ( is_readable( $photo_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-single-photo',
				get_theme_file_uri( '/assets/scripts/single-photo.js' ),
				array(),
				(string) filemtime( $photo_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-single-photo', 'strategy', 'defer' );
		}
	}

	if ( is_post_type_archive( 'photo' ) ) {
		$photo_archive_script_path = get_theme_file_path( '/assets/scripts/archive-photo.js' );

		if ( is_readable( $photo_archive_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-archive-photo',
				get_theme_file_uri( '/assets/scripts/archive-photo.js' ),
				array(),
				(string) filemtime( $photo_archive_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-archive-photo', 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'soc_enqueue_assets' );
