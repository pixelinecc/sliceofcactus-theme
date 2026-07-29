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

	$needs_magazine_hub = is_singular( 'photo' ) || is_singular( 'creation' ) || is_singular( 'recit' ) || is_post_type_archive( 'photo' ) || is_post_type_archive( 'creation' ) || is_post_type_archive( 'recit' ) || is_tax( 'creation_type' ) || is_tax( 'resonance' ) || is_page_template( 'page-projet-52.php' ) || is_page_template( 'page-color-your-life.php' ) || is_page_template( 'page-voyage-carte.php' );
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

	$needs_lightbox = is_singular( 'photo' ) || is_singular( 'creation' ) || is_front_page();
	$lightbox_deps  = array( 'sliceofcactus' );

	if ( $needs_lightbox ) {
		$lightbox_style_path = get_theme_file_path( '/assets/styles/components/lightbox.css' );

		if ( is_readable( $lightbox_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-lightbox',
				get_theme_file_uri( '/assets/styles/components/lightbox.css' ),
				array( 'sliceofcactus' ),
				(string) filemtime( $lightbox_style_path )
			);
			$lightbox_deps[] = 'sliceofcactus-lightbox';
		}
	}

	if ( is_singular( 'photo' ) ) {
		$photo_style_path = get_theme_file_path( '/assets/styles/templates/single-photo.css' );

		if ( is_readable( $photo_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-photo',
				get_theme_file_uri( '/assets/styles/templates/single-photo.css' ),
				array_merge( $magazine_hub_deps, $lightbox_deps ),
				(string) filemtime( $photo_style_path )
			);
		}
	}

	$creation_deps = $magazine_hub_deps;

	if ( is_singular( 'creation' ) || is_tax( 'creation_type' ) ) {
		$creation_style_path = get_theme_file_path( '/assets/styles/templates/single-creation.css' );

		if ( is_readable( $creation_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-single-creation',
				get_theme_file_uri( '/assets/styles/templates/single-creation.css' ),
				array_merge( $magazine_hub_deps, $lightbox_deps ),
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

	if ( is_tax( 'creation_type' ) || is_post_type_archive( 'creation' ) ) {
		$creation_type_archive_style_path = get_theme_file_path( '/assets/styles/templates/taxonomy-creation-type.css' );

		if ( is_readable( $creation_type_archive_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-taxonomy-creation-type',
				get_theme_file_uri( '/assets/styles/templates/taxonomy-creation-type.css' ),
				$creation_deps,
				(string) filemtime( $creation_type_archive_style_path )
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

	if ( is_page_template( 'page-projet-52.php' ) ) {
		$p52_style_path = get_theme_file_path( '/assets/styles/templates/page-projet-52.css' );

		if ( is_readable( $p52_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-page-projet-52',
				get_theme_file_uri( '/assets/styles/templates/page-projet-52.css' ),
				$magazine_hub_deps,
				(string) filemtime( $p52_style_path )
			);
		}
	}

	if ( is_page_template( 'page-color-your-life.php' ) ) {
		$cyl_style_path = get_theme_file_path( '/assets/styles/templates/page-color-your-life.css' );

		if ( is_readable( $cyl_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-page-color-your-life',
				get_theme_file_uri( '/assets/styles/templates/page-color-your-life.css' ),
				$magazine_hub_deps,
				(string) filemtime( $cyl_style_path )
			);
		}
	}

	if ( is_page_template( 'page-voyage-carte.php' ) ) {
		wp_enqueue_style(
			'leaflet',
			'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
			array(),
			'1.9.4'
		);

		$carte_style_path = get_theme_file_path( '/assets/styles/templates/page-voyage-carte.css' );

		if ( is_readable( $carte_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-page-voyage-carte',
				get_theme_file_uri( '/assets/styles/templates/page-voyage-carte.css' ),
				array_merge( $magazine_hub_deps, array( 'leaflet' ) ),
				(string) filemtime( $carte_style_path )
			);
		}
	}

	if ( is_front_page() ) {
		$front_page_style_path = get_theme_file_path( '/assets/styles/templates/front-page.css' );

		if ( is_readable( $front_page_style_path ) ) {
			wp_enqueue_style(
				'sliceofcactus-front-page',
				get_theme_file_uri( '/assets/styles/templates/front-page.css' ),
				$lightbox_deps,
				(string) filemtime( $front_page_style_path )
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

	if ( is_post_type_archive( 'creation' ) ) {
		$creation_archive_script_path = get_theme_file_path( '/assets/scripts/archive-creation.js' );

		if ( is_readable( $creation_archive_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-archive-creation',
				get_theme_file_uri( '/assets/scripts/archive-creation.js' ),
				array(),
				(string) filemtime( $creation_archive_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-archive-creation', 'strategy', 'defer' );
		}
	}

	if ( is_page_template( 'page-projet-52.php' ) ) {
		$p52_script_path = get_theme_file_path( '/assets/scripts/page-projet-52.js' );

		if ( is_readable( $p52_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-page-projet-52',
				get_theme_file_uri( '/assets/scripts/page-projet-52.js' ),
				array(),
				(string) filemtime( $p52_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-page-projet-52', 'strategy', 'defer' );
		}
	}

	if ( is_page_template( 'page-color-your-life.php' ) ) {
		$cyl_script_path = get_theme_file_path( '/assets/scripts/page-color-your-life.js' );

		if ( is_readable( $cyl_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-page-color-your-life',
				get_theme_file_uri( '/assets/scripts/page-color-your-life.js' ),
				array(),
				(string) filemtime( $cyl_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-page-color-your-life', 'strategy', 'defer' );
		}
	}

	if ( is_page_template( 'page-voyage-carte.php' ) ) {
		wp_enqueue_script(
			'leaflet',
			'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
			array(),
			'1.9.4',
			true
		);

		$carte_script_path = get_theme_file_path( '/assets/scripts/page-voyage-carte.js' );

		if ( is_readable( $carte_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-page-voyage-carte',
				get_theme_file_uri( '/assets/scripts/page-voyage-carte.js' ),
				array( 'leaflet' ),
				(string) filemtime( $carte_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-page-voyage-carte', 'strategy', 'defer' );
		}
	}

	if ( is_front_page() ) {
		$front_page_script_path = get_theme_file_path( '/assets/scripts/front-page.js' );

		if ( is_readable( $front_page_script_path ) ) {
			wp_enqueue_script(
				'sliceofcactus-front-page',
				get_theme_file_uri( '/assets/scripts/front-page.js' ),
				array(),
				(string) filemtime( $front_page_script_path ),
				true
			);
			wp_script_add_data( 'sliceofcactus-front-page', 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'soc_enqueue_assets' );
