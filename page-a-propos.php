<?php
/**
 * Template Name: À propos
 *
 * Rewritten 2026-08-06 into a short, personal page instead of the site's
 * "documentation" page it had drifted into — see the brief's own beats:
 * header (WP title + hardcoded subtitle + the_content() intro), portrait,
 * "Regarder, garder, partager", "La palette" (every rubrique accent,
 * restored from the previous version of this page — see below), "Des
 * chemins de traverse" (2026-08-07 — Photo/Atelier/Récits as big scattered
 * words filled with their own palette color, Résonances crossing them;
 * replaced the earlier plain text + CTA link), "Dire bonjour"
 * (.about-contact, 2026-08-07 — replaced the former plain-text
 * .about-conclusion with the same email/Instagram links as the footer).
 * ("Trois façons d'entrer" — Photo/Atelier/Récits as .btn buttons — was
 * removed after the fact; assets/styles/components/buttons.css is no
 * longer loaded on this page, see inc/assets.php.)
 *
 * Structured like the other bespoke rubrique pages (archive-creation.php,
 * page-projet-52.php): .mag-runhead and .mag-masthead straight from
 * assets/styles/components/magazine-hub.css, sitting directly on the body's
 * own accent-gradient background (assets/styles/base/elements.css) — no
 * paper flip, no per-section background override, only its own --accent /
 * --accent-deep pair set on the body in page-a-propos.css.
 *
 * "La palette" reads --accent-* CSS custom properties (tokens.css is the
 * only place their hex actually lives), so its hex label and on-dark/
 * on-light contrast class can only be resolved client-side, in
 * assets/scripts/page-a-propos.js.
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

	// "Des chemins de traverse" → the general "Résonances" page
	// (page-resonances.php), wherever it's been placed in wp-admin.
	$resonances_url = soc_get_page_url_by_template( 'page-resonances.php' );

	// "La palette": every rubrique accent (--accent-*, minus their
	// --accent-deep-* counterparts) from assets/styles/settings/tokens.css,
	// the theme's only source for these colors. Hex text is filled in by JS
	// (assets/scripts/page-a-propos.js) reading each swatch's computed
	// background — so this list only has to name the CSS var, never its
	// value. Each swatch also links to its rubrique's own page/archive.
	$palette = array(
		array(
			'var'   => '--accent-signature',
			'label' => __( 'Signature', 'sliceofcactus' ),
			'url'   => home_url( '/' ),
		),
		array(
			'var'   => '--accent-photo',
			'label' => __( 'Photo', 'sliceofcactus' ),
			'url'   => get_post_type_archive_link( 'photo' ),
		),
		array(
			'var'   => '--accent-carte',
			'label' => __( 'Carte', 'sliceofcactus' ),
			'url'   => soc_get_page_url_by_template( 'page-voyage-carte.php' ),
		),
		array(
			'var'   => '--accent-color-your-life',
			'label' => __( 'Color Your Life', 'sliceofcactus' ),
			'url'   => soc_get_page_url_by_template( 'page-color-your-life.php' ),
		),
		array(
			'var'   => '--accent-p52',
			'label' => __( 'Projet 52', 'sliceofcactus' ),
			'url'   => soc_get_page_url_by_template( 'page-projet-52.php' ),
		),
		array(
			'var'   => '--accent-atelier',
			'label' => __( 'Atelier', 'sliceofcactus' ),
			'url'   => get_post_type_archive_link( 'creation' ),
		),
		array(
			'var'   => '--accent-atelier-dessin',
			'label' => __( 'Atelier · dessin', 'sliceofcactus' ),
			'url'   => soc_get_creation_rubrique_archive_link( 'dessin' ),
		),
		array(
			'var'   => '--accent-atelier-coloriage',
			'label' => __( 'Atelier · coloriage', 'sliceofcactus' ),
			'url'   => soc_get_creation_rubrique_archive_link( 'coloriage' ),
		),
		array(
			'var'   => '--accent-apropos',
			'label' => __( 'À propos', 'sliceofcactus' ),
			'url'   => get_permalink( $post_id ),
		),
		array(
			'var'   => '--accent-recit',
			'label' => __( 'Récits', 'sliceofcactus' ),
			'url'   => get_post_type_archive_link( 'recit' ),
		),
		array(
			'var'   => '--accent-resonance',
			'label' => __( 'Résonances', 'sliceofcactus' ),
			'url'   => $resonances_url,
		),
	);

	// "Des chemins de traverse": Photo/Atelier/Récits' own archive URLs,
	// already resolved above for their palette swatches — reused here so
	// the crossing words below link to the same place.
	$palette_urls = wp_list_pluck( $palette, 'url', 'var' );
	$photo_url    = $palette_urls['--accent-photo'] ?? '';
	$atelier_url  = $palette_urls['--accent-atelier'] ?? '';
	$recit_url    = $palette_urls['--accent-recit'] ?? '';
	?>
	<main id="main-content" class="soc-about rubrique-page">

		<div class="mag-runhead">
			<span><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Retour à l\'accueil', 'sliceofcactus' ); ?></a></span>
			<span><?php esc_html_e( 'Regarder, créeer, partager', 'sliceofcactus' ); ?></span>
			<span><?php esc_html_e( 'A propos', 'sliceofcactus' ); ?></span>
		</div>

		<header class="mag-masthead">
			<h1 class="mag-masthead__title">
				<?php the_title(); ?>
				<em><?php esc_html_e( 'Derrière Slice of Cactus', 'sliceofcactus' ); ?></em>
			</h1>
			<div class="mag-masthead__lead" data-reveal>
				<?php the_excerpt(); ?>
			</div>
		</header>

		<div class="about-intro">
			<div class="about-body" data-reveal>
				<?php the_content(); ?>
			</div>

			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<figure class="about-portrait" data-reveal>
					<div class="about-portrait__frame">
						<?php
						echo get_the_post_thumbnail(
							$post_id,
							'large',
							array(
								'class' => 'about-portrait__img',
								'alt'   => __( 'Quatre autoportraits spontanés de Céline, créatrice de Slice of Cactus', 'sliceofcactus' ),
							)
						);
						?>
					</div>
					<figcaption class="about-portrait__cap">
						<?php esc_html_e( 'Quatre essais. Toujours pas de photo sérieuse.', 'sliceofcactus' ); ?>
					</figcaption>
				</figure>
			<?php endif; ?>
		</div>

		<section class="about-palette" aria-labelledby="about-palette-heading" data-reveal>
			<div class="about-palette__in">
				<div class="about-palette__intro">
					<h2 id="about-palette-heading" class="about-kicker">
						<?php esc_html_e( 'La palette', 'sliceofcactus' ); ?>
					</h2>
					<p class="about-palette__lead">
						<?php esc_html_e( 'Une couleur par rubrique, jamais interchangeable.', 'sliceofcactus' ); ?>
					</p>

					<div class="about-palette__legend">
						<p>
							<?php esc_html_e( 'Chaque rubrique a sa couleur, chaque couleur son usage.', 'sliceofcactus' ); ?>
						</p>
						<p>
							<?php
							esc_html_e(
								'Photo réunit les séries et carnets de voyage — Carte pour les repères géographiques, Color Your Life et Projet 52 pour les temps longs. Atelier rassemble le dessin et le coloriage. Récits porte les textes. Résonances relie tout le reste par ce qu\'il évoque.',
								'sliceofcactus'
							);
							?>
						</p>
					</div>
				</div>

				<div class="about-palette__content">
					<div class="about-palette__grid" data-palette>
						<?php foreach ( $palette as $swatch ) : ?>
							<?php
							$swatch_url = is_string( $swatch['url'] ) ? $swatch['url'] : '';
							$swatch_tag = '' !== $swatch_url ? 'a' : 'div';
							?>
							<<?php echo tag_escape( $swatch_tag ); ?>
								class="about-palette__swatch"
								style="background:var(<?php echo esc_attr( $swatch['var'] ); ?>)"
								data-fill
								<?php echo '' !== $swatch_url ? 'href="' . esc_url( $swatch_url ) . '"' : ''; ?>
							>
								<span class="about-palette__hex" data-hex></span>
								<span class="about-palette__label"><?php echo esc_html( $swatch['label'] ); ?></span>
							</<?php echo tag_escape( $swatch_tag ); ?>>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

		<section class="about-crossroads" aria-labelledby="about-crossroads-heading" data-reveal>
			<h2 id="about-crossroads-heading" class="about-kicker">
				<?php esc_html_e( 'Des chemins de traverse', 'sliceofcactus' ); ?>
			</h2>

			<div class="about-crossroads__words" data-palette>
				<div class="about-crossroads__row">
					<?php if ( '' !== $photo_url ) : ?>
						<a class="about-crossroads__word about-crossroads__word--photo" style="--word-color:var(--accent-photo)" data-fill href="<?php echo esc_url( $photo_url ); ?>">
							<?php esc_html_e( 'Photo', 'sliceofcactus' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $atelier_url ) : ?>
						<a class="about-crossroads__word about-crossroads__word--atelier" style="--word-color:var(--accent-atelier)" data-fill href="<?php echo esc_url( $atelier_url ); ?>">
							<?php esc_html_e( 'Atelier', 'sliceofcactus' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( '' !== $recit_url ) : ?>
						<a class="about-crossroads__word about-crossroads__word--recit" style="--word-color:var(--accent-recit)" data-fill href="<?php echo esc_url( $recit_url ); ?>">
							<?php esc_html_e( 'Récits', 'sliceofcactus' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $resonances_url ) : ?>
					<a class="about-crossroads__word about-crossroads__word--resonance" style="--word-color:var(--accent-resonance)" data-fill href="<?php echo esc_url( $resonances_url ); ?>">
						<?php esc_html_e( 'Résonances', 'sliceofcactus' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="about-crossroads__text">
				<p>
					<?php esc_html_e( 'Photo, Atelier et Récits indiquent la forme des contenus. Les Résonances révèlent ce qui les relie : une couleur, une émotion, un souvenir ou une manière de regarder le monde.', 'sliceofcactus' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Un même contenu peut suivre plusieurs chemins. Il suffit de choisir celui qui vous attire aujourd\'hui.', 'sliceofcactus' ); ?>
				</p>
			</div>

			<?php if ( '' !== $resonances_url ) : ?>
				<a class="about-crossroads__cta" href="<?php echo esc_url( $resonances_url ); ?>">
					<?php esc_html_e( 'Voir les résonances', 'sliceofcactus' ); ?>
				</a>
			<?php endif; ?>
		</section>

		<section class="about-contact" aria-labelledby="about-contact-heading" data-reveal>
			<div class="about-contact__in">
				<div class="about-contact__intro">
					<h2 id="about-contact-heading" class="about-kicker">
						<?php esc_html_e( 'Dire', 'sliceofcactus' ); ?>
						<em><?php esc_html_e( 'bonjour.', 'sliceofcactus' ); ?></em>
					</h2>
					<p class="about-contact__lead">
						<?php esc_html_e( 'Une photo qui vous a parlé, une question, une envie d\'échanger : passez le mot.', 'sliceofcactus' ); ?>
					</p>
				</div>

				<ul class="about-contact__list">
					<li class="about-contact__item">
						<span class="about-contact__label"><?php esc_html_e( 'Email', 'sliceofcactus' ); ?></span>
						<a class="about-contact__value" href="mailto:bonjour@sliceofcactus.fr">bonjour@sliceofcactus.fr</a>
					</li>
					<li class="about-contact__item">
						<span class="about-contact__label"><?php esc_html_e( 'Instagram · Photo', 'sliceofcactus' ); ?></span>
						<a class="about-contact__value" href="https://www.instagram.com/sliceofcactus/" target="_blank" rel="noopener noreferrer">@sliceofcactus</a>
					</li>
					<li class="about-contact__item">
						<span class="about-contact__label"><?php esc_html_e( 'Instagram · Dessin', 'sliceofcactus' ); ?></span>
						<a class="about-contact__value" href="https://www.instagram.com/traitducameleon/" target="_blank" rel="noopener noreferrer">@traitducameleon</a>
					</li>
				</ul>
			</div>
		</section>

	</main>
	<?php
endwhile;

get_footer();
