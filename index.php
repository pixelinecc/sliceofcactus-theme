<?php
/**
 * Fallback template.
 *
 * @package SliceOfCactus
 */

get_header();
?>
<main id="main-content" class="soc-site-main soc-container">
	<?php if ( have_posts() ) : ?>
		<header class="soc-page-header">
			<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
		</header>

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content' );
		endwhile;

		the_posts_pagination();
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>
</main>
<?php
get_footer();

