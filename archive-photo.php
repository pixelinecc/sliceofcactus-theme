<?php
/**
 * Photo archive, migrated from sliceofcactus-astro/src/pages/photo/index.astro.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$photos = soc_get_photo_archive_series();
$total  = count( $photos );
?>
<main id="main-content" class="soc-photo-archive rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Photos', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Label 36 poses', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'voyage · lifestyle · noir & blanc', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'Photo', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'le label 36 poses — jamais plus de trente-six images', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead">
			<p>
				<span class="drop">U</span><?php esc_html_e( 'ne photographie qui se choisit plutôt qu\'elle ne s\'accumule. Voyage, lifestyle et noir & blanc — chaque série tient en trente-six poses. À parcourir comme on veut : par série, par lieu, par couleur ou semaine après semaine.', 'sliceofcactus' ); ?>
			</p>
		</div>
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
		<h2><?php esc_html_e( 'Les séries', 'sliceofcactus' ); ?></h2>
		<span id="soc-serie-count">
			<?php
			printf(
				/* translators: %s: number of series. */
				esc_html( _n( '%s série', '%s séries', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<div class="rubchips" id="soc-rub-chips"></div>

	<section class="serie-grid" id="soc-serie-grid">
		<?php foreach ( $photos as $index => $photo ) : ?>
			<?php
			$narrations = soc_get_photo_narrations( $photo->ID );
			$narration  = ! empty( $narrations ) ? $narrations[0] : null;
			$location   = soc_get_photo_location( $photo->ID );
			$country    = soc_get_photo_country( $photo->ID );
			$poses      = soc_get_photo_pose_count( $photo->ID );
			$cover_id   = soc_get_photo_cover_id( $photo->ID );
			$no         = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
			$tot        = str_pad( (string) $total, 2, '0', STR_PAD_LEFT );

			if ( ! empty( $location ) ) {
				$place = implode(
					' · ',
					array_filter( array( $country ? $country->name : '', $location['name'] ?? '' ) )
				);
			} else {
				$place = $narration ? $narration->name : '';
			}
			?>
			<a
				class="serie-cell"
				href="<?php echo esc_url( get_permalink( $photo ) ); ?>"
				data-narration="<?php echo esc_attr( $narration ? $narration->slug : '' ); ?>"
				data-narration-label="<?php echo esc_attr( $narration ? $narration->name : '' ); ?>"
			>
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

</main>
<?php
get_footer();