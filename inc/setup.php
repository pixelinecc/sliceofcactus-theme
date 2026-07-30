<?php
/**
 * Theme setup.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers theme features and navigation locations.
 *
 * @return void
 */
function soc_setup(): void {
	load_theme_textdomain( 'sliceofcactus', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	// Gives bespoke page templates (e.g. page-a-propos.php) a native, editable
	// masthead subtitle without a dedicated ACF field.
	add_post_type_support( 'page', 'excerpt' );
	add_editor_style(
		array(
			'assets/styles/settings/tokens.css',
			'assets/styles/base/reset.css',
			'assets/styles/base/typography.css',
			'assets/styles/base/elements.css',
			'assets/styles/layout/containers.css',
			'assets/styles/utilities/utilities.css',
			'assets/styles/editor.css',
		)
	);

	add_theme_support(
		'html5',
		array(
			'gallery',
			'caption',
			'search-form',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary'        => __( 'Navigation principale', 'sliceofcactus' ),
			'footer_photo'   => __( 'Pied de page — Photo', 'sliceofcactus' ),
			'footer_dessin'  => __( 'Pied de page — Dessin', 'sliceofcactus' ),
			'footer_read'    => __( 'Pied de page — À lire et suivre', 'sliceofcactus' ),
			'footer_legal'   => __( 'Pied de page — Informations légales', 'sliceofcactus' ),
		)
	);
}
add_action( 'after_setup_theme', 'soc_setup' );
