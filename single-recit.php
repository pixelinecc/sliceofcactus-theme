<?php
/**
 * Single récit controller.
 *
 * Migrated from sliceofcactus-astro/src/pages/recits/[id].astro. Content is
 * authored with Gutenberg (the_content()) rather than the corps array of
 * paragraphs used in Astro, per the project's editorial architecture.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="soc-recit-main">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single/recit', 'article' );
	endwhile;
	?>
</main>
<?php
get_footer();