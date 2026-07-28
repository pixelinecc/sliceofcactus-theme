<?php
/**
 * Site header.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name              = get_bloginfo( 'name' );
$site_name_words        = preg_split( '/\s+/u', trim( $site_name ), -1, PREG_SPLIT_NO_EMPTY );
$has_primary_navigation = has_nav_menu( 'primary' );

if ( empty( $site_name_words ) ) {
	$site_name_words = array( $site_name );
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="cursor" id="cursor"></div>
<a class="soc-skip-link" href="#main-content">
	<?php esc_html_e( 'Aller au contenu', 'sliceofcactus' ); ?>
</a>
<header class="soc-site-header nav" id="soc-site-header">
	<div class="soc-site-brand">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a
				class="nav__logo"
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				rel="home"
				aria-label="<?php echo esc_attr( $site_name ); ?>"
				<?php echo is_front_page() ? 'aria-current="page"' : ''; ?>
			>
				<span class="nav__logo-text" aria-hidden="true">
					<?php foreach ( $site_name_words as $word_index => $site_name_word ) : ?>
						<span<?php echo 1 === $word_index ? ' class="is-outline"' : ''; ?>>
							<?php echo esc_html( $site_name_word ); ?>
						</span>
					<?php endforeach; ?>
				</span>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( $has_primary_navigation ) : ?>
		<nav
			class="soc-primary-navigation nav__links"
			id="soc-primary-navigation"
			aria-label="<?php esc_attr_e( 'Navigation principale', 'sliceofcactus' ); ?>"
		>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_id'        => 'soc-primary-menu',
					'menu_class'     => 'soc-primary-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<button
			class="nav__burger"
			id="soc-menu-toggle"
			type="button"
			aria-expanded="false"
			aria-controls="soc-primary-navigation"
			aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'sliceofcactus' ); ?>"
			data-label-open="<?php esc_attr_e( 'Ouvrir le menu', 'sliceofcactus' ); ?>"
			data-label-close="<?php esc_attr_e( 'Fermer le menu', 'sliceofcactus' ); ?>"
		>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</button>
	<?php endif; ?>
</header>
