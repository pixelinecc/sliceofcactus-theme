<?php
/**
 * Site footer.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name        = get_bloginfo( 'name' );
$site_name_words  = preg_split( '/\s+/u', trim( $site_name ), -1, PREG_SPLIT_NO_EMPTY );
$footer_columns   = array(
	array(
		'location' => 'footer_photo',
		'title'    => __( 'Photo', 'sliceofcactus' ),
	),
	array(
		'location' => 'footer_dessin',
		'title'    => __( 'Atelier', 'sliceofcactus' ),
	),
	array(
		'location' => 'footer_read',
		'title'    => __( 'Récits', 'sliceofcactus' ),
	),
	array(
		'location' => 'footer_resonances',
		'title'    => __( 'Résonances', 'sliceofcactus' ),
	),
);
$contact_email    = 'bonjour@sliceofcactus.fr';
$instagram_url    = 'https://www.instagram.com/sliceofcactus/';
$instagramdessin_url    = 'https://www.instagram.com/traitducameleon/';
$latest_recits    = soc_get_home_recits( 1 );
$latest_recit     = ! empty( $latest_recits ) ? $latest_recits[0] : null;

if ( empty( $site_name_words ) ) {
	$site_name_words = array( $site_name );
}
?>
<footer class="footer" id="contact">
	<div class="footer__top">
		<div class="footer__brand">
			<a
				class="footer__logo"
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				rel="home"
				aria-label="<?php echo esc_attr( $site_name ); ?>"
				<?php echo is_front_page() ? 'aria-current="page"' : ''; ?>
			>
				<span class="footer__logo-text" aria-hidden="true">
					<?php foreach ( $site_name_words as $word_index => $site_name_word ) : ?>
						<span<?php echo 1 === $word_index ? ' class="is-outline"' : ''; ?>>
							<?php echo esc_html( $site_name_word ); ?>
						</span>
					<?php endforeach; ?>
				</span>
			</a>

			<p class="footer__tag">
				Atelier d'images — photo <em>36 poses</em>, dessin &amp; coloriage.
			</p>
		</div>

		<nav class="footer__nav" aria-label="<?php esc_attr_e( 'Navigation du pied de page', 'sliceofcactus' ); ?>">
			<?php foreach ( $footer_columns as $footer_column ) : ?>
				<div class="footer__col">
					<h2 class="footer__heading"><?php echo esc_html( $footer_column['title'] ); ?></h2>

					<?php if ( has_nav_menu( $footer_column['location'] ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $footer_column['location'],
								'container'      => false,
								'menu_id'        => 'menu-' . str_replace( '_', '-', $footer_column['location'] ),
								'menu_class'     => 'footer__menu',
								'fallback_cb'    => false,
								'depth'          => 1,
							)
						);
						?>
					<?php endif; ?>

					<?php if ( 'footer_read' === $footer_column['location'] && $latest_recit instanceof WP_Post ) : ?>
						<a href="<?php echo esc_url( get_permalink( $latest_recit ) ); ?>">
							<?php echo esc_html( get_the_title( $latest_recit ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</nav>
	</div>

	<div class="footer__bottom">
		<span>
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?>
			<?php echo esc_html( $site_name ); ?> · atelier d'images
		</span>

		<a class="footer__bottom-link" href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>">
			<?php esc_html_e( 'Écrire ↗', 'sliceofcactus' ); ?>
		</a>

		<span class="footer__social-inline">
			<a class="footer__bottom-link" href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Instagram Photo ↗', 'sliceofcactus' ); ?>
			</a>
			<span aria-hidden="true">·</span>
			<a class="footer__bottom-link" href="<?php echo esc_url( $instagramdessin_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Instagram Dessin ↗', 'sliceofcactus' ); ?>
			</a>
		</span>

		<div class="footer__utilities">
			<?php if ( has_nav_menu( 'footer_legal' ) ) : ?>
				<nav
					class="footer__legal-navigation"
					aria-label="<?php esc_attr_e( 'Informations légales', 'sliceofcactus' ); ?>"
				>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_legal',
						'container'      => false,
						'menu_id'        => 'menu-footer-legal',
						'menu_class'     => 'footer__legal-menu',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
				</nav>
			<?php endif; ?>

			<a href="#" class="footer__top-link" data-magnetic>
				<?php esc_html_e( 'Retour en haut ↑', 'sliceofcactus' ); ?>
			</a>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
