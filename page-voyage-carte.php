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
 * Destinations are grouped by country (soc_get_voyage_map_destinations()):
 * the country chips filter which points show on the map, and reveal that
 * country's series as a plain-text list above the chips — the map markers
 * stay per point (a country has no single lat/lon of its own).
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$countries = soc_get_voyage_map_destinations();
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

		<?php if ( ! empty( $countries ) ) : ?>
			<div class="dest-articles" id="destArticles" hidden></div>

			<div class="dest-chips" id="destChips">
				<button type="button" data-country-slug="" class="is-active">
					<?php esc_html_e( 'Tous les pays', 'sliceofcactus' ); ?>
				</button>
				<?php foreach ( $countries as $country ) : ?>
					<button type="button" data-country-slug="<?php echo esc_attr( $country['slug'] ); ?>">
						<?php echo esc_html( $country['name'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $countries as $country ) : ?>
				<?php foreach ( $country['points'] as $point ) : ?>
					<template
						class="soc-dest-popup"
						data-country-slug="<?php echo esc_attr( $country['slug'] ); ?>"
						data-lat="<?php echo esc_attr( $point['lat'] ); ?>"
						data-lon="<?php echo esc_attr( $point['lon'] ); ?>"
					>
						<strong class="soc-dest-popup__title">
							<?php echo esc_html( $point['name'] ); ?>
							<?php if ( count( $point['series'] ) > 1 ) : ?>
								·
								<?php
								printf(
									/* translators: %s: number of series. */
									esc_html( _n( '%s série', '%s séries', count( $point['series'] ), 'sliceofcactus' ) ),
									esc_html( number_format_i18n( count( $point['series'] ) ) )
								);
								?>
							<?php endif; ?>
						</strong>
						<?php foreach ( $point['series'] as $photo ) : ?>
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

				<template class="soc-country-articles" data-country-slug="<?php echo esc_attr( $country['slug'] ); ?>">
					<h2 class="dest-articles__title"><?php echo esc_html( $country['name'] ); ?></h2>
					<ul class="dest-articles__list">
						<?php foreach ( $country['series'] as $photo ) : ?>
							<?php
							$point_name = soc_get_photo_location( $photo->ID )['name'] ?? '';
							$poses      = soc_get_photo_pose_count( $photo->ID );
							$cover_id   = soc_get_photo_cover_id( $photo->ID );
							?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $photo ) ); ?>">
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
											echo esc_html( $point_name ? $point_name . ' · ' : '' );
											printf(
												/* translators: %s: number of poses. */
												esc_html( _n( '%s pose', '%s poses', $poses, 'sliceofcactus' ) ),
												esc_html( number_format_i18n( $poses ) )
											);
											?>
										</span>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</template>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

</main>
<?php
get_footer();