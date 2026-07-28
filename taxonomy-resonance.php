<?php
/**
 * Résonance archive.
 *
 * No Astro equivalent: résonances are the one functional evolution over
 * sliceofcactus-astro (see CLAUDE.md) — they connect Photo, Création and
 * Récit. Reuses the existing "more-series" card and magazine-hub chrome
 * (assets/styles/components/magazine-hub.css) rather than introducing new
 * markup.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term        = get_queried_object();
$description = $term instanceof WP_Term ? term_description( $term ) : '';

$groups = array(
	'photo'    => array(
		'label' => __( 'Photo', 'sliceofcactus' ),
		'items' => $term instanceof WP_Term ? soc_get_resonance_items( $term, 'photo' ) : array(),
	),
	'creation' => array(
		'label' => __( 'Création', 'sliceofcactus' ),
		'items' => $term instanceof WP_Term ? soc_get_resonance_items( $term, 'creation' ) : array(),
	),
	'recit'    => array(
		'label' => __( 'Récits', 'sliceofcactus' ),
		'items' => $term instanceof WP_Term ? soc_get_resonance_items( $term, 'recit' ) : array(),
	),
);

$total = array_sum( array_map( static fn( array $group ): int => count( $group['items'] ), $groups ) );
?>
<main id="main-content" class="soc-resonance-archive rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Résonances', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Boussole éditoriale transversale', 'sliceofcactus' ); ?></span>
		<span><?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?>
			<em><?php esc_html_e( 'relie Photo, Création et Récits', 'sliceofcactus' ); ?></em>
		</h1>
		<?php if ( '' !== $description ) : ?>
			<div class="mag-masthead__lead">
				<p><?php echo wp_kses_post( $description ); ?></p>
			</div>
		<?php endif; ?>
	</header>

	<?php if ( 0 === $total ) : ?>

		<p class="soc-empty-note"><?php esc_html_e( 'Rien n\'est encore relié à cette résonance.', 'sliceofcactus' ); ?></p>

	<?php else : ?>

		<?php foreach ( $groups as $type => $group ) : ?>
			<?php if ( empty( $group['items'] ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>

			<div class="mag-sommaire">
				<h2><?php echo esc_html( $group['label'] ); ?></h2>
				<span>
					<?php
					printf(
						/* translators: %s: number of items. */
						esc_html( _n( '%s résultat', '%s résultats', count( $group['items'] ), 'sliceofcactus' ) ),
						esc_html( number_format_i18n( count( $group['items'] ) ) )
					);
					?>
				</span>
			</div>

			<div class="more-series__grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<?php
					if ( 'photo' === $type ) {
						$cover_id   = soc_get_photo_cover_id( $item->ID );
						$narrations = soc_get_photo_narrations( $item->ID );
						$sub_label  = ! empty( $narrations ) ? $narrations[0]->name : __( 'Photo', 'sliceofcactus' );
					} elseif ( 'creation' === $type ) {
						$cover_id  = soc_get_creation_cover_id( $item->ID );
						$medium    = soc_get_creation_medium( $item->ID );
						$sub_label = $medium ? $medium->name : __( 'Création', 'sliceofcactus' );
					} else {
						$cover_id  = absint( get_post_thumbnail_id( $item->ID ) );
						$date      = soc_get_recit_date_label( $item->ID );
						$sub_label = '' !== $date ? $date : __( 'Récit', 'sliceofcactus' );
					}
					?>
					<a class="more-series__card" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
						<?php if ( $cover_id ) : ?>
							<div class="more-series__thumb">
								<?php
								echo wp_get_attachment_image(
									$cover_id,
									'large',
									false,
									array(
										'class'   => 'more-series__plate',
										'alt'     => get_the_title( $item ),
										'loading' => 'lazy',
									)
								);
								?>
							</div>
						<?php endif; ?>
						<div class="more-series__cap">
							<span class="more-series__title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
							<span class="more-series__n"><?php echo esc_html( $sub_label ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

	<?php endif; ?>

</main>
<?php
get_footer();