<?php
/**
 * Creation single: shared markup for dessin and coloriage.
 *
 * Migrated from sliceofcactus-astro/src/pages/dessin/[id].astro and
 * coloriage/[id].astro, which are near-identical besides the technique/book
 * text and the optional book-credit block — one template covers both,
 * matching the single-photo precedent.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id        = get_the_ID();
$rubrique       = soc_get_creation_rubrique( $post_id );
$is_coloriage   = $rubrique && 'coloriage' === $rubrique->slug;
$technique      = soc_get_creation_technique_label( $post_id );
$book           = soc_get_creation_book( $post_id );
$accroche       = soc_get_creation_intro( $post_id );
$resonances     = get_the_terms( $post_id, 'resonance' );
$resonances     = is_array( $resonances ) ? $resonances : array();
$gallery_ids    = soc_get_creation_gallery_ids( $post_id );
$suggestions    = soc_get_creation_suggestions( $post_id );
$related_recits = soc_get_creation_related_recits( $post_id );
$accent_color   = soc_get_creation_accent_color( $post_id );
$sheet_id       = 'soc-creation-sheet-' . $post_id;
$lightbox_id    = 'soc-creation-lightbox-' . $post_id;

if ( $is_coloriage ) {
	$drop_source  = get_the_title();
	$lead_text    = ! empty( $book['title'] ) ? $book['title'] : $accroche;
	$card_by      = __( 'feutres & crayons', 'sliceofcactus' );
	$sommaire_sub = sprintf(
		/* translators: %s: creation title. */
		__( '%s, colorié', 'sliceofcactus' ),
		get_the_title()
	);
} else {
	$drop_source  = '' !== $technique ? $technique : __( 'Dessin', 'sliceofcactus' );
	$lead_text    = '' !== $technique
		? sprintf(
			/* translators: %s: technique name. */
			__( 'Technique : %s.', 'sliceofcactus' ),
			$technique
		)
		: '';
	$card_by      = '' !== $technique ? $technique : __( 'dessin', 'sliceofcactus' );
	$sommaire_sub = '' !== $technique ? $technique : __( 'Dessin', 'sliceofcactus' );
}

$drop_letter = function_exists( 'mb_substr' ) ? mb_substr( $drop_source, 0, 1 ) : substr( $drop_source, 0, 1 );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rubrique-page colo soc-creation' ); ?>>
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
		<span>
			<?php
			echo $is_coloriage
				? esc_html__( 'Carnet de voyage · hors label 36 poses', 'sliceofcactus' )
				: esc_html__( 'Trait du camélon · hors label 36 poses', 'sliceofcactus' );
			?>
		</span>
		<span><?php the_title(); ?></span>
	</div>

	<a
		class="back-link"
		href="<?php echo esc_url( soc_get_creation_rubrique_archive_link( $is_coloriage ? 'coloriage' : 'dessin' ) ); ?>"
	>
		<?php echo $is_coloriage ? esc_html__( '‹ Tous les coloriages', 'sliceofcactus' ) : esc_html__( '‹ Toutes les techniques', 'sliceofcactus' ); ?>
	</a>

	<header class="mag-masthead">
		<h1 class="mag-masthead__title">
			<?php the_title(); ?>
			<?php if ( '' !== $accroche ) : ?>
				<em><?php echo esc_html( $accroche ); ?></em>
			<?php endif; ?>
		</h1>

		<?php if ( '' !== $lead_text ) : ?>
			<div class="mag-masthead__lead">
				<p>
					<span class="drop"><?php echo esc_html( $drop_letter ); ?></span>
					<?php echo esc_html( $lead_text ); ?>
				</p>
			</div>
		<?php endif; ?>
	</header>

	<div class="view-switch">
		<div class="view-toggle" role="tablist" aria-label="<?php esc_attr_e( 'Explorer le dessin', 'sliceofcactus' ); ?>">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'creation' ) ); ?>">
				<?php esc_html_e( 'Toutes', 'sliceofcactus' ); ?>
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

	<?php if ( $is_coloriage && ! empty( $book['cover'] ) ) : ?>
		<aside class="book-credit">
			<?php
			echo wp_get_attachment_image(
				$book['cover'],
				'thumbnail',
				false,
				array(
					'alt' => sprintf(
						/* translators: %s: book title. */
						__( 'Jaquette du livre %s', 'sliceofcactus' ),
						! empty( $book['title'] ) ? $book['title'] : get_the_title()
					),
				)
			);
			?>
			<p class="book-credit__txt">
				<b><?php echo esc_html( ! empty( $book['title'] ) ? $book['title'] : get_the_title() ); ?></b>
				<?php if ( ! empty( $book['author'] ) ) : ?>
					<?php
					printf(
						/* translators: %s: book author. */
						esc_html__( 'd’après le livre de %s', 'sliceofcactus' ),
						esc_html( $book['author'] )
					);
					?>
				<?php endif; ?>
				<?php if ( ! empty( $book['publisher'] ) ) : ?>
					· <?php echo esc_html( $book['publisher'] ); ?>
				<?php endif; ?>
			</p>
		</aside>
	<?php endif; ?>

	<div class="mag-sommaire">
		<h2><?php esc_html_e( 'Les planches', 'sliceofcactus' ); ?></h2>
		<span><?php echo esc_html( $sommaire_sub ); ?></span>
	</div>

	<section
		class="colo-grid"
		id="<?php echo esc_attr( $sheet_id ); ?>"
		data-lightbox="<?php echo esc_attr( $lightbox_id ); ?>"
	>
		<?php foreach ( $gallery_ids as $attachment_id ) : ?>
			<?php $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ); ?>
			<a class="colo-card" href="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'full' ) ); ?>">
				<div class="colo-card__img">
					<?php
					echo wp_get_attachment_image(
						$attachment_id,
						'large',
						false,
						array(
							'alt'     => $alt,
							'loading' => 'lazy',
						)
					);
					?>
				</div>
				<div class="colo-card__cap">
					<span>
						<span class="colo-card__title"><?php echo esc_html( $alt ); ?></span>
						<span class="colo-card__by"><?php echo esc_html( $card_by ); ?></span>
					</span>
					<span class="colo-card__dot" style="--k: <?php echo esc_attr( $accent_color ); ?>"></span>
				</div>
			</a>
		<?php endforeach; ?>
	</section>

	<?php if ( ! empty( $suggestions ) ) : ?>
		<section class="more-series">
			<div class="mag-sommaire">
				<h2>
					<?php echo $is_coloriage ? esc_html__( 'Autres livres', 'sliceofcactus' ) : esc_html__( 'Autres techniques', 'sliceofcactus' ); ?>
				</h2>
				<span>
					<?php echo $is_coloriage ? esc_html__( 'à colorier', 'sliceofcactus' ) : esc_html__( 'dessinées', 'sliceofcactus' ); ?>
				</span>
			</div>

			<div class="more-series__grid">
				<?php foreach ( $suggestions as $other ) : ?>
					<?php
					$other_cover = soc_get_creation_cover_id( $other->ID );
					$other_book  = soc_get_creation_book( $other->ID );
					?>
					<a class="more-series__card" href="<?php echo esc_url( get_permalink( $other ) ); ?>">
						<div class="more-series__thumb">
							<?php if ( $other_cover ) : ?>
								<?php
								echo wp_get_attachment_image(
									$other_cover,
									'large',
									false,
									array(
										'class'   => 'more-series__plate',
										'alt'     => get_the_title( $other ),
										'loading' => 'lazy',
									)
								);
								?>
							<?php endif; ?>

							<?php if ( $is_coloriage && ! empty( $other_book['cover'] ) ) : ?>
								<?php
								echo wp_get_attachment_image(
									$other_book['cover'],
									'thumbnail',
									false,
									array(
										'class'   => 'more-series__badge',
										'alt'     => sprintf(
											/* translators: %s: creation title. */
											__( 'Jaquette %s', 'sliceofcactus' ),
											get_the_title( $other )
										),
										'loading' => 'lazy',
									)
								);
								?>
							<?php endif; ?>
						</div>
						<div class="more-series__cap">
							<span class="more-series__title"><?php echo esc_html( get_the_title( $other ) ); ?></span>
							<span class="more-series__n"><?php esc_html_e( 'voir', 'sliceofcactus' ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! empty( $related_recits ) ) : ?>
		<p class="linked-note is-on">
			<?php esc_html_e( 'Cette création est racontée dans :', 'sliceofcactus' ); ?>
			<?php foreach ( $related_recits as $index => $recit ) : ?>
				<?php echo 0 < $index ? ', ' : ' '; ?>
				<a href="<?php echo esc_url( get_permalink( $recit ) ); ?>"><?php echo esc_html( get_the_title( $recit ) ); ?></a>
			<?php endforeach; ?>
			.
		</p>
	<?php endif; ?>

	<?php if ( ! empty( $resonances ) ) : ?>
		<section class="serie-resonances" aria-label="<?php esc_attr_e( 'Résonances', 'sliceofcactus' ); ?>">
			<span class="serie-resonances__label"><?php esc_html_e( 'Résonances :', 'sliceofcactus' ); ?></span>
			<ul class="serie-resonances__list">
				<?php foreach ( $resonances as $resonance ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $resonance ) ); ?>">
							<?php echo esc_html( $resonance->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</article>

<?php if ( ! empty( $gallery_ids ) ) : ?>
	<div
		class="lightbox<?php echo count( $gallery_ids ) > 1 ? ' lightbox--filmstrip' : ''; ?>"
		id="<?php echo esc_attr( $lightbox_id ); ?>"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Visionneuse de la création', 'sliceofcactus' ); ?>"
		aria-hidden="true"
	>
		<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Fermer', 'sliceofcactus' ); ?>">×</button>
		<button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Planche précédente', 'sliceofcactus' ); ?>">‹</button>
		<figure class="lightbox__fig">
			<img alt="">
			<figcaption></figcaption>
		</figure>
		<button class="lightbox__nav lightbox__nav--next" type="button" aria-label="<?php esc_attr_e( 'Planche suivante', 'sliceofcactus' ); ?>">›</button>

		<?php if ( count( $gallery_ids ) > 1 ) : ?>
			<div class="lightbox__strip-wrap">
				<button class="lightbox__strip-nav lightbox__strip-nav--prev" type="button" aria-label="<?php esc_attr_e( 'Défiler les vignettes vers la gauche', 'sliceofcactus' ); ?>">‹</button>

				<div class="lightbox__strip" role="group" aria-label="<?php esc_attr_e( 'Navigation entre les planches', 'sliceofcactus' ); ?>">
					<?php foreach ( array_values( $gallery_ids ) as $index => $attachment_id ) : ?>
						<button
							class="lightbox__strip__item"
							type="button"
							aria-label="<?php echo esc_attr( sprintf( __( 'Aller à la planche %s', 'sliceofcactus' ), number_format_i18n( $index + 1 ) ) ); ?>"
						>
							<?php
							echo wp_get_attachment_image(
								$attachment_id,
								'thumbnail',
								false,
								array(
									'loading' => 'lazy',
									'alt'     => '',
								)
							);
							?>
						</button>
					<?php endforeach; ?>
				</div>

				<button class="lightbox__strip-nav lightbox__strip-nav--next" type="button" aria-label="<?php esc_attr_e( 'Défiler les vignettes vers la droite', 'sliceofcactus' ); ?>">›</button>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>