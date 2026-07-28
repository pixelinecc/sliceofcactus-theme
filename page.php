<?php
/**
 * Page template.
 *
 * @package SliceOfCactus
 */

get_header();
?>
<main id="main-content" class="soc-site-main soc-container">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content' );
	endwhile;
	?>
</main>
<?php
get_footer();

