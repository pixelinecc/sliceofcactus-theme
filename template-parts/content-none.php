<?php
/**
 * Empty content presentation.
 *
 * @package SliceOfCactus
 */

?>
<section class="soc-content-none">
	<?php if ( is_search() ) : ?>
		<h1><?php esc_html_e( 'Aucun résultat', 'sliceofcactus' ); ?></h1>
		<p><?php esc_html_e( 'Aucun résultat pour cette recherche. Essayez avec d’autres mots-clés.', 'sliceofcactus' ); ?></p>
	<?php else : ?>
		<h1><?php esc_html_e( 'Aucun contenu', 'sliceofcactus' ); ?></h1>
		<p><?php esc_html_e( 'Rien n’a encore été publié ici.', 'sliceofcactus' ); ?></p>
	<?php endif; ?>
</section>

