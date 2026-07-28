<?php
/**
 * Single creation controller.
 *
 * One template serves both dessin and coloriage, matching
 * sliceofcactus-astro/src/pages/dessin/[id].astro and coloriage/[id].astro:
 * the visual distinction is a body class and CSS accent, plus a couple of
 * conditional blocks (book credit), not a separate template file.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="soc-creation-main">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single/creation', 'contact-sheet' );
	endwhile;
	?>
</main>
<?php
get_footer();