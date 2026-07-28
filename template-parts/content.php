<?php
/**
 * Default content presentation.
 *
 * @package SliceOfCactus
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'soc-entry' ); ?>>
	<header class="soc-entry__header">
		<?php if ( is_singular() ) : ?>
			<h1 class="soc-entry__title"><?php the_title(); ?></h1>
		<?php else : ?>
			<h2 class="soc-entry__title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
		<?php endif; ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="soc-entry__image">
			<?php the_post_thumbnail( 'large' ); ?>
		</figure>
	<?php endif; ?>

	<div class="soc-entry__content">
		<?php
		if ( is_singular() ) {
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="soc-page-links" aria-label="' . esc_attr__( 'Pages du contenu', 'sliceofcactus' ) . '">',
					'after'  => '</nav>',
				)
			);
		} else {
			the_excerpt();
		}
		?>
	</div>
</article>

