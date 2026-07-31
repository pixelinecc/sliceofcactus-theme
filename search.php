<?php
/**
 * Search results template.
 *
 * Without this file, WordPress falls back to index.php, whose header always
 * shows the site name instead of the search query — same soc-page-header/
 * soc-container structure, template-parts/content and content-none, just
 * with a contextual title.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="soc-site-main soc-container">
	<header class="soc-page-header">
		<h1>
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Résultats de recherche pour : %s', 'sliceofcactus' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
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
