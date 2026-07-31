<?php
/**
 * Narration taxonomy archive: one rubrique of the Photo archive (voyage,
 * lifestyle, portraits…), reusing the same masthead and .serie-grid markup
 * as archive-photo.php, scoped to a single term.
 *
 * projet-52 and color-your-life never reach this template: they redirect to
 * their own dedicated page before rendering (see inc/redirects.php) — every
 * other narration term lands here. No rubchips filter here (unlike the main
 * archive): the grid is already scoped to one narration, so there is
 * nothing left to filter client-side.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term        = get_queried_object();
$photos      = $term instanceof WP_Term ? soc_get_photo_archive_series_by_narration( $term ) : array();
$total       = count( $photos );
$description = $term instanceof WP_Term ? term_description( $term ) : '';
?>
<main id="main-content" class="soc-photo-archive rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Photos', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Label 36 poses', 'sliceofcactus' ); ?></span>
		<span><?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?>
		</h1>
		<?php if ( '' !== $description ) : ?>
			<div class="mag-masthead__lead">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer la photo', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>" class="is-active" aria-current="page">
				<?php esc_html_e( 'Séries', 'sliceofcactus' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/voyage-carte/' ) ); ?>">
				<?php esc_html_e( 'Carte', 'sliceofcactus' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/color-your-life/' ) ); ?>">
				<?php esc_html_e( 'Par couleur', 'sliceofcactus' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/projet-52/' ) ); ?>">
				<?php esc_html_e( 'Projet 52', 'sliceofcactus' ); ?>
			</a>
		</div>
	</div>

	<div class="mag-sommaire">
		<h2><?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?></h2>
		<span>
			<?php
			printf(
				/* translators: %s: number of series. */
				esc_html( _n( '%s série', '%s séries', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<?php if ( 0 === $total ) : ?>

		<p class="soc-empty-note"><?php esc_html_e( 'Aucune série publiée dans cette rubrique pour le moment.', 'sliceofcactus' ); ?></p>

	<?php else : ?>

		<section class="serie-grid">
			<?php foreach ( $photos as $index => $photo ) : ?>
				<?php
				$location = soc_get_photo_location( $photo->ID );
				$country  = soc_get_photo_country( $photo->ID );
				$poses    = soc_get_photo_pose_count( $photo->ID );
				$cover_id = soc_get_photo_cover_id( $photo->ID );
				$no       = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
				$tot      = str_pad( (string) $total, 2, '0', STR_PAD_LEFT );
				$place    = ! empty( $location )
					? implode(
						' · ',
						array_filter( array( $country ? $country->name : '', $location['name'] ?? '' ) )
					)
					: '';
				?>
				<a class="serie-cell" href="<?php echo esc_url( get_permalink( $photo ) ); ?>">
					<div class="serie-cell__media">
						<?php if ( $cover_id ) : ?>
							<?php
							echo wp_get_attachment_image(
								$cover_id,
								'large',
								false,
								array(
									'alt'     => get_the_title( $photo ),
									'loading' => 'lazy',
								)
							);
							?>
						<?php endif; ?>
					</div>
					<div class="serie-cell__caption">
						<span class="serie-cell__no"><?php echo esc_html( "{$no} / {$tot}" ); ?></span>
						<div>
							<?php if ( '' !== $place ) : ?>
								<span class="serie-cell__place"><?php echo esc_html( $place ); ?></span>
							<?php endif; ?>
							<h3 class="serie-cell__title"><?php echo esc_html( get_the_title( $photo ) ); ?></h3>
						</div>
						<span class="serie-cell__count">
							<b><?php echo esc_html( number_format_i18n( $poses ) ); ?></b>
							<?php esc_html_e( 'poses', 'sliceofcactus' ); ?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</section>

	<?php endif; ?>

	<a class="back-link" href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>">
		<?php esc_html_e( '‹ Toutes les séries', 'sliceofcactus' ); ?>
	</a>

</main>
<?php
get_footer();
