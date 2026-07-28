<?php
/**
 * Récit article, migrated from sliceofcactus-astro/src/pages/recits/[id].astro.
 *
 * The corps array of paragraphs is replaced by the Gutenberg editor
 * (the_content()); the image Astro inserted after the first paragraph is
 * replaced by the dedicated hero fields (soc_recit_hero_layout/_caption),
 * rendered as a figure above the body.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$archive_url  = get_post_type_archive_link( 'recit' );
$location     = soc_get_recit_location_label( $post_id );
$date_label   = soc_get_recit_date_label( $post_id );
$hero_layout  = soc_get_recit_hero_layout( $post_id );
$hero_caption = soc_get_recit_hero_caption( $post_id );
$resonances   = get_the_terms( $post_id, 'resonance' );
$resonances   = is_array( $resonances ) ? $resonances : array();
$related      = soc_get_recit_related_creations( $post_id );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
	<?php if ( $archive_url ) : ?>
		<a class="article__back" href="<?php echo esc_url( $archive_url ); ?>">
			<?php esc_html_e( '← Tous les récits', 'sliceofcactus' ); ?>
		</a>
	<?php endif; ?>

	<div class="article__kicker">
		<?php
		echo '' !== $location
			? esc_html(
				sprintf(
					/* translators: %s: place name. */
					__( 'Récit · %s', 'sliceofcactus' ),
					$location
				)
			)
			: esc_html__( 'Récit', 'sliceofcactus' );
		?>
	</div>

	<h1 class="article__title"><?php the_title(); ?></h1>

	<?php if ( '' !== $date_label ) : ?>
		<div class="article__meta"><?php echo esc_html( $date_label ); ?></div>
	<?php endif; ?>

	<?php if ( has_post_thumbnail( $post_id ) ) : ?>
		<figure class="article__figure article__figure--<?php echo esc_attr( $hero_layout ); ?>">
			<?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
			<?php if ( '' !== $hero_caption ) : ?>
				<figcaption><?php echo esc_html( $hero_caption ); ?></figcaption>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<div class="article__body">
		<?php the_content(); ?>
	</div>

	<?php if ( ! empty( $related ) ) : ?>
		<p class="linked-note is-on">
			<?php esc_html_e( 'Cette histoire accompagne :', 'sliceofcactus' ); ?>
			<?php foreach ( $related as $index => $creation ) : ?>
				<?php echo 0 < $index ? ', ' : ' '; ?>
				<a href="<?php echo esc_url( get_permalink( $creation ) ); ?>"><?php echo esc_html( get_the_title( $creation ) ); ?></a>
			<?php endforeach; ?>
			.
		</p>
	<?php endif; ?>

	<?php if ( ! empty( $resonances ) ) : ?>
		<section class="serie-resonances" aria-label="<?php esc_attr_e( 'Résonances', 'sliceofcactus' ); ?>">
			<span class="serie-resonances__label"><?php esc_html_e( 'Résonances :', 'sliceofcactus' ); ?></span>
			<ul class="serie-resonances__list">
				<?php foreach ( $resonances as $resonance ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $resonance ) ); ?>">
							<?php echo esc_html( $resonance->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</article>