<?php
/**
 * Template Name: Carte des voyages
 *
 * Migrated from sliceofcactus-astro/src/pages/voyage-carte.astro. Popup
 * content is rendered server-side into <template> elements instead of
 * being passed to Leaflet as JSON (Astro's window.__DESTS__): the map
 * script (assets/scripts/page-voyage-carte.js) just reads them from the
 * DOM, matching how the rest of the theme's JS works.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$destinations = soc_get_voyage_map_destinations();
?>
<main id="main-content" class="soc-voyage-carte rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Voyage', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Label photo · 36 poses', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'la carte des séries', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'La carte', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'chaque point, une série de 36 poses', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead" data-reveal>
			<p>
				<span class="drop">O</span>
				<?php esc_html_e( 'ù la pellicule s\'est arrêtée : touchez un point sur la carte pour ouvrir la série. Des carnets de lumière, d\'une côte à l\'autre.', 'sliceofcactus' ); ?>
			</p>
		</div>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer la photo', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>"><?php esc_html_e( 'Séries', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/voyage-carte/' ) ); ?>" class="is-active" aria-current="page"><?php esc_html_e( 'Carte', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/color-your-life/' ) ); ?>"><?php esc_html_e( 'Par couleur', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/projet-52/' ) ); ?>"><?php esc_html_e( 'Projet 52', 'sliceofcactus' ); ?></a>
		</div>
	</div>

	<div class="map-wrap" data-reveal>
		<div id="leaflet-map" class="soc-leaflet-map"></div>

		<?php if ( ! empty( $destinations ) ) : ?>
			<div class="dest-chips" id="destChips">
				<?php foreach ( $destinations as $destination ) : ?>
					<button
						type="button"
						data-dest-name="<?php echo esc_attr( $destination['name'] ); ?>"
						data-lat="<?php echo esc_attr( $destination['lat'] ); ?>"
						data-lon="<?php echo esc_attr( $destination['lon'] ); ?>"
					>
						<?php echo esc_html( $destination['name'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $destinations as $destination ) : ?>
				<template class="soc-dest-popup" data-dest-name="<?php echo esc_attr( $destination['name'] ); ?>">
					<strong class="soc-dest-popup__title">
						<?php echo esc_html( $destination['name'] ); ?>
						<?php if ( count( $destination['series'] ) > 1 ) : ?>
							·
							<?php
							printf(
								/* translators: %s: number of series. */
								esc_html( _n( '%s série', '%s séries', count( $destination['series'] ), 'sliceofcactus' ) ),
								esc_html( number_format_i18n( count( $destination['series'] ) ) )
							);
							?>
						<?php endif; ?>
					</strong>
					<?php foreach ( $destination['series'] as $photo ) : ?>
						<?php
						$cover_id = soc_get_photo_cover_id( $photo->ID );
						$poses    = soc_get_photo_pose_count( $photo->ID );
						?>
						<a class="soc-dest-popup__serie" href="<?php echo esc_url( get_permalink( $photo ) ); ?>">
							<?php if ( $cover_id ) : ?>
								<?php
								echo wp_get_attachment_image(
									$cover_id,
									'thumbnail',
									false,
									array(
										'alt'     => '',
										'loading' => 'lazy',
									)
								);
								?>
							<?php endif; ?>
							<span>
								<strong><?php echo esc_html( get_the_title( $photo ) ); ?></strong>
								<span>
									<?php
									printf(
										/* translators: %s: number of poses. */
										esc_html( _n( '%s pose', '%s poses', $poses, 'sliceofcactus' ) ),
										esc_html( number_format_i18n( $poses ) )
									);
									?>
								</span>
							</span>
						</a>
					<?php endforeach; ?>
				</template>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

</main>
<?php
get_footer();