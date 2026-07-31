<?php
/**
 * Template Name: À propos
 *
 * Structured like the other bespoke rubrique pages (archive-creation.php,
 * page-projet-52.php): .mag-runhead and .mag-masthead straight from
 * assets/styles/components/magazine-hub.css, sitting directly on the body's
 * own accent-gradient background (assets/styles/base/elements.css) — no
 * paper flip, no per-section background override, only its own --accent /
 * --accent-deep pair set on the body in page-a-propos.css. The one reused
 * piece from outside that family is .article__resonance-card
 * (single-recit.css), for "Trois formes" — already exactly the small
 * colored-border card this page needs.
 *
 * "La démarche" is its own sticky-scroll section (.about-sequence): a
 * visual that swaps gradient and number as each step scrolls into view.
 * Its JS lives in assets/scripts/page-a-propos.js, its CSS alongside the
 * rest of this page in assets/styles/templates/page-a-propos.css.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$lead    = get_the_excerpt();

	$resonance_terms = get_terms(
		array(
			'taxonomy'   => 'resonance',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $resonance_terms ) ) {
		$resonance_terms = array();
	}

	usort(
		$resonance_terms,
		static function ( WP_Term $a, WP_Term $b ): int {
			$order_a = function_exists( 'get_field' ) ? (int) get_field( 'soc_resonance_order', 'resonance_' . $a->term_id ) : 0;
			$order_b = function_exists( 'get_field' ) ? (int) get_field( 'soc_resonance_order', 'resonance_' . $b->term_id ) : 0;

			return $order_a <=> $order_b;
		}
	);

	// "Trois formes": same .universe/.upanel panels as the front page's own
	// "Trois univers" section (assets/styles/components/panels.css), fed by
	// the single most recent post of each type — same cover-fetching helpers
	// front-page.php already uses, just without its narration/rubrique
	// filtering (this isn't curating one series, just picking an example).
	$latest_photo = ( new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => 1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	) )->posts;

	$latest_creation = ( new WP_Query(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => 1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	) )->posts;

	$latest_recit = soc_get_recit_archive_items();

	$forms = array(
		array(
			'num'    => '01',
			'kicker' => __( 'Photographie · 36 poses', 'sliceofcactus' ),
			'title'  => __( 'Photo', 'sliceofcactus' ),
			'desc'   => __( 'Des séries, des voyages, un projet au long cours.', 'sliceofcactus' ),
			'cta'    => __( 'Explorer Photo →', 'sliceofcactus' ),
			'url'    => get_post_type_archive_link( 'photo' ),
			'color'  => '#27513e',
			'image'  => ! empty( $latest_photo ) ? soc_get_photo_cover_id( $latest_photo[0]->ID ) : 0,
		),
		array(
			'num'    => '02',
			'kicker' => __( 'Dessin & coloriage · trait du camélon', 'sliceofcactus' ),
			'title'  => __( 'Atelier', 'sliceofcactus' ),
			'desc'   => __( 'Dessins et coloriages, un geste qui prolonge le regard.', 'sliceofcactus' ),
			'cta'    => __( 'Explorer l\'atelier →', 'sliceofcactus' ),
			'url'    => get_post_type_archive_link( 'creation' ),
			'color'  => '#e0592f',
			'image'  => ! empty( $latest_creation ) ? soc_get_creation_cover_id( $latest_creation[0]->ID ) : 0,
		),
		array(
			'num'    => '03',
			'kicker' => __( 'Carnets d\'écriture', 'sliceofcactus' ),
			'title'  => __( 'Récits', 'sliceofcactus' ),
			'desc'   => __( 'Des textes courts, un fil plutôt qu\'un compte-rendu.', 'sliceofcactus' ),
			'cta'    => __( 'Lire les récits →', 'sliceofcactus' ),
			'url'    => get_post_type_archive_link( 'recit' ),
			'color'  => '#6f4e2b',
			'image'  => ! empty( $latest_recit ) ? absint( get_post_thumbnail_id( $latest_recit[0]->ID ) ) : 0,
		),
	);

	// La démarche: each step's gradient is its color darkened ~28%, same
	// ratio as this page's own --accent / --accent-deep pair.
	$steps = array(
		array(
			'title'    => __( 'Observer', 'sliceofcactus' ),
			'text'     => __( 'Regarder autrement ce qui semblait ordinaire.', 'sliceofcactus' ),
			'gradient' => 'linear-gradient(160deg, #efa9ed 0%, #ac7aab 100%)',
		),
		array(
			'title'    => __( 'Raconter', 'sliceofcactus' ),
			'text'     => __( 'Donner une forme aux images et aux instants.', 'sliceofcactus' ),
			'gradient' => 'linear-gradient(160deg, #e9ff43 0%, #a8b830 100%)',
		),
		array(
			'title'    => __( 'Créer', 'sliceofcactus' ),
			'text'     => __( 'Dessiner, colorier, prolonger le geste.', 'sliceofcactus' ),
			'gradient' => 'linear-gradient(160deg, #e0592f 0%, #a14022 100%)',
		),
		array(
			'title'    => __( 'Partager', 'sliceofcactus' ),
			'text'     => __( 'Donner à voir, sans tout dire.', 'sliceofcactus' ),
			'gradient' => 'linear-gradient(160deg, #27513e 0%, #1c3a2d 100%)',
		),
	);
	?>
	<main id="main-content" class="soc-about rubrique-page">

		<div class="mag-runhead">
			<span><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Retour à l\'accueil', 'sliceofcactus' ); ?></a></span>
			<span><?php esc_html_e( 'Page', 'sliceofcactus' ); ?></span>
			<span><b><?php the_title(); ?></b></span>
		</div>

		<header class="mag-masthead">
			<h1 class="mag-masthead__title">
				<?php the_title(); ?>
				<em><?php esc_html_e( 'sélectionner plutôt qu\'accumuler', 'sliceofcactus' ); ?></em>
			</h1>
			<?php if ( '' !== $lead ) : ?>
				<?php
				$lead_first = mb_substr( $lead, 0, 1 );
				$lead_rest  = mb_substr( $lead, 1 );
				?>
				<div class="mag-masthead__lead" data-reveal>
					<p>
						<span class="drop"><?php echo esc_html( $lead_first ); ?></span>
						<?php echo esc_html( $lead_rest ); ?>
					</p>
				</div>
			<?php endif; ?>
		</header>

		<div class="about-body" data-reveal>
			<?php the_content(); ?>
		</div>

		<section class="about-sequence" aria-labelledby="about-sequence-heading">
			<div class="about-sequence__in">
				<div class="about-sequence__sticky">
					<span class="about-kicker" id="about-sequence-heading">
						<?php esc_html_e( 'La démarche', 'sliceofcactus' ); ?>
					</span>
					<div class="about-sequence__visual" data-visual style="background:<?php echo esc_attr( $steps[0]['gradient'] ); ?>">
						<div class="about-sequence__big" data-big>01</div>
					</div>
					<div class="about-sequence__heading">
						<?php esc_html_e( 'Quatre gestes, dans cet ordre.', 'sliceofcactus' ); ?>
					</div>
				</div>

				<div class="about-sequence__list">
					<?php foreach ( $steps as $i => $step ) : ?>
						<?php $no = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ); ?>
						<div class="about-sequence-item" data-val="<?php echo esc_attr( $no ); ?>" data-bg="<?php echo esc_attr( $step['gradient'] ); ?>">
							<div class="about-sequence-item__no"><?php echo esc_html( $no ); ?></div>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="universe about-forms" id="trois-formes">
			<div class="section-head">
				<h2 class="section-head__title" data-reveal><?php esc_html_e( 'Trois formes', 'sliceofcactus' ); ?></h2>
				<p class="section-head__sub" data-reveal>
					<?php esc_html_e( 'Une même main, trois manières de regarder. Survolez pour entrer.', 'sliceofcactus' ); ?>
				</p>
			</div>
			<div class="universe__panels">
				<?php foreach ( $forms as $form ) : ?>
					<?php if ( ! $form['url'] ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<a
						class="upanel"
						href="<?php echo esc_url( $form['url'] ); ?>"
						style="--c: <?php echo esc_attr( $form['color'] ); ?>;<?php echo $form['image'] ? ' --img: url(' . esc_url( wp_get_attachment_image_url( $form['image'], 'large' ) ) . ')' : ''; ?>"
					>
						<span class="upanel__bg" aria-hidden="true"></span>
						<span class="upanel__num"><?php echo esc_html( $form['num'] ); ?></span>
						<span class="upanel__k"><?php echo esc_html( $form['kicker'] ); ?></span>
						<h3 class="upanel__t"><?php echo esc_html( $form['title'] ); ?></h3>
						<p class="upanel__d"><?php echo esc_html( $form['desc'] ); ?></p>
						<span class="upanel__cta"><?php echo esc_html( $form['cta'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>


		<?php if ( ! empty( $resonance_terms ) ) : ?>
			<section class="about-resonances-section" aria-labelledby="about-resonances-heading" data-reveal>
				<h2 id="about-resonances-heading" class="about-kicker">
					<?php esc_html_e( 'Les résonances', 'sliceofcactus' ); ?>
				</h2>
				<p class="about-resonances-lead">
					<?php esc_html_e( 'Les résonances ne sont pas des catégories : elles relient une photographie, un récit ou un dessin par ce qu\'ils évoquent, au-delà du sujet qui les rassemble.', 'sliceofcactus' ); ?>
				</p>
				<div class="about-resonances">
					<?php
					$resonance_kind_labels = array(
						'recit'    => __( 'Récit', 'sliceofcactus' ),
						'photo'    => __( 'Photo', 'sliceofcactus' ),
						'creation' => __( 'Atelier', 'sliceofcactus' ),
					);
					?>
					<?php foreach ( $resonance_terms as $term ) : ?>
						<?php
						$intro = function_exists( 'get_field' ) ? get_field( 'soc_resonance_intro', 'resonance_' . $term->term_id ) : '';

						// Every CPT tagged with this résonance, pooled together and
						// sorted by date — not one-per-type, just whatever's
						// actually most recent, mixed.
						$pool = array();

						foreach ( $resonance_kind_labels as $post_type => $kind_label ) {
							foreach ( soc_get_resonance_items( $term, $post_type ) as $item ) {
								$pool[] = array(
									'post' => $item,
									'kind' => $kind_label,
								);
							}
						}

						usort(
							$pool,
							static function ( array $a, array $b ): int {
								return strtotime( $b['post']->post_date ) <=> strtotime( $a['post']->post_date );
							}
						);

						$previews = array_slice( $pool, 0, 3 );
						?>
						<div class="rmosaic-card<?php echo count( $previews ) >= 2 ? ' rmosaic-card--tall' : ''; ?>">
							<h3 class="rmosaic-card__name"><?php echo esc_html( $term->name ); ?></h3>
							<?php if ( is_string( $intro ) && '' !== $intro ) : ?>
								<p class="rmosaic-card__intro"><?php echo esc_html( $intro ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $previews ) ) : ?>
								<ul class="rmosaic-card__list">
									<?php foreach ( $previews as $preview ) : ?>
										<li>
											<a href="<?php echo esc_url( get_permalink( $preview['post'] ) ); ?>">
												<span class="rmosaic-card__kind"><?php echo esc_html( $preview['kind'] ); ?></span>
												<span class="rmosaic-card__title"><?php echo esc_html( get_the_title( $preview['post'] ) ); ?></span>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<a class="rmosaic-card__cta" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
								<?php esc_html_e( 'Tout découvrir →', 'sliceofcactus' ); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<section class="about-invite" data-reveal>
			<p class="about-invite__line"><?php esc_html_e( 'La suite se lit mieux qu\'elle ne s\'explique.', 'sliceofcactus' ); ?></p>
			<div class="about-invite__links">
				<a class="about-invite__link is-solid" href="<?php echo esc_url( get_post_type_archive_link( 'recit' ) ); ?>">
					<?php esc_html_e( 'Commencer un récit', 'sliceofcactus' ); ?>
				</a>
				<a class="about-invite__link" href="<?php echo esc_url( get_post_type_archive_link( 'photo' ) ); ?>">
					<?php esc_html_e( 'Explorer une série', 'sliceofcactus' ); ?>
				</a>
				<a class="about-invite__link" href="<?php echo esc_url( get_post_type_archive_link( 'creation' ) ); ?>">
					<?php esc_html_e( 'Découvrir l\'atelier', 'sliceofcactus' ); ?>
				</a>
			</div>
		</section>

	</main>
	<?php
endwhile;

get_footer();
