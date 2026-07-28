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

	$needs_magazine_hub = is_singular( 'photo' ) || is_singular( 'creation' ) || is_singular( 'recit' ) || is_post_type_archive( 'photo' ) || is_post_type_archive( 'recit' ) || is_tax( 'medium' );
	$magazine_hub_deps  = array( 'sliceofcactus' );

	if ( $needs_magazine_hub ) {
		$magazine_hub_style_path = get_theme_file_path( '/assets/styles/components/magazine-hub.css' );

		if ( is_readable( $magazine_hub_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-magazine-hub',
				get_theme_file_uri( '/assets/styles/components/magazine-hub.css' ),
				array( 'sliceofcactus' ),
				(string) filemtime( $magazine_hub_style_path )
			);
			$magazine_hub_deps[] = 'sliceofcactus-magazine-hub';
		}
	}

	if ( is_singular( 'photo' ) ) {
		$photo_style_path = get_theme_file_path( '/assets/styles/templates/single-photo.css' );

		if ( is_readable( $photo_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-photo',
				get_theme_file_uri( '/assets/styles/templates/single-photo.css' ),
				$magazine_hub_deps,
				(string) filemtime( $photo_style_path )
			);
		}
	}

	$creation_deps = $magazine_hub_deps;

	if ( is_singular( 'creation' ) || is_tax( 'medium' ) ) {
		$creation_style_path = get_theme_file_path( '/assets/styles/templates/single-creation.css' );

		if ( is_readable( $creation_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-creation',
				get_theme_file_uri( '/assets/styles/templates/single-creation.css' ),
				$magazine_hub_deps,
				(string) filemtime( $creation_style_path )
			);
			$creation_deps[] = 'sliceofcactus-single-creation';
		}
	}

	if ( is_post_type_archive( 'photo' ) ) {
		$photo_archive_style_path = get_theme_file_path( '/assets/styles/templates/archive-photo.css' );

		if ( is_readable( $photo_archive_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-archive-photo',
				get_theme_file_uri( '/assets/styles/templates/archive-photo.css' ),
				$magazine_hub_deps,
				(string) filemtime( $photo_archive_style_path )
			);
		}
	}

	if ( is_tax( 'medium' ) ) {
		$medium_archive_style_path = get_theme_file_path( '/assets/styles/templates/taxonomy-medium.css' );

		if ( is_readable( $medium_archive_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-taxonomy-medium',
				get_theme_file_uri( '/assets/styles/templates/taxonomy-medium.css' ),
				$creation_deps,
				(string) filemtime( $medium_archive_style_path )
			);
		}
	}

	$recit_deps = $magazine_hub_deps;

	if ( is_singular( 'recit' ) || is_post_type_archive( 'recit' ) ) {
		$recit_archive_style_path = get_theme_file_path( '/assets/styles/templates/archive-recit.css' );

		if ( is_readable( $recit_archive_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-archive-recit',
				get_theme_file_uri( '/assets/styles/templates/archive-recit.css' ),
				$magazine_hub_deps,
				(string) filemtime( $recit_archive_style_path )
			);
			$recit_deps[] = 'sliceofcactus-archive-recit';
		}
	}

	if ( is_singular( 'recit' ) ) {
		$recit_style_path = get_theme_file_path( '/assets/styles/templates/single-recit.css' );

		if ( is_readable( $recit_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-recit',
				get_theme_file_uri( '/assets/styles/templates/single-recit.css' ),
				$recit_deps,
				(string) filemtime( $recit_style_path )
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

	if ( is_singular( 'creation' ) ) {
		$creation_script_path = get_theme_file_path( '/assets/scripts/single-creation.js' );

		if ( is_readable( $creation_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-single-creation',
				get_theme_file_uri( '/assets/scripts/single-creation.js' ),
				array(),
				(string) filemtime( $creation_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-single-creation', 'strategy', 'defer' );
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
