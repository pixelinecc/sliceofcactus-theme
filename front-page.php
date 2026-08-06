<?php
/**
 * Front page, migrated from sliceofcactus-astro/src/pages/index.astro.
 *
 * The hero polaroids and "36 poses" filmstrip are fabricated from
 * picsum.photos placeholders in Astro, with no real data behind them —
 * here they're built from real, recently published content instead (see
 * soc_get_home_hero_polaroids(), soc_get_home_featured_photo() in
 * inc/queries.php), gracefully omitting whatever category has nothing
 * published yet.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$polaroids = soc_get_home_hero_polaroids();

$universe_photo  = soc_get_photos_by_narration( 'voyage', 1 );
$universe_dessin = soc_get_creation_archive_items( 'dessin' );
$universe_recit  = soc_get_recit_archive_items();

$universe_panels = array(
	array(
		'href'   => get_post_type_archive_link( 'photo' ),
		'color'  => 'var(--accent-photo)',
		'image'  => ! empty( $universe_photo ) ? soc_get_photo_cover_id( $universe_photo[0]->ID ) : 0,
		'num'    => '01',
		'kicker' => __( 'Photographie · 36 poses', 'sliceofcactus' ),
		'title'  => __( 'Photo', 'sliceofcactus' ),
		'desc'   => __( 'Voyage, lifestyle, portraits, noir & blanc — jamais plus de trente-six images par série.', 'sliceofcactus' ),
		'cta'    => __( 'Voir les séries →', 'sliceofcactus' ),
	),
	array(
		'href'   => get_post_type_archive_link( 'creation' ),
		'color'  => 'var(--accent-atelier)',
		'image'  => ! empty( $universe_dessin ) ? soc_get_creation_cover_id( $universe_dessin[0]->ID ) : 0,
		'num'    => '02',
		'kicker' => __( 'Dessin & coloriage · trait du camélon', 'sliceofcactus' ),
		'title'  => __( 'Atelier', 'sliceofcactus' ),
		'desc'   => __( 'Croquis aux feutres, carnets et coloriages qui débordent volontiers des lignes.', 'sliceofcactus' ),
		'cta'    => __( 'Ouvrir les carnets →', 'sliceofcactus' ),
	),
	array(
		'href'   => get_post_type_archive_link( 'recit' ),
		'color'  => 'var(--accent-recit)',
		'image'  => ! empty( $universe_recit ) ? absint( get_post_thumbnail_id( $universe_recit[0]->ID ) ) : 0,
		'num'    => '03',
		'kicker' => __( 'Carnets d\'écriture', 'sliceofcactus' ),
		'title'  => __( 'Récits', 'sliceofcactus' ),
		'desc'   => __( 'Le contexte, l\'anecdote, la lumière d\'un matin — en marge des images.', 'sliceofcactus' ),
		'cta'    => __( 'Lire les récits →', 'sliceofcactus' ),
	),
);

$explore_voyage = soc_get_voyage_map_destinations();
$explore_color  = soc_get_color_your_life_series();
$explore_p52    = soc_get_photos_by_narration( 'projet-52', 1 );

$explore_tiles = array(
	array(
		'class'  => ' xtile--lead',
		'href'   => home_url( '/voyage-carte/' ),
		'color'  => 'var(--accent-carte)',
		'image'  => ( ! empty( $explore_voyage ) && ! empty( $explore_voyage[0]['series'] ) )
			? soc_get_photo_cover_id( $explore_voyage[0]['series'][0]->ID )
			: 0,
		'kicker' => __( 'Par lieu', 'sliceofcactus' ),
		'title'  => __( 'La carte', 'sliceofcactus' ),
		'desc'   => __( 'Chaque point, une série de voyage — d\'une côte à l\'autre, là où la pellicule s\'est arrêtée.', 'sliceofcactus' ),
	),
	array(
		'class'  => '',
		'href'   => home_url( '/color-your-life/' ),
		'color'  => 'var(--accent-color-your-life)',
		'image'  => ! empty( $explore_color ) ? soc_get_photo_cover_id( $explore_color[0]->ID ) : 0,
		'kicker' => __( 'Par couleur', 'sliceofcactus' ),
		'title'  => __( 'Color Your Life', 'sliceofcactus' ),
		'desc'   => __( 'Les images rangées par teinte dominante.', 'sliceofcactus' ),
	),
	array(
		'class'  => '',
		'href'   => home_url( '/projet-52/' ),
		'color'  => 'var(--accent-p52)',
		'image'  => ! empty( $explore_p52 ) ? soc_get_photo_cover_id( $explore_p52[0]->ID ) : 0,
		'kicker' => __( 'Semaine après semaine', 'sliceofcactus' ),
		'title'  => __( 'Projet 52', 'sliceofcactus' ),
		'desc'   => __( 'Une photographie par semaine, toute l\'année.', 'sliceofcactus' ),
	),
);

$featured_photo    = soc_get_home_featured_photo();
$filmstrip_images  = array();
$filmstrip_title   = '';
$filmstrip_kicker  = __( 'Photo', 'sliceofcactus' );
$filmstrip_permalink = '';

if ( $featured_photo instanceof WP_Post ) {
	$gallery_ids = function_exists( 'get_field' )
		? array_filter( array_map( 'absint', (array) get_field( 'soc_photo_gallery', $featured_photo->ID ) ) )
		: array();

	if ( empty( $gallery_ids ) && has_post_thumbnail( $featured_photo ) ) {
		$gallery_ids = array( get_post_thumbnail_id( $featured_photo ) );
	}

	$filmstrip_images    = array_slice( array_values( $gallery_ids ), 0, 36 );
	$filmstrip_title     = get_the_title( $featured_photo );
	$filmstrip_permalink = get_permalink( $featured_photo );

	$narrations = soc_get_photo_narrations( $featured_photo->ID );

	if ( ! empty( $narrations ) ) {
		$filmstrip_kicker = $narrations[0]->name;
	}
}

$home_recits = soc_get_home_recits( 3 );
?>
<main id="main-content" class="soc-front-page">

	<div class="preloader" id="preloader">
		<div class="preloader__inner">
			<div class="preloader__frame">
				<span class="preloader__count" id="preCount">00</span>
				<span class="preloader__slash">/ 36</span>
			</div>
			<div class="preloader__label"><?php esc_html_e( 'chargement de la pellicule', 'sliceofcactus' ); ?></div>
		</div>
	</div>

	<div class="hero-viewport">
		<section class="hero" id="top">
			<div class="hero__blob" aria-hidden="true"></div>
			<div class="hero__blob hero__blob--2" aria-hidden="true"></div>
			<div class="hero__blob hero__blob--3" aria-hidden="true"></div>

			<div class="hero__content">
				<div class="hero__intro">
					<p class="hero__kicker" data-reveal><?php esc_html_e( 'Photographe & dessinatrice · atelier d\'images', 'sliceofcactus' ); ?></p>
					<h1 class="hero__title">
						<span data-reveal>Slice</span>
						<span data-reveal class="is-outline">of</span>
						<span data-reveal>Cactus.</span>
					</h1>
					<p class="hero__lead" data-reveal>
						<?php
						echo wp_kses(
							__( 'Slice of Cactus réunit mes deux mains : la <strong>photographie</strong>, sous le label <strong>36 poses</strong>, et le <strong>dessin</strong> — croquis, feutres et coloriages, sous <em>trait du camélon</em>. Et des <strong>récits</strong>, en marge des images.', 'sliceofcactus' ),
							array(
								'strong' => array(),
								'em'     => array(),
							)
						);
						?>
					</p>
					<div class="hero__actions" data-reveal>
						<a href="#univers" class="btn btn--fill" data-magnetic><?php esc_html_e( 'Explorer les univers', 'sliceofcactus' ); ?></a>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'recit' ) ); ?>" class="btn btn--line" data-magnetic><?php esc_html_e( 'Lire les récits →', 'sliceofcactus' ); ?></a>
					</div>
				</div>

				<?php if ( ! empty( $polaroids ) ) : ?>
					<div class="polas" data-reveal>
						<?php foreach ( $polaroids as $polaroid ) : ?>
							<a class="pola" href="<?php echo esc_url( $polaroid['href'] ); ?>" style="<?php echo esc_attr( $polaroid['style'] ); ?>">
								<?php
								echo wp_get_attachment_image(
									$polaroid['image_id'],
									'medium',
									false,
									array(
										'alt'     => '',
										'loading' => 'eager',
									)
								);
								?>
								<span class="pola__cap"><?php echo esc_html( $polaroid['caption'] ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="hero__meta">
				<span><?php esc_html_e( 'Photo · Dessin · Récits', 'sliceofcactus' ); ?></span>
				<span class="hero__meta-dot"></span>
				<span><?php esc_html_e( 'Slow & argentique', 'sliceofcactus' ); ?></span>
			</div>

			<a class="hero__scroll" href="#univers" aria-label="<?php esc_attr_e( 'Défiler vers le bas', 'sliceofcactus' ); ?>">
				<span><?php esc_html_e( 'Défiler', 'sliceofcactus' ); ?></span>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M12 4v14"></path><path d="M6 12l6 6 6-6"></path>
				</svg>
			</a>
		</section>

		<div class="typewriter" aria-hidden="true">
			<span class="typewriter__text" id="tw"></span><span class="typewriter__caret"></span>
		</div>
	</div>

	<section class="universe" id="univers">
		<div class="section-head">
			<span class="section-head__num">01</span>
			<h2 class="section-head__title" data-reveal><?php esc_html_e( 'Trois univers,', 'sliceofcactus' ); ?><br><?php esc_html_e( 'une même main', 'sliceofcactus' ); ?></h2>
			<p class="section-head__sub" data-reveal>
				<?php
				echo wp_kses(
					__( 'La photo sous <em>36 poses</em>, le dessin sous <em>trait du camélon</em>, et les récits en marge des images. Survolez pour entrer.', 'sliceofcactus' ),
					array( 'em' => array() )
				);
				?>
			</p>
		</div>
		<div class="universe__panels">
			<?php foreach ( $universe_panels as $panel ) : ?>
				<a
					class="upanel"
					href="<?php echo esc_url( $panel['href'] ); ?>"
					style="--c: <?php echo esc_attr( $panel['color'] ); ?>;<?php echo $panel['image'] ? ' --img: url(' . esc_url( wp_get_attachment_image_url( $panel['image'], 'large' ) ) . ')' : ''; ?>"
				>
					<span class="upanel__bg" aria-hidden="true"></span>
					<span class="upanel__num"><?php echo esc_html( $panel['num'] ); ?></span>
					<span class="upanel__k"><?php echo esc_html( $panel['kicker'] ); ?></span>
					<h3 class="upanel__t"><?php echo esc_html( $panel['title'] ); ?></h3>
					<p class="upanel__d"><?php echo esc_html( $panel['desc'] ); ?></p>
					<span class="upanel__cta"><?php echo esc_html( $panel['cta'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="explore" id="explorer">
		<div class="section-head">
			<span class="section-head__num">02</span>
			<h2 class="section-head__title" data-reveal><?php esc_html_e( 'Explorer autrement', 'sliceofcactus' ); ?></h2>
			<p class="section-head__sub" data-reveal><?php esc_html_e( 'D\'autres façons de parcourir les images : par lieu, par couleur, semaine après semaine.', 'sliceofcactus' ); ?></p>
		</div>
		<div class="explore__grid">
			<?php foreach ( $explore_tiles as $tile ) : ?>
				<a
					class="xtile<?php echo esc_attr( $tile['class'] ); ?>"
					href="<?php echo esc_url( $tile['href'] ); ?>"
					style="--c: <?php echo esc_attr( $tile['color'] ); ?>;<?php echo $tile['image'] ? ' --img: url(' . esc_url( wp_get_attachment_image_url( $tile['image'], 'large' ) ) . ')' : ''; ?>"
				>
					<span class="xtile__bg" aria-hidden="true"></span>
					<span class="xtile__go" aria-hidden="true">↗</span>
					<span class="xtile__k"><?php echo esc_html( $tile['kicker'] ); ?></span>
					<h3 class="xtile__t"><?php echo esc_html( $tile['title'] ); ?></h3>
					<p class="xtile__d"><?php echo esc_html( $tile['desc'] ); ?></p>
					<span class="xtile__bar" aria-hidden="true"></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( ! empty( $filmstrip_images ) ) : ?>
		<section class="serie" id="serie">
			<div class="section-head">
				<span class="section-head__num">03</span>
				<h2 class="section-head__title" data-reveal><?php esc_html_e( '36 poses — la série à la une', 'sliceofcactus' ); ?></h2>
				<p class="section-head__sub" data-reveal>
					<em>« <?php echo esc_html( $filmstrip_title ); ?> »</em> — <?php echo esc_html( $filmstrip_kicker ); ?> · <?php esc_html_e( 'le label photo de Slice of Cactus', 'sliceofcactus' ); ?>
				</p>
			</div>
			<div class="filmstrip" id="filmstrip" data-lightbox="lightbox">
				<?php foreach ( $filmstrip_images as $index => $image_id ) : ?>
					<a
						class="frame"
						href="<?php echo esc_url( $filmstrip_permalink ); ?>"
						data-index="<?php echo esc_attr( $index ); ?>"
						data-full="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'large' ) ); ?>"
						data-caption="<?php
						echo esc_attr(
							sprintf(
								/* translators: 1: pose number, 2: total poses, 3: series title. */
								__( 'Pose %1$s / %2$s · « %3$s »', 'sliceofcactus' ),
								str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
								str_pad( (string) count( $filmstrip_images ), 2, '0', STR_PAD_LEFT ),
								$filmstrip_title
							)
						);
						?>"
					>
						<span class="frame__num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="frame__img">
							<?php
							echo wp_get_attachment_image(
								$image_id,
								'medium_large',
								false,
								array(
									'alt'     => $filmstrip_title,
									'loading' => 'lazy',
								)
							);
							?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
			<p class="serie__hint" data-reveal><?php esc_html_e( 'Cliquez une pose pour l\'agrandir · faites défiler la pellicule →', 'sliceofcactus' ); ?></p>
		</section>
	<?php endif; ?>

	<section class="manifeste" id="manifeste">
		<div class="manifeste__blob" aria-hidden="true"></div>
		<div class="section-head">
			<span class="section-head__num">04</span>
		</div>
		<div class="manifeste__text">
			<p data-reveal><span class="drop">J</span><?php esc_html_e( 'e crois à la lenteur. À l\'image que l\'on choisit plutôt qu\'à celle que l\'on collectionne. Trente-six poses, c\'est assez pour raconter, trop peu pour se disperser.', 'sliceofcactus' ); ?></p>
			<p data-reveal><?php esc_html_e( 'Ici, pas de flux infini. Une galerie qui se visite comme un musée, se feuillette comme un magazine, respire comme une session au petit matin.', 'sliceofcactus' ); ?></p>
			<p data-reveal class="manifeste__sign">— <strong><?php esc_html_e( '36 poses', 'sliceofcactus' ); ?></strong>, <?php esc_html_e( 'le label photo de Slice of Cactus', 'sliceofcactus' ); ?></p>
		</div>
	</section>

	<?php if ( ! empty( $home_recits ) ) : ?>
		<section class="home-recits">
			<div class="section-head">
				<span class="section-head__num">05</span>
				<h2 class="section-head__title" data-reveal><?php esc_html_e( 'Récits à la une', 'sliceofcactus' ); ?></h2>
				<p class="section-head__sub" data-reveal><?php esc_html_e( 'Quelques carnets d\'écriture, à lire en marge des images.', 'sliceofcactus' ); ?></p>
			</div>
			<div class="home-recits__grid">
				<?php foreach ( $home_recits as $recit ) : ?>
					<a class="hr-card" href="<?php echo esc_url( get_permalink( $recit ) ); ?>">
						<span class="hr-card__k"><?php echo esc_html( soc_get_recit_date_label( $recit->ID ) ); ?></span>
						<h3 class="hr-card__t"><?php echo esc_html( get_the_title( $recit ) ); ?></h3>
						<p class="hr-card__ex"><?php echo esc_html( get_the_excerpt( $recit ) ); ?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

</main>

<?php if ( ! empty( $filmstrip_images ) ) : ?>
	<div
		class="lightbox<?php echo count( $filmstrip_images ) > 1 ? ' lightbox--filmstrip' : ''; ?>"
		id="lightbox"
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Visionneuse de la série à la une', 'sliceofcactus' ); ?>"
		aria-hidden="true"
	>
		<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Fermer', 'sliceofcactus' ); ?>">×</button>
		<button class="lightbox__nav lightbox__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Image précédente', 'sliceofcactus' ); ?>">‹</button>
		<figure class="lightbox__fig">
			<img alt="">
			<figcaption></figcaption>
		</figure>
		<button class="lightbox__nav lightbox__nav--next" type="button" aria-label="<?php esc_attr_e( 'Image suivante', 'sliceofcactus' ); ?>">›</button>

		<?php if ( count( $filmstrip_images ) > 1 ) : ?>
			<div class="lightbox__strip-wrap">
				<button class="lightbox__strip-nav lightbox__strip-nav--prev" type="button" aria-label="<?php esc_attr_e( 'Défiler les vignettes vers la gauche', 'sliceofcactus' ); ?>">‹</button>

				<div class="lightbox__strip" role="group" aria-label="<?php esc_attr_e( 'Navigation entre les poses', 'sliceofcactus' ); ?>">
					<?php foreach ( $filmstrip_images as $index => $image_id ) : ?>
						<button
							class="lightbox__strip__item"
							type="button"
							aria-label="<?php echo esc_attr( sprintf( __( 'Aller à la pose %s', 'sliceofcactus' ), number_format_i18n( $index + 1 ) ) ); ?>"
						>
							<?php
							echo wp_get_attachment_image(
								$image_id,
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
<?php
get_footer();