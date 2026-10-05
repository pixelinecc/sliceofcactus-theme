<?php
/**
 * Title: Lecture — avis sans spoiler + spoilers
 * Slug: sliceofcactus/lecture-structure
 * Description: Structure d'un article de lecture : avis sans spoiler, puis zone spoilers clairement séparée.
 * Categories: sliceofcactus
 * Post Types: lecture
 * Inserter: true
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Mon avis — sans spoiler', 'lecture pattern', 'sliceofcactus' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"lecture-spoilers"} -->
<div class="wp-block-group lecture-spoilers"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( '⚠️ SPOILERS — maintenant, on peut vraiment en parler', 'lecture pattern', 'sliceofcactus' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
