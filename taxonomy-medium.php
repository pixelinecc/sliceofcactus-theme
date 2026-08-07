<?php
/**
 * Medium taxonomy archive: /medium/{slug}/, e.g. /medium/crayons-de-couleur/.
 *
 * A medium (technique: aquarelle, feutres, crayons…) isn't scoped to one
 * rubrique, so this mixes dessins and coloriages, same shape as the Atelier
 * archive (archive-creation.php) filtered down to one term instead of
 * everything — reuses its exact book-grid markup/classes so it inherits the
 * same visual charter (assets/styles/templates/taxonomy-creation-type.css)
 * instead of introducing a second look for the same content type.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term  = get_queried_object();
$items = $term instanceof WP_Term ? soc_get_creation_archive_items_by_medium( $term ) : array();
$total = count( $items );
?>
<main id="main-content" class="soc-creation-archive rubrique-page colo">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Atelier', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Trait du camélon', 'sliceofcactus' ); ?></span>
		<span><?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?></span>
	</div>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php echo esc_html( $term instanceof WP_Term ? $term->name : '' ); ?>
		</h1>
		<?php if ( $term instanceof WP_Term && '' !== $term->description ) : ?>
			<div class="mag-masthead__lead">
				<p><?php echo esc_html( $term->description ); ?></p>
			</div>
		<?php endif; ?>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer l\'atelier', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'creation' ) ); ?>">
				<?php esc_html_e( 'Tout', 'sliceofcactus' ); ?>
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
		<h2><?php esc_html_e( 'Les planches', 'sliceofcactus' ); ?></h2>
		<span>
			<?php
			printf(
				/* translators: %s: number of creations. */
				esc_html( _n( '%s création', '%s créations', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<section class="book-grid">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$rubrique     = soc_get_creation_rubrique( $item->ID );
			$is_coloriage = $rubrique && 'coloriage' === $rubrique->slug;
			$cover_id     = soc_get_creation_cover_id( $item->ID );
			$plate_count  = count( soc_get_creation_gallery_ids( $item->ID ) );
			$book         = soc_get_creation_book( $item->ID );
			?>
			<a class="book-card" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
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
						<?php echo esc_html( $is_coloriage ? __( 'Coloriage', 'sliceofcactus' ) : __( 'Dessin', 'sliceofcactus' ) ); ?>
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
