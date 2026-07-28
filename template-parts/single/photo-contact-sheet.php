<?php
/**
 * Contact-sheet presentation migrated from Astro photo/[id].astro.
 *
 * @package SliceOfCactus
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id          = get_the_ID();
$narrations       = soc_get_photo_narrations( $post_id );
$narration_slug   = isset( $args['narration_slug'] ) ? sanitize_title( $args['narration_slug'] ) : '';
$active_narration = null;

foreach ( $narrations as $narration ) {
	if ( $narration_slug === $narration->slug ) {
		$active_narration = $narration;
		break;
	}
}

if ( ! $active_narration && ! empty( $narrations ) ) {
	$active_narration = $narrations[0];
}

$archive_url = get_post_type_archive_link( 'photo' );
$intro       = soc_get_photo_intro( $post_id );
$location    = soc_get_photo_location( $post_id );
$photo_year  = soc_get_photo_year( $post_id );
$content     = apply_filters( 'the_content', get_the_content() );
$image_count = preg_match_all( '/<img\b/i', $content );
$uses_cover  = 0 === $image_count && has_post_thumbnail( $post_id );

if ( $uses_cover ) {
	$image_count = 1;
}

$film_count  = $image_count > 0 ? (int) ceil( $image_count / 36 ) : 0;
$title_html  = esc_html( get_the_title() );
$title_html  = preg_replace( '/,\s*/u', ',<br>', $title_html, 1 );
$suggestions = soc_get_photo_suggestions( $post_id, 6 );
$sheet_id    = 'soc-photo-sheet-' . $post_id;
$lightbox_id = 'soc-photo-lightbox-' . $post_id;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'soc-photo soc-photo--contact-sheet' ); ?>>
	<header class="serie-hero">
		<?php if ( $archive_url ) : ?>
			<a class="serie-hero__back" href="<?php echo esc_url( $archive_url ); ?>">
				<?php
				printf(
					/* translators: %s: narration name. */
					esc_html__( '← %s · 36 poses', 'sliceofcactus' ),
					esc_html( $active_narration ? $active_narration->name : __( 'Photo', 'sliceofcactus' ) )
				);
				?>
			</a>
		<?php endif; ?>

		<p class="serie-hero__cat">
			<?php if ( $film_count > 1 ) : ?>
				<?php
				printf(
					/* translators: 1: number of films, 2: narration name. */
					esc_html( _n( '%1$d pellicule · %2$s', '%1$d pellicules · %2$s', $film_count, 'sliceofcactus' ) ),
					esc_html( number_format_i18n( $film_count ) ),
					esc_html( $active_narration ? $active_narration->name : __( 'Photo', 'sliceofcactus' ) )
				);
				?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %s: narration name. */
					esc_html__( '36 poses · %s', 'sliceofcactus' ),
					esc_html( $active_narration ? $active_narration->name : __( 'Photo', 'sliceofcactus' ) )
				);
				?>
			<?php endif; ?>
		</p>

		<h1 class="serie-hero__title"><?php echo wp_kses( $title_html, array( 'br' => array() ) ); ?></h1>

		<?php if ( $image_count > 0 || ! empty( $location ) || '' !== $photo_year ) : ?>
			<div class="serie-hero__meta">
				<?php if ( $image_count > 0 ) : ?>
					<?php if ( $film_count > 1 ) : ?>
						<span>
							<?php esc_html_e( 'Pellicules :', 'sliceofcactus' ); ?>
							<b><?php echo esc_html( number_format_i18n( $film_count ) ); ?> × 36 poses</b>
							·
							<?php
							printf(
								/* translators: %s: number of images. */
								esc_html( _n( '%s vue', '%s vues', $image_count, 'sliceofcactus' ) ),
								esc_html( number_format_i18n( $image_count ) )
							);
							?>
						</span>
					<?php else : ?>
						<span>
							<?php esc_html_e( 'Pellicule :', 'sliceofcactus' ); ?>
							<b><?php echo esc_html( number_format_i18n( $image_count ) ); ?> / 36 poses</b>
						</span>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( ! empty( $location ) ) : ?>
					<span>
						<?php esc_html_e( 'Lieu :', 'sliceofcactus' ); ?>
						<b>
							<?php
							echo esc_html( $location['name'] );
							if ( ! empty( $location['country'] ) ) {
								echo esc_html( ', ' . $location['country'] );
							}
							?>
						</b>
					</span>
				<?php endif; ?>

				<?php if ( '' !== $photo_year ) : ?>
					<span>
						<?php esc_html_e( 'Année :', 'sliceofcactus' ); ?>
						<b><?php echo esc_html( $photo_year ); ?></b>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $intro ) : ?>
			<p class="serie-hero__lead"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</header>

	<section class="contact-sheet" aria-labelledby="<?php echo esc_attr( $sheet_id . '-title' ); ?>">
		<div class="contact-sheet__head">
			<h2 id="<?php echo esc_attr( $sheet_id . '-title' ); ?>">
				<?php esc_html_e( 'Planche-contact', 'sliceofcactus' ); ?>
			</h2>

			<?php if ( $image_count > 0 ) : ?>
				<span>
					<?php
					printf(
						/* translators: %s: number of images. */
						esc_html( _n( '%s vue · cliquez pour agrandir', '%s vues · cliquez pour agrandir', $image_count, 'sliceofcactus' ) ),
						esc_html( number_format_i18n( $image_count ) )
					);
					?>
				</span>
			<?php endif; ?>
		</div>

		<div
			class="sheet-grid soc-photo-sheet__content"
			id="<?php echo esc_attr( $sheet_id ); ?>"
			data-lightbox="<?php echo esc_attr( $lightbox_id ); ?>"
		>
			<?php if ( $uses_cover ) : ?>
				<figure class="wp-block-image">
					<?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
				</figure>
			<?php else : ?>
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered Gutenberg content. ?>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( ! empty( $suggestions ) ) : ?>
		<section class="more-series" aria-labelledby="<?php echo esc_attr( 'soc-more-photos-' . $post_id ); ?>">
			<div class="mag-sommaire">
				<h2 id="<?php echo esc_attr( 'soc-more-photos-' . $post_id ); ?>">
					<?php esc_html_e( 'Autres séries', 'sliceofcactus' ); ?>
				</h2>
				<span><?php esc_html_e( 'à explorer', 'sliceofcactus' ); ?></span>
			</div>

			<div class="more-series__grid">
				<?php foreach ( $suggestions as $suggestion ) : ?>
					<?php
					$suggestion_narrations = soc_get_photo_narrations( $suggestion->ID );
					$suggestion_label      = ! empty( $suggestion_narrations )
						? $suggestion_narrations[0]->name
						: __( 'Photo', 'sliceofcactus' );
					?>
					<a class="more-series__card" href="<?php echo esc_url( get_permalink( $suggestion ) ); ?>">
						<?php if ( has_post_thumbnail( $suggestion ) ) : ?>
							<div class="more-series__thumb">
								<?php
								echo get_the_post_thumbnail(
									$suggestion,
									'large',
									array(
										'class'   => 'more-series__plate',
										'loading' => 'lazy',
									)
								);
								?>
							</div>
						<?php endif; ?>

						<div class="more-series__cap">
							<span class="more-series__title"><?php echo esc_html( get_the_title( $suggestion ) ); ?></span>
							<span class="more-series__n"><?php echo esc_html( $suggestion_label ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
</article>

<?php if ( $image_count > 0 ) : ?>
	<div
		class="lightbox"
		id="<?php echo esc_attr( $lightbox_id ); ?>"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Visionneuse de la série photo', 'sliceofcactus' ); ?>"
		aria-hidden="true"
	>
		<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Fermer', 'sliceofcactus' ); ?>">×</button>
		<button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Photo précédente', 'sliceofcactus' ); ?>">‹</button>
		<figure class="lightbox__fig">
			<img alt="">
			<figcaption></figcaption>
		</figure>
		<button class="lightbox__nav lightbox__nav--next" type="button" aria-label="<?php esc_attr_e( 'Photo suivante', 'sliceofcactus' ); ?>">›</button>
	</div>
<?php endif; ?>
