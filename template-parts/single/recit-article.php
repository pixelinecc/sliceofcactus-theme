<?php
/**
 * Récit article, migrated from sliceofcactus-astro/src/pages/recits/[id].astro.
 *
 * The corps array of paragraphs is replaced by the Gutenberg editor
 * (the_content()). The masthead (mag-runhead + Anton title on --accent) is
 * the same regardless of soc_recit_hero_layout — only the featured image's
 * own presentation changes:
 * - "plate": inserted as a framed plate at the top of the body.
 * - "cover": used as the masthead's background instead of a flat fill.
 * - "margin": tucked beside the body text like a snapshot taped into a
 *   notebook.
 *
 * Without a featured image, none of the three applies — the masthead shows
 * alone, which is also why it never depends on having a good photo to show.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id          = get_the_ID();
$archive_url      = get_post_type_archive_link( 'recit' );
$date_label       = soc_get_recit_date_label( $post_id );
$hero_layout      = soc_get_recit_hero_layout( $post_id );
$hero_caption     = soc_get_recit_hero_caption( $post_id );
$has_hero_image   = has_post_thumbnail( $post_id );
$resonances       = get_the_terms( $post_id, 'resonance' );
$resonances       = is_array( $resonances ) ? $resonances : array();
$resonance_groups = soc_get_resonance_groups( $post_id );
$related          = soc_get_recit_related_creations( $post_id );
$related_photos   = soc_get_recit_photos( $post_id );
$reading_minutes  = soc_get_recit_reading_minutes( $post_id );

$use_cover  = $has_hero_image && 'cover' === $hero_layout;
$use_plate  = $has_hero_image && 'plate' === $hero_layout;
$use_margin = $has_hero_image && 'margin' === $hero_layout;

$stamp_href = '';
$stamp_text = '';

if ( ! empty( $related_photos ) ) {
	$stamp_href = '#article-gallery-' . $post_id;
	$stamp_text = __( 'Galerie photo', 'sliceofcactus' );
} elseif ( ! empty( $related ) ) {
	$stamp_href = '#article-creations-' . $post_id;
	$stamp_text = __( 'Contenu associé', 'sliceofcactus' );
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
	<div class="mag-runhead">
		<span>
			<?php if ( $archive_url ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( '← Tous les récits', 'sliceofcactus' ); ?></a>
			<?php endif; ?>
		</span>
		<span><?php esc_html_e( 'Récit', 'sliceofcactus' ); ?></span>
		<span>
			<?php if ( '' !== $date_label ) : ?>
				<b><?php echo esc_html( $date_label ); ?></b>
			<?php endif; ?>
		</span>
	</div>

	<div
		class="article__masthead<?php echo $use_cover ? ' article__masthead--cover' : ''; ?>"
		<?php if ( $use_cover ) : ?>
			style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'large' ) ); ?>');"
		<?php endif; ?>
	>
		<div class="article__masthead-inner">
			<h1 class="article__masthead-title"><?php the_title(); ?></h1>
			<div class="journal-folio">
				<span><?php esc_html_e( 'Slice of Cactus', 'sliceofcactus' ); ?></span>
				<span><?php esc_html_e( 'Édition N°1', 'sliceofcactus' ); ?></span>
				<span>
					<?php
					printf(
						/* translators: %s: number of minutes. */
						esc_html__( '%s min de lecture', 'sliceofcactus' ),
						esc_html( number_format_i18n( $reading_minutes ) )
					);
					?>
				</span>
			</div>
		</div>
	</div>

	<div class="article__body">
		<?php if ( '' !== $stamp_href ) : ?>
			<a class="article__stamp" href="<?php echo esc_url( $stamp_href ); ?>">
				<span class="article__stamp-label"><?php esc_html_e( 'À voir aussi', 'sliceofcactus' ); ?></span>
				<?php echo esc_html( $stamp_text ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $use_plate ) : ?>
			<?php
			ob_start();
			?>
			<figure class="article__plate">
				<div class="article__plate-mark">
					<span><?php esc_html_e( 'Planche', 'sliceofcactus' ); ?></span>
					<?php if ( '' !== $hero_caption ) : ?>
						<span><?php echo esc_html( $hero_caption ); ?></span>
					<?php endif; ?>
				</div>
				<div class="article__plate-frame">
					<?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
				</div>
			</figure>
			<?php
			$plate_html    = ob_get_clean();
			$content_html  = apply_filters( 'the_content', get_the_content() );
			// Only splice after the first paragraph when the content actually
			// opens with one: a bare strpos() for '</p>' would otherwise match
			// a closing tag nested inside an earlier block (e.g. a quote's
			// citation paragraph), splitting that block's markup instead of
			// following the opening paragraph.
			$starts_with_p = 0 === stripos( ltrim( $content_html ), '<p' );
			$after_first_p = $starts_with_p ? strpos( $content_html, '</p>' ) : false;

			if ( false !== $after_first_p ) {
				$after_first_p += strlen( '</p>' );

				echo substr( $content_html, 0, $after_first_p ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $plate_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo substr( $content_html, $after_first_p ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo $plate_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		<?php else : ?>
			<?php if ( $use_margin ) : ?>
				<figure class="article__snapshot">
					<?php echo get_the_post_thumbnail( $post_id, 'medium' ); ?>
					<?php if ( '' !== $hero_caption ) : ?>
						<figcaption class="article__snapshot-cap"><?php echo esc_html( $hero_caption ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

			<?php the_content(); ?>
		<?php endif; ?>
	</div>

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

	<?php if ( ! empty( $related ) ) : ?>
		<section class="article__gallery" aria-labelledby="<?php echo esc_attr( 'article-creations-' . $post_id ); ?>">
			<div class="article__gallery-head">
				<h2 id="<?php echo esc_attr( 'article-creations-' . $post_id ); ?>">
					<?php esc_html_e( 'Cette histoire accompagne', 'sliceofcactus' ); ?>
				</h2>
			</div>

			<div class="article__gallery-grid">
				<?php foreach ( $related as $creation ) : ?>
					<?php $cover_id = soc_get_creation_cover_id( $creation->ID ); ?>
					<a class="article__gallery-card" href="<?php echo esc_url( get_permalink( $creation ) ); ?>">
						<?php if ( $cover_id ) : ?>
							<div class="article__gallery-thumb">
								<?php
								echo wp_get_attachment_image(
									$cover_id,
									'medium',
									false,
									array(
										'class'   => 'article__gallery-plate',
										'loading' => 'lazy',
										'alt'     => '',
									)
								);
								?>
							</div>
						<?php endif; ?>
						<div class="article__gallery-cap">
							<span class="article__gallery-title"><?php echo esc_html( get_the_title( $creation ) ); ?></span>
							<span class="article__gallery-more"><?php esc_html_e( 'Voir →', 'sliceofcactus' ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $related_photos ) ) : ?>
		<section class="article__gallery article__gallery--photos" aria-labelledby="<?php echo esc_attr( 'article-gallery-' . $post_id ); ?>">
			<div class="article__gallery-head">
				<h2 id="<?php echo esc_attr( 'article-gallery-' . $post_id ); ?>">
					<?php esc_html_e( 'Photos associées', 'sliceofcactus' ); ?>
				</h2>
			</div>

			<div class="article__gallery-grid">
				<?php foreach ( $related_photos as $photo ) : ?>
					<?php $cover_id = soc_get_photo_cover_id( $photo->ID ); ?>
					<a class="article__gallery-card" href="<?php echo esc_url( get_permalink( $photo ) ); ?>">
						<?php if ( $cover_id ) : ?>
							<div class="article__gallery-thumb">
								<?php
								echo wp_get_attachment_image(
									$cover_id,
									'medium',
									false,
									array(
										'class'   => 'article__gallery-plate',
										'loading' => 'lazy',
										'alt'     => '',
									)
								);
								?>
							</div>
						<?php endif; ?>
						<div class="article__gallery-cap">
							<span class="article__gallery-title"><?php echo esc_html( get_the_title( $photo ) ); ?></span>
							<span class="article__gallery-more"><?php esc_html_e( 'Voir la série →', 'sliceofcactus' ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $resonance_groups ) ) : ?>
		<section class="article__resonances" aria-labelledby="<?php echo esc_attr( 'article-resonances-' . $post_id ); ?>">
			<div class="article__resonances-label" id="<?php echo esc_attr( 'article-resonances-' . $post_id ); ?>">
				<?php esc_html_e( 'Résonne avec', 'sliceofcactus' ); ?>
			</div>

			<div class="article__resonances-grid">
				<?php foreach ( $resonance_groups as $group ) : ?>
					<div class="article__resonance-card" <?php echo '' !== $group['color'] ? 'style="--term-color:' . esc_attr( $group['color'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<h3 class="article__resonance-term">
							<a href="<?php echo esc_url( get_term_link( $group['term'] ) ); ?>">
								<?php echo esc_html( $group['term']->name ); ?>
							</a>
						</h3>
						<ul class="article__resonance-items">
							<?php foreach ( $group['items'] as $entry ) : ?>
								<li class="article__resonance-item">
									<span class="article__resonance-kind"><?php echo esc_html( $entry['kind'] ); ?></span>
									<a href="<?php echo esc_url( get_permalink( $entry['post'] ) ); ?>">
										<?php echo esc_html( get_the_title( $entry['post'] ) ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
</article>
