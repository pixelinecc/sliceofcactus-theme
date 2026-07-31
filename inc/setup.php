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
			'primary'           => __( 'Navigation principale', 'sliceofcactus' ),
			'footer_photo'      => __( 'Pied de page — Photo', 'sliceofcactus' ),
			'footer_dessin'     => __( 'Pied de page — Dessin', 'sliceofcactus' ),
			'footer_read'       => __( 'Pied de page — À lire et suivre', 'sliceofcactus' ),
			'footer_resonances' => __( 'Pied de page — Résonances', 'sliceofcactus' ),
			'footer_legal'      => __( 'Pied de page — Informations légales', 'sliceofcactus' ),
		)
	);
}
add_action( 'after_setup_theme', 'soc_setup' );

/**
 * Disables WordPress 6.7's automatic sizes="auto" on lazy-loaded images.
 *
 * Every card grid in this theme (contact-sheet poses, colo-card, cyl-card,
 * more-series__card, hr-card...) crops a same-aspect-ratio image to a fixed
 * box with CSS (aspect-ratio + object-fit: cover) far narrower than the
 * image's native width — never at the near-full-viewport width core assumes.
 * "auto" makes supporting browsers measure the <img>'s box before the CSS
 * grid has finished laying out sibling tracks, locking in an undersized
 * srcset candidate that then gets stretched by object-fit and blurs.
 */
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );
