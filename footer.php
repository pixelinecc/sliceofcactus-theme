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
		'title'    => __( 'Dessin', 'sliceofcactus' ),
	),
	array(
		'location' => 'footer_read',
		'title'    => __( 'À lire & suivre', 'sliceofcactus' ),
	),
);
$contact_email    = 'bonjour@sliceofcactus.fr';

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
			<a
				class="footer__mail"
				href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>"
				data-magnetic
			>
				<?php echo esc_html( $contact_email ); ?>
			</a>
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

					<?php if ( 'footer_read' === $footer_column['location'] ) : ?>
						<a href="<?php echo esc_url( 'https://instagram.com/sliceofcactus' ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Instagram · photo', 'sliceofcactus' ); ?>
						</a>
						<a href="<?php echo esc_url( 'https://instagram.com/traitducamelon' ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Instagram · dessin', 'sliceofcactus' ); ?>
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
