<?php
/**
 * Résonance archive.
 *
 * No Astro equivalent: résonances are the one functional evolution over
 * sliceofcactus-astro (see CLAUDE.md) — they connect Photo, Création and
 * Récit. Same dark background and row/track/nav slider system as the
 * parent page-resonances.php (assets/styles/components/resonances.css),
 * kept split by post type here (one row each for Photo/Création/Récits)
 * rather than mixed by date like the parent page's rows.
 *
 * Navigation back to the other résonances: a .view-switch/.view-toggle up
 * top (same pattern as Création's archive-creation.php/
 * taxonomy/creation-type-book-grid.php pair) plus a plain .back-link at the
 * bottom to the parent page — assumed to live at /resonances/, matching the
 * résonance taxonomy's own rewrite slug (adjust if the page was created
 * elsewhere).
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term        = get_queried_object();
$description = $term instanceof WP_Term ? term_description( $term ) : '';
$intro       = '';

if ( $term instanceof WP_Term && function_exists( 'get_field' ) ) {
	$intro = get_field( 'soc_resonance_intro', 'resonance_' . $term->term_id );
	$intro = is_string( $intro ) ? trim( $intro ) : '';
}

$all_terms = get_terms(
	array(
		'taxonomy'   => 'resonance',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);
$all_terms = is_array( $all_terms ) ? $all_terms : array();

$groups = array(
	'photo'    => array(
		'label' => __( 'Photo', 'sliceofcactus' ),
		'items' => $term instanceof WP_Term ? soc_get_resonance_items( $term, 'photo' ) : array(),
	),
	'creation' => array(
		'label' => __( 'Atelier', 'sliceofcactus' ),
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
		<span><?php esc_html_e( 'relie Photo, Atelier et Récits', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?>
			<?php if ( '' !== $intro ) : ?>
				<em><?php echo esc_html( $intro ); ?></em>
			<?php endif; ?>
		</h1>
		<?php if ( '' !== $description ) : ?>
			<div class="mag-masthead__lead">
				<p><?php echo wp_kses_post( $description ); ?></p>
			</div>
		<?php endif; ?>
	</header>

	<?php if ( ! empty( $all_terms ) ) : ?>
		<div class="view-switch">
			<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer les résonances', 'sliceofcactus' ); ?>">
				<a href="<?php echo esc_url( home_url( '/resonances/' ) ); ?>">
					<?php esc_html_e( 'Toutes', 'sliceofcactus' ); ?>
				</a>
				<?php foreach ( $all_terms as $tab_term ) : ?>
					<?php $is_current = $term instanceof WP_Term && $term->term_id === $tab_term->term_id; ?>
					<a
						href="<?php echo esc_url( get_term_link( $tab_term ) ); ?>"
						<?php echo $is_current ? 'class="is-active" aria-current="page"' : ''; ?>
					>
						<?php echo esc_html( $tab_term->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 0 === $total ) : ?>

		<p class="soc-empty-note"><?php esc_html_e( 'Rien n\'est encore relié à cette résonance.', 'sliceofcactus' ); ?></p>

	<?php else : ?>

		<?php foreach ( $groups as $type => $group ) : ?>
			<?php if ( empty( $group['items'] ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>

			<?php $track_id = 'resonance-archive-track-' . $type; ?>
			<section class="resonance-row" aria-labelledby="<?php echo esc_attr( 'resonance-archive-title-' . $type ); ?>">
				<div class="resonance-row__head">
					<div class="resonance-row__intro">
						<h2 class="resonance-row__title" id="<?php echo esc_attr( 'resonance-archive-title-' . $type ); ?>">
							<?php echo esc_html( $group['label'] ); ?>
						</h2>
					</div>

					<div class="resonance-row__actions">
						<span class="resonance-row__count">
							<?php
							printf(
								/* translators: %s: number of items. */
								esc_html( _n( '%s résultat', '%s résultats', count( $group['items'] ), 'sliceofcactus' ) ),
								esc_html( number_format_i18n( count( $group['items'] ) ) )
							);
							?>
						</span>
						<div class="resonance-row__nav">
							<button class="resonance-row__nav-btn resonance-row__nav-btn--prev" type="button" data-track="<?php echo esc_attr( $track_id ); ?>" aria-label="<?php esc_attr_e( 'Défiler vers la gauche', 'sliceofcactus' ); ?>">‹</button>
							<button class="resonance-row__nav-btn resonance-row__nav-btn--next" type="button" data-track="<?php echo esc_attr( $track_id ); ?>" aria-label="<?php esc_attr_e( 'Défiler vers la droite', 'sliceofcactus' ); ?>">›</button>
						</div>
					</div>
				</div>

				<div class="resonance-row__track" id="<?php echo esc_attr( $track_id ); ?>">
					<?php foreach ( $group['items'] as $item ) : ?>
						<?php
						if ( 'photo' === $type ) {
							$cover_id   = soc_get_photo_cover_id( $item->ID );
							$narrations = soc_get_photo_narrations( $item->ID );
							$sub_label  = ! empty( $narrations ) ? $narrations[0]->name : __( 'Photo', 'sliceofcactus' );
						} elseif ( 'creation' === $type ) {
							$cover_id  = soc_get_creation_cover_id( $item->ID );
							$rubrique  = soc_get_creation_rubrique( $item->ID );
							$sub_label = $rubrique ? $rubrique->name : __( 'Atelier', 'sliceofcactus' );
						} else {
							$cover_id  = absint( get_post_thumbnail_id( $item->ID ) );
							$date      = soc_get_recit_date_label( $item->ID );
							$sub_label = '' !== $date ? $date : __( 'Récit', 'sliceofcactus' );
						}
						?>
						<a class="more-series__card resonance-row__card" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
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
			</section>
		<?php endforeach; ?>

	<?php endif; ?>

	<div class="resonance-archive__footer">
		<a class="back-link" href="<?php echo esc_url( home_url( '/resonances/' ) ); ?>">
			<?php esc_html_e( '‹ Toutes les résonances', 'sliceofcactus' ); ?>
		</a>
	</div>

</main>
<?php
get_footer();