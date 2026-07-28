<?php
/**
 * Not found template.
 *
 * @package SliceOfCactus
 */

get_header();
?>
<main id="main-content" class="soc-site-main soc-container">
	<section class="soc-not-found">
		<h1><?php esc_html_e( 'Page introuvable', 'sliceofcactus' ); ?></h1>
		<p><?php esc_html_e( 'Cette page n’existe pas ou a été déplacée.', 'sliceofcactus' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Revenir à l’accueil', 'sliceofcactus' ); ?>
		</a>
	</section>
</main>
<?php
get_footer();

