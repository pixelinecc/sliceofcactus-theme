<?php
/**
 * Template Name: Color Your Life
 *
 * Migrated from sliceofcactus-astro/src/pages/color-your-life.astro.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$series  = soc_get_color_your_life_series();
$palette = array();

foreach ( $series as $photo ) {
	$name = soc_get_photo_color_name( $photo->ID );

	if ( '' !== $name && ! isset( $palette[ $name ] ) ) {
		$palette[ $name ] = soc_get_photo_color( $photo->ID );
	}
}
?>
<main id="main-content" class="soc-color-your-life rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Thème', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Label photo · 36 poses', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'rangé par couleur', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'Color Your Life', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'des séries rangées par dominante de couleur', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead" data-reveal>
			<p>
				<span class="drop">D</span>
				<?php esc_html_e( 'u rouge braise au rose floraison : chaque série porte une couleur qui lui colle à la peau. Choisissez une teinte, laissez-vous porter.', 'sliceofcactus' ); ?>
			</p>
		</div>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer la photo', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>"><?php esc_html_e( 'Séries', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/voyage-carte/' ) ); ?>"><?php esc_html_e( 'Carte', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/color-your-life/' ) ); ?>" class="is-active" aria-current="page"><?php esc_html_e( 'Par couleur', 'sliceofcactus' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/projet-52/' ) ); ?>"><?php esc_html_e( 'Projet 52', 'sliceofcactus' ); ?></a>
		</div>
	</div>

	<?php if ( ! empty( $series ) ) : ?>

		<div class="spectrum" id="spectrum">
			<button type="button" data-color="all" class="is-active"><?php esc_html_e( 'Tout', 'sliceofcactus' ); ?></button>
			<?php foreach ( $palette as $name => $hex ) : ?>
				<button type="button" data-color="<?php echo esc_attr( $name ); ?>" style="--c: <?php echo esc_attr( $hex ); ?>">
					<i></i><?php echo esc_html( $name ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="cyl-grid" id="cylGrid">
			<?php foreach ( $series as $photo ) : ?>
				<?php
				$cover_id   = soc_get_photo_cover_id( $photo->ID );
				$color_name = soc_get_photo_color_name( $photo->ID );
				$color_hex  = soc_get_photo_color( $photo->ID );
				$poses      = soc_get_photo_pose_count( $photo->ID );
				?>
				<a
					class="cyl-card"
					href="<?php echo esc_url( get_permalink( $photo ) ); ?>"
					data-color="<?php echo esc_attr( $color_name ); ?>"
					style="--c: <?php echo esc_attr( $color_hex ); ?>"
				>
					<div class="cyl-card__img">
						<?php
						echo wp_get_attachment_image(
							$cover_id,
							'large',
							false,
							array(
								'alt'     => sprintf(
									/* translators: 1: series title, 2: dominant color name. */
									__( '%1$s — dominante %2$s', 'sliceofcactus' ),
									get_the_title( $photo ),
									$color_name
								),
								'loading' => 'lazy',
							)
						);
						?>
						<span class="cyl-card__wash"></span>
					</div>
					<div class="cyl-card__cap">
						<span class="cyl-card__color"><i></i><?php echo esc_html( $color_name ); ?></span>
						<span class="cyl-card__title"><?php echo esc_html( get_the_title( $photo ) ); ?></span>
						<span class="cyl-card__n">
							<?php
							printf(
								/* translators: %s: number of poses. */
								esc_html__( '%s / 36 poses', 'sliceofcactus' ),
								esc_html( number_format_i18n( $poses ) )
							);
							?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<p class="cyl-empty"><?php esc_html_e( 'Aucune série avec une couleur dominante pour le moment.', 'sliceofcactus' ); ?></p>

	<?php endif; ?>

</main>
<?php
get_footer();