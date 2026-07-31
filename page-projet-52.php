<?php
/**
 * Template Name: Projet 52
 *
 * Migrated from sliceofcactus-astro/src/pages/projet-52.astro. Astro's
 * version has no real data (a hardcoded YEARS config feeding placeholder
 * images) — here one "photo" post per year is tagged with the narration
 * "projet-52", its ordered soc_photo_gallery images filling the weeks in
 * sequence, grouped by soc_get_projet52_years().
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$years        = soc_get_projet52_years();
$default_year = ! empty( $years ) ? array_key_first( $years ) : null;
?>
<main id="main-content" class="soc-p52 rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Projet 52', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Label photo', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'une photo par semaine', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'Projet 52', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'une photographie par semaine, toute l\'année', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead" data-reveal>
			<p>
				<span class="drop">C</span>
				<?php esc_html_e( 'inquante-deux semaines, cinquante-deux images. Un journal visuel qui se remplit au fil de l\'année — une discipline douce, une case à la fois.', 'sliceofcactus' ); ?>
			</p>
		</div>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer la photo', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>"><?php esc_html_e( 'Séries', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/voyage-carte/' ) ); ?>"><?php esc_html_e( 'Carte', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/color-your-life/' ) ); ?>"><?php esc_html_e( 'Par couleur', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/projet-52/' ) ); ?>" class="is-active" aria-current="page"><?php esc_html_e( 'Projet 52', 'sliceofcactus' ); ?></a>
		</div>
	</div>

	<?php if ( empty( $years ) ) : ?>

		<p class="p52-empty"><?php esc_html_e( 'Aucune photo Projet 52 pour le moment.', 'sliceofcactus' ); ?></p>

	<?php else : ?>

		<div class="p52-head">
			<div class="p52-years" id="p52Years" role="tablist" aria-label="<?php esc_attr_e( 'Année', 'sliceofcactus' ); ?>">
				<?php foreach ( $years as $year => $data ) : ?>
					<button
						type="button"
						data-year="<?php echo esc_attr( $year ); ?>"
						data-done="<?php echo esc_attr( $data['done'] ); ?>"
						<?php echo $year === $default_year ? 'class="is-active" aria-current="true"' : ''; ?>
					>
						<?php echo esc_html( $year ); ?>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="p52-progress">
				<div class="p52-bar"><span id="p52BarFill"></span></div>
				<span class="p52-count" id="p52Count"></span>
			</div>
		</div>

		<?php foreach ( $years as $year => $data ) : ?>
			<div
				class="p52-grid"
				id="p52Grid-<?php echo esc_attr( $year ); ?>"
				data-p52-grid
				<?php echo $year !== $default_year ? 'hidden' : ''; ?>
			>
				<?php foreach ( $data['weeks'] as $week => $attachment_id ) : ?>
					<?php $week_label = sprintf( 'S%02d', $week ); ?>
					<?php if ( null !== $attachment_id ) : ?>
						<?php
						$image_alt = trim( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
						$caption   = '' !== $image_alt
							? sprintf(
								/* translators: 1: week number, 2: image description. */
								__( 'Semaine %1$d · %2$s', 'sliceofcactus' ),
								$week,
								$image_alt
							)
							: sprintf(
								/* translators: %d: week number. */
								__( 'Semaine %d', 'sliceofcactus' ),
								$week
							);
						?>
						<button
							type="button"
							class="wk wk--full"
							data-full="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'large' ) ); ?>"
							data-caption="<?php echo esc_attr( $caption ); ?>"
							aria-label="<?php echo esc_attr( $caption ); ?>"
						>
							<?php
							echo wp_get_attachment_image(
								$attachment_id,
								'medium',
								false,
								array(
									'alt'     => $caption,
									'loading' => 'lazy',
								)
							);
							?>
							<span class="wk__no"><?php echo esc_html( $week_label ); ?></span>
						</button>
					<?php else : ?>
						<div class="wk wk--empty">
							<span class="wk__no"><?php echo esc_html( $week_label ); ?></span>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

	<?php endif; ?>

</main>

<div
	class="lightbox"
	id="p52-lightbox"
	role="dialog"
	aria-modal="true"
	aria-label="<?php esc_attr_e( 'Visionneuse du Projet 52', 'sliceofcactus' ); ?>"
	aria-hidden="true"
>
	<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Fermer', 'sliceofcactus' ); ?>">×</button>
	<button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Semaine précédente', 'sliceofcactus' ); ?>">‹</button>
	<figure class="lightbox__fig">
		<img alt="">
		<figcaption></figcaption>
	</figure>
	<button class="lightbox__nav lightbox__nav--next" type="button" aria-label="<?php esc_attr_e( 'Semaine suivante', 'sliceofcactus' ); ?>">›</button>

	<div class="lightbox__strip-wrap">
		<button class="lightbox__strip-nav lightbox__strip-nav--prev" type="button" aria-label="<?php esc_attr_e( 'Défiler les vignettes vers la gauche', 'sliceofcactus' ); ?>">‹</button>
		<div class="lightbox__strip" id="p52lbStrip" role="group" aria-label="<?php esc_attr_e( 'Navigation entre les semaines', 'sliceofcactus' ); ?>"></div>
		<button class="lightbox__strip-nav lightbox__strip-nav--next" type="button" aria-label="<?php esc_attr_e( 'Défiler les vignettes vers la droite', 'sliceofcactus' ); ?>">›</button>
	</div>
</div>
<?php
get_footer();