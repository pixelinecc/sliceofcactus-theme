<?php
/**
 * Creation_type taxonomy archive: shared markup for the /dessin and
 * /coloriage book grids.
 *
 * Migrated from sliceofcactus-astro/src/pages/dessin/index.astro and
 * coloriage/index.astro, which are near-identical besides copy and the
 * optional book jaquette badge — one template covers both, matching the
 * single-creation precedent.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rubrique_slug = soc_get_creation_archive_rubrique();
$is_coloriage  = 'coloriage' === $rubrique_slug;
$items         = soc_get_creation_archive_items( $rubrique_slug );
$total         = count( $items );
?>
<div class="rubrique-page colo">

	<div class="mag-runhead">
		<span>
			<?php
			printf(
				/* translators: %s: "Dessin" or "Coloriage". */
				esc_html__( 'Slice of Cactus — %s', 'sliceofcactus' ),
				'<b>' . ( $is_coloriage ? esc_html__( 'Coloriage', 'sliceofcactus' ) : esc_html__( 'Dessin', 'sliceofcactus' ) ) . '</b>'
			);
			?>
		</span>
		<span><?php esc_html_e( 'Trait du camélon', 'sliceofcactus' ); ?></span>
		<span>
			<?php echo $is_coloriage ? esc_html__( 'tous les livres coloriés', 'sliceofcactus' ) : esc_html__( 'Carnet de croquis', 'sliceofcactus' ); ?>
		</span>
	</div>

	<header class="mag-masthead">
		<?php if ( $is_coloriage ) : ?>
			<h1 class="mag-masthead__title">
				<?php esc_html_e( 'Coloriages', 'sliceofcactus' ); ?>
				<em><?php esc_html_e( 'les livres à colorier, et les pages qu\'on y remplit', 'sliceofcactus' ); ?></em>
			</h1>
			<div class="mag-masthead__lead" data-reveal>
				<p>
					<span class="drop">I</span>
					<?php esc_html_e( 'ci, pas de règle des trente-six. Le coloriage prend ses aises : un livre de planches dessinées, puis la couleur qui s\'y invite, page après page, crayon après crayon. On sort volontiers des lignes.', 'sliceofcactus' ); ?>
				</p>
			</div>
		<?php else : ?>
			<h1 class="mag-masthead__title">
				<?php esc_html_e( 'Dessins', 'sliceofcactus' ); ?>
				<em><?php esc_html_e( 'croquis, aquarelle, digital, pastel — par technique', 'sliceofcactus' ); ?></em>
			</h1>
			<div class="mag-masthead__lead" data-reveal>
				<p>
					<span class="drop">L</span>
					<?php esc_html_e( 'e dessin sous trait du camélon, rangé par technique : feutres et crayons croqués sur le vif, aquarelle, dessin digital, pastel gras… Choisissez une matière pour en voir les planches.', 'sliceofcactus' ); ?>
				</p>
			</div>
		<?php endif; ?>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer le dessin', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'creation' ) ); ?>">
				<?php esc_html_e( 'Tout', 'sliceofcactus' ); ?>
			</a>
			<a
				href="<?php echo esc_url( soc_get_creation_rubrique_archive_link( 'dessin' ) ); ?>"
				<?php echo ! $is_coloriage ? 'class="is-active" aria-current="page"' : ''; ?>
			>
				<?php esc_html_e( 'Dessins', 'sliceofcactus' ); ?>
			</a>
			<a
				href="<?php echo esc_url( soc_get_creation_rubrique_archive_link( 'coloriage' ) ); ?>"
				<?php echo $is_coloriage ? 'class="is-active" aria-current="page"' : ''; ?>
			>
				<?php esc_html_e( 'Coloriages', 'sliceofcactus' ); ?>
			</a>
		</div>
	</div>

	<div class="mag-sommaire">
		<h2>
			<?php echo $is_coloriage ? esc_html__( 'Les livres à colorier', 'sliceofcactus' ) : esc_html__( 'Les techniques', 'sliceofcactus' ); ?>
		</h2>
		<span
			id="soc-creation-count"
			data-noun-singular="<?php echo esc_attr( $is_coloriage ? __( 'livre', 'sliceofcactus' ) : __( 'technique', 'sliceofcactus' ) ); ?>"
			data-noun-plural="<?php echo esc_attr( $is_coloriage ? __( 'livres', 'sliceofcactus' ) : __( 'techniques', 'sliceofcactus' ) ); ?>"
		>
			<?php
			if ( $is_coloriage ) {
				printf(
					/* translators: %s: number of books. */
					esc_html( _n( '%s livre', '%s livres', $total, 'sliceofcactus' ) ),
					esc_html( number_format_i18n( $total ) )
				);
			} else {
				printf(
					/* translators: %s: number of techniques. */
					esc_html( _n( '%s technique', '%s techniques', $total, 'sliceofcactus' ) ),
					esc_html( number_format_i18n( $total ) )
				);
			}
			?>
		</span>
	</div>

	<p class="colo-intro" data-reveal>
		<?php
		echo $is_coloriage
			? esc_html__( 'Chaque livre est une collection de planches à colorier — je les remplis de couleurs, page après page. Ouvrez-en un pour voir ce que ça donne.', 'sliceofcactus' )
			: esc_html__( 'Chaque technique réunit une série de dessins — feutres, aquarelle, digital, pastel. Ouvrez-en une pour voir les planches.', 'sliceofcactus' );
		?>
	</p>

	<div class="rubchips" id="soc-medium-chips"></div>

	<section class="book-grid" id="soc-creation-grid">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$cover_id    = soc_get_creation_cover_id( $item->ID );
			$plate_count = count( soc_get_creation_gallery_ids( $item->ID ) );
			$technique   = soc_get_creation_technique_label( $item->ID );
			$book        = soc_get_creation_book( $item->ID );
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
								'alt'     => $is_coloriage
									? sprintf(
										/* translators: %s: creation title. */
										__( 'Page coloriée — %s', 'sliceofcactus' ),
										get_the_title( $item )
									)
									: get_the_title( $item ),
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
						<?php
						if ( $is_coloriage ) {
							echo esc_html( ! empty( $book['publisher'] ) ? $book['publisher'] : __( 'Slice of Cactus', 'sliceofcactus' ) );
						} else {
							echo esc_html( '' !== $technique ? $technique : __( 'Dessin', 'sliceofcactus' ) );
						}
						?>
					</span>
					<h3 class="book-card__title"><?php echo esc_html( get_the_title( $item ) ); ?></h3>
					<p class="book-card__meta"><?php echo esc_html( soc_get_creation_intro( $item->ID ) ); ?></p>
				</div>
			</a>
		<?php endforeach; ?>
	</section>

</div>
