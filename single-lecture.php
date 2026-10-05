<?php
/**
 * Single lecture controller.
 *
 * Content is authored with Gutenberg (the_content()); the book's fiche
 * (cover, author, note, genre…) comes from the lecture's ACF fields.
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

		get_template_part( 'template-parts/single/lecture', 'article' );
	endwhile;
	?>
</main>
<?php
get_footer();
