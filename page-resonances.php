<?php
/**
 * Template Name: Résonances
 *
 * Parent overview of every résonance term: same .mag-runhead/.mag-masthead
 * and .view-switch/.view-toggle as the other rubrique pages
 * (archive-creation.php, page-projet-52.php) — "Toutes" is this page, each
 * other tab a specific term. Below, one horizontal-scroll row per term
 * (soc_get_resonance_rows()), mixing Photo/Création/Récit, with its own
 * intro (soc_resonance_intro ACF field, not the native term description)
 * and a "Tout voir" link to that term's own archive (taxonomy-resonance.php),
 * which still groups by post type.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$rows = soc_get_resonance_rows( 8 );
?>
<main id="main-content" class="soc-resonances rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Résonances', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Boussole éditoriale transversale', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Photo, Création, Récits', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'Résonances', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'relie Photo, Création et Récits', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead">
			<p>
				<span class="drop">C</span>
				<?php esc_html_e( 'haque résonance relie des créations d\'univers différents autour d\'une même idée. Faites défiler une ligne pour la parcourir, ou ouvrez-la pour tout voir.', 'sliceofcactus' ); ?>
			</p>
		</div>
	</header>

	<?php if ( empty( $rows ) ) : ?>

		<p class="soc-empty-note"><?php esc_html_e( 'Aucune résonance n\'est encore reliée à un article.', 'sliceofcactus' ); ?></p>

	<?php else : ?>

		<div class="view-switch">
			<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer les résonances', 'sliceofcactus' ); ?>">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="is-active" aria-current="page">
					<?php esc_html_e( 'Toutes', 'sliceofcactus' ); ?>
				</a>
				<?php foreach ( $rows as $row ) : ?>
					<a href="<?php echo esc_url( get_term_link( $row['term'] ) ); ?>">
						<?php echo esc_html( $row['term']->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<?php foreach ( $rows as $row ) : ?>
			<?php
			$term_id   = $row['term']->term_id;
			$track_id  = 'resonance-track-' . $term_id;
			$term_link = get_term_link( $row['term'] );
			$intro     = function_exists( 'get_field' ) ? get_field( 'soc_resonance_intro', 'resonance_' . $term_id ) : '';
			$intro     = is_string( $intro ) ? trim( $intro ) : '';
			?>
			<section class="resonance-row" aria-labelledby="<?php echo esc_attr( 'resonance-row-title-' . $term_id ); ?>">
				<div class="resonance-row__head">
					<div class="resonance-row__intro">
						<h2 class="resonance-row__title" id="<?php echo esc_attr( 'resonance-row-title-' . $term_id ); ?>">
							<?php echo esc_html( $row['term']->name ); ?>
						</h2>
						<?php if ( '' !== $intro ) : ?>
							<p class="resonance-row__desc"><?php echo esc_html( $intro ); ?></p>
						<?php endif; ?>
					</div>

					<div class="resonance-row__actions">
						<a class="resonance-row__more" href="<?php echo esc_url( $term_link ); ?>">
							<?php esc_html_e( 'Tout voir', 'sliceofcactus' ); ?>
							<span aria-hidden="true">→</span>
						</a>
						<div class="resonance-row__nav">
							<button class="resonance-row__nav-btn resonance-row__nav-btn--prev" type="button" data-track="<?php echo esc_attr( $track_id ); ?>" aria-label="<?php esc_attr_e( 'Défiler vers la gauche', 'sliceofcactus' ); ?>">‹</button>
							<button class="resonance-row__nav-btn resonance-row__nav-btn--next" type="button" data-track="<?php echo esc_attr( $track_id ); ?>" aria-label="<?php esc_attr_e( 'Défiler vers la droite', 'sliceofcactus' ); ?>">›</button>
						</div>
					</div>
				</div>

				<div class="resonance-row__track" id="<?php echo esc_attr( $track_id ); ?>">
					<?php foreach ( $row['items'] as $entry ) : ?>
						<a class="more-series__card resonance-row__card" href="<?php echo esc_url( get_permalink( $entry['post'] ) ); ?>">
							<?php if ( $entry['cover_id'] ) : ?>
								<div class="more-series__thumb">
									<?php
									echo wp_get_attachment_image(
										$entry['cover_id'],
										'medium_large',
										false,
										array(
											'class'   => 'more-series__plate',
											'alt'     => get_the_title( $entry['post'] ),
											'loading' => 'lazy',
										)
									);
									?>
								</div>
							<?php endif; ?>
							<div class="more-series__cap">
								<span class="more-series__title"><?php echo esc_html( get_the_title( $entry['post'] ) ); ?></span>
								<span class="more-series__n"><?php echo esc_html( $entry['kind'] ); ?></span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>

	<?php endif; ?>

</main>
<?php
get_footer();
