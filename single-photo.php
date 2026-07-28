<?php
/**
 * Single photo controller.
 *
 * One contact-sheet template serves every narration, matching
 * sliceofcactus-astro/src/pages/photo/[id].astro: the visual distinction
 * between narrations (voyage, lifestyle, …) is a body class and CSS accent,
 * not a separate template file.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="soc-photo-main">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single/photo', 'contact-sheet' );
	endwhile;
	?>
</main>
<?php
get_footer();
