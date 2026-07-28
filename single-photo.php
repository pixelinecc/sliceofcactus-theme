<?php
/**
 * Single photo controller.
 *
 * The visual template is selected from the assigned narration taxonomy:
 * template-parts/single/photo-{narration-slug}.php.
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

		$template_slug = soc_get_photo_narration_template_slug( get_the_ID() );
		get_template_part( 'template-parts/single/photo', $template_slug );
	endwhile;
	?>
</main>
<?php
get_footer();
