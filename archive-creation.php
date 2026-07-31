<?php
/**
 * Atelier archive: every dessin and coloriage mixed, most recent first.
 *
 * Public-facing rubrique renamed from "Création" to "Atelier" — the CPT's
 * own key ('creation') and every internal function/class/file name stay
 * untouched, only labels and the rewrite slug (now /atelier/) changed.
 *
 * No Astro equivalent (Astro only has separate dessin/index.astro and
 * coloriage/index.astro, still served here by taxonomy-creation_type.php)
 * — this is a new unified overview, validated explicitly. Structured like
 * archive-photo.php (masthead, view-switch, filter chips, grid), using the
 * book-card markup already established in
 * template-parts/taxonomy/creation-type-book-grid.php for visual
 * consistency across every Atelier page. Filter chips run on the medium
 * taxonomy (technique: aquarelle, feutres…) — the rubrique split
 * (dessin/coloriage) is already covered by the view-switch tabs above.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$items = soc_get_creation_archive_series();
$total = count( $items );
?>
<main id="main-content" class="soc-creation-archive rubrique-page colo">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Atelier', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Trait du camélon · hors label 36 poses', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'dessins & coloriages', 'sliceofcactus' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php esc_html_e( 'Atelier', 'sliceofcactus' ); ?>
			<em><?php esc_html_e( 'tous les carnets, dessins et coloriages confondus', 'sliceofcactus' ); ?></em>
		</h1>
		<div class="mag-masthead__lead">
			<p>
				<span class="drop">T</span>
				<?php esc_html_e( 'oute la matière graphique de Slice of Cactus au même endroit : croquis, aquarelles, planches numériques et livres à colorier. Choisissez un type pour affiner.', 'sliceofcactus' ); ?>
			</p>
		</div>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer l\'atelier', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'creation' ) ); ?>" class="is-active" aria-current="page">
				<?php esc_html_e( 'Toutes', 'sliceofcactus' ); ?>
			</a>
			<a href="<?php echo esc_url( soc_get_creation_rubrique_archive_link( 'dessin' ) ); ?>">
				<?php esc_html_e( 'Dessins', 'sliceofcactus' ); ?>
			</a>
			<a href="<?php echo esc_url( soc_get_creation_rubrique_archive_link( 'coloriage' ) ); ?>">
				<?php esc_html_e( 'Coloriages', 'sliceofcactus' ); ?>
			</a>
		</div>
	</div>

	<div class="mag-sommaire">
		<h2><?php esc_html_e( 'Tout l\'atelier', 'sliceofcactus' ); ?></h2>
		<span id="soc-creation-count">
			<?php
			printf(
				/* translators: %s: number of contents. */
				esc_html( _n( '%s contenu', '%s contenus', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<div class="rubchips" id="soc-medium-chips"></div>

	<section class="book-grid" id="soc-creation-grid">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$rubrique     = soc_get_creation_rubrique( $item->ID );
			$is_coloriage = $rubrique && 'coloriage' === $rubrique->slug;
			$techniques   = get_the_terms( $item->ID, 'medium' );
			$technique    = is_array( $techniques ) && ! empty( $techniques ) ? $techniques[0] : null;
			$cover_id     = soc_get_creation_cover_id( $item->ID );
			$plate_count  = count( soc_get_creation_gallery_ids( $item->ID ) );
			$book         = soc_get_creation_book( $item->ID );
			?>
			<a
				class="book-card"
				href="<?php echo esc_url( get_permalink( $item ) ); ?>"
				data-mediums="<?php echo esc_attr( soc_get_creation_mediums_json( $item->ID ) ); ?>"
			>
				<div class="book-card__cover">
					<span class="book-card__badge">
						<?php
						if ( $is_coloriage ) {
							printf(
								/* translators: %s: number of pages. */
								esc_html__( '%s pages', 'sliceofcactus' ),
								esc_html( number_format_i18n( $plate_count ) )
							);
						} else {
							printf(
								/* translators: %s: number of plates. */
								esc_html__( '%s planches', 'sliceofcactus' ),
								esc_html( number_format_i18n( $plate_count ) )
							);
						}
						?>
					</span>
					<?php if ( $cover_id ) : ?>
						<?php
						echo wp_get_attachment_image(
							$cover_id,
							'large',
							false,
							array(
								'class'   => 'book-card__plate',
								'alt'     => get_the_title( $item ),
								'loading' => 'lazy',
							)
						);
						?>
					<?php endif; ?>
					<?php if ( $is_coloriage && ! empty( $book['cover'] ) ) : ?>
						<?php
						echo wp_get_attachment_image(
							$book['cover'],
							'thumbnail',
							false,
							array(
								'class'   => 'book-card__jaq',
								'alt'     => sprintf(
									/* translators: %s: creation title. */
									__( 'Livre %s', 'sliceofcactus' ),
									get_the_title( $item )
								),
								'loading' => 'lazy',
							)
						);
						?>
					<?php endif; ?>
				</div>
				<div class="book-card__body">
					<span class="book-card__ed">
						<?php echo esc_html( $technique ? $technique->name : ( $is_coloriage ? __( 'Coloriage', 'sliceofcactus' ) : __( 'Dessin', 'sliceofcactus' ) ) ); ?>
					</span>
					<h3 class="book-card__title"><?php echo esc_html( get_the_title( $item ) ); ?></h3>
					<p class="book-card__meta"><?php echo esc_html( soc_get_creation_intro( $item->ID ) ); ?></p>
				</div>
			</a>
		<?php endforeach; ?>
	</section>

</main>
<?php
get_footer();
