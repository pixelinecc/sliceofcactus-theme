<?php
/**
 * Reusable editorial queries.
 *
 * Ce module est réservé aux requêtes concernant les récits liés,
 * les collections, les créations, les résonances et la poursuite
 * de l’exploration. Les requêtes seront ajoutées uniquement lorsque
 * leurs règles de sélection seront confirmées.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the photos displayed after a single photo.
 *
 * Random draw among photos sharing at least one narration with the current
 * series, matching the shuffled `others` pool of sliceofcactus-astro's
 * photo/[id].astro (no manual curation).
 *
 * @param int $post_id Optional current photo ID.
 * @param int $limit   Maximum number of results.
 * @return WP_Post[]
 */
function soc_get_photo_suggestions( int $post_id = 0, int $limit = 6 ): array {
	$post_id = $post_id ?: get_the_ID();
	$limit   = max( 0, min( 12, $limit ) );

	if ( ! $post_id || 0 === $limit || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$narration_ids = wp_list_pluck( soc_get_photo_narrations( $post_id ), 'term_id' );

	if ( empty( $narration_ids ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'orderby'             => 'rand',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'term_id',
					'terms'    => $narration_ids,
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Gets the photo series shown on the Photo archive.
 *
 * Mirrors the RUBRIQUES filter of sliceofcactus-astro/src/pages/photo/index.astro:
 * every narration except the two standalone collections (Projet 52, Color Your Life),
 * limited to series with a cover image, most recent publish date first.
 *
 * @return WP_Post[]
 */
function soc_get_photo_archive_series(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'slug',
					'terms'    => array( 'projet-52', 'color-your-life' ),
					'operator' => 'NOT IN',
				),
			),
		)
	);

	return array_values(
		array_filter(
			$query->posts,
			static fn( WP_Post $photo ): bool => soc_get_photo_cover_id( $photo->ID ) > 0
		)
	);
}

/**
 * Gets the creations displayed after a single creation.
 *
 * Mirrors the `others` list of sliceofcactus-astro's dessin/[id].astro and
 * coloriage/[id].astro: every other creation sharing the same rubrique, with
 * no limit or shuffling (no manual curation, matching Astro).
 *
 * @param int $post_id Optional current creation ID.
 * @return WP_Post[]
 */
function soc_get_creation_suggestions( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return array();
	}

	$rubrique = soc_get_creation_rubrique( $post_id );

	if ( ! $rubrique ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'post__not_in'        => array( $post_id ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'creation_type',
					'field'    => 'term_id',
					'terms'    => array( $rubrique->term_id ),
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Gets the récits that reference a creation via soc_recit_creations.
 *
 * Reverse lookup: no field lives on the creation itself, avoiding a second
 * manual relationship to keep in sync with soc_recit_creations.
 *
 * @param int $post_id Optional current creation ID.
 * @return WP_Post[]
 */
function soc_get_creation_related_recits( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'creation' !== get_post_type( $post_id ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'recit',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'soc_recit_creations',
					'value'   => $post_id,
					'compare' => '=',
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Gets the récits that reference a photo series via soc_recit_photos.
 *
 * Reverse lookup: no field lives on the photo itself, avoiding a second
 * manual relationship to keep in sync with soc_recit_photos.
 *
 * @param int $post_id Optional current photo ID.
 * @return WP_Post[]
 */
function soc_get_photo_related_recits( int $post_id = 0 ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || 'photo' !== get_post_type( $post_id ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'recit',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'soc_recit_photos',
					'value'   => $post_id,
					'compare' => '=',
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Gets the creations shown on the Dessin/Coloriage archive.
 *
 * Mirrors sliceofcactus-astro's dessin/index.astro and coloriage/index.astro:
 * every creation of the given rubrique, most recent first. Coloriages are
 * additionally required to have a cover image, matching Astro's
 * `s.rubrique === 'coloriage' && s.couverture` filter — dessins have no such
 * requirement.
 *
 * @param string $rubrique_slug Rubrique (creation_type) term slug: 'dessin' or 'coloriage'.
 * @return WP_Post[]
 */
function soc_get_creation_archive_items( string $rubrique_slug ): array {
	$term = get_term_by( 'slug', $rubrique_slug, 'creation_type' );

	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'creation_type',
					'field'    => 'term_id',
					'terms'    => array( $term->term_id ),
				),
			),
		)
	);

	$items = $query->posts;

	if ( 'coloriage' === $rubrique_slug ) {
		$items = array_values(
			array_filter(
				$items,
				static fn( WP_Post $item ): bool => soc_get_creation_cover_id( $item->ID ) > 0
			)
		);
	}

	return $items;
}

/**
 * Gets the creations shown on the combined Créations archive.
 *
 * No Astro equivalent (Astro only has separate dessin/coloriage index
 * pages, already covered by soc_get_creation_archive_items() /
 * taxonomy-creation_type.php) — this powers the new unified /creations/
 * overview, every rubrique mixed, most recent first, cover image required.
 *
 * @return WP_Post[]
 */
function soc_get_creation_archive_series(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'creation',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	return array_values(
		array_filter(
			$query->posts,
			static fn( WP_Post $item ): bool => soc_get_creation_cover_id( $item->ID ) > 0
		)
	);
}

/**
 * Gets the récits shown on the Récits archive, most recent first.
 *
 * Mirrors sliceofcactus-astro's recits/index.astro: the JSON array is
 * already most-recent-first, its first item running as "la une".
 *
 * @return WP_Post[]
 */
function soc_get_recit_archive_items(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'recit',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	return $query->posts;
}

/**
 * Gets the Projet 52 grid: one gallery image per week, grouped by calendar
 * year.
 *
 * sliceofcactus-astro's projet-52.astro has no real data behind it (a
 * YEARS = {2024: 40, ...} config feeding a picsum.photos placeholder grid).
 * Here one "photo" post per year is tagged with the narration "projet-52";
 * its year comes from its native publish date, and its ordered
 * soc_photo_gallery images fill the weeks in sequence (image 1 = week 1,
 * etc.) — editors add one photo per week to that single gallery instead of
 * publishing a separate post every week.
 *
 * @return array<int, array{done: int, post_id: int, weeks: array<int, int|null>}> Keyed by year (most recent first), weeks holding attachment IDs.
 */
function soc_get_projet52_years(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'slug',
					'terms'    => array( 'projet-52' ),
				),
			),
		)
	);

	$years = array();

	foreach ( $query->posts as $post ) {
		$year        = (int) get_the_date( 'Y', $post );
		$attachments = array_slice( soc_get_photo_gallery_ids( $post->ID ), 0, 52 );

		if ( empty( $attachments ) ) {
			continue;
		}

		if ( ! isset( $years[ $year ] ) ) {
			$years[ $year ] = array(
				'done'    => 0,
				'post_id' => $post->ID,
				'weeks'   => array_fill( 1, 52, null ),
			);
		}

		foreach ( $attachments as $index => $attachment_id ) {
			$week = $index + 1;

			if ( null === $years[ $year ]['weeks'][ $week ] ) {
				$years[ $year ]['done']++;
			}

			$years[ $year ]['weeks'][ $week ] = $attachment_id;
		}
	}

	krsort( $years );

	return $years;
}

/**
 * Gets the photo series shown on the Color Your Life page, sorted by hue.
 *
 * Mirrors sliceofcactus-astro's color-your-life.astro: the same series as
 * the Photo archive (soc_get_photo_archive_series — same three rubriques,
 * cover image required), further limited to series with a dominant color,
 * ordered around the color wheel instead of by date.
 *
 * @return WP_Post[]
 */
function soc_get_color_your_life_series(): array {
	$series = array_values(
		array_filter(
			soc_get_photo_archive_series(),
			static fn( WP_Post $photo ): bool => '' !== soc_get_photo_color( $photo->ID )
		)
	);

	usort(
		$series,
		static fn( WP_Post $a, WP_Post $b ): int
			=> soc_hex_to_hue( soc_get_photo_color( $a->ID ) ) <=> soc_hex_to_hue( soc_get_photo_color( $b->ID ) )
	);

	return $series;
}

/**
 * Gets the destinations shown on the voyage map, grouped by location name.
 *
 * Mirrors sliceofcactus-astro's voyage-carte.astro: every "voyage" series
 * with a location, grouped by location name, series sorted most recent
 * first within each destination.
 *
 * @return array<int, array{name: string, country: string, lat: float, lon: float, series: WP_Post[]}>
 */
function soc_get_voyage_map_destinations(): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'slug',
					'terms'    => array( 'voyage' ),
				),
			),
		)
	);

	$destinations = array();

	foreach ( $query->posts as $photo ) {
		$location = soc_get_photo_location( $photo->ID );

		if ( empty( $location ) || ! is_numeric( $location['latitude'] ?? '' ) || ! is_numeric( $location['longitude'] ?? '' ) ) {
			continue;
		}

		$key = $location['name'];

		if ( ! isset( $destinations[ $key ] ) ) {
			$destinations[ $key ] = array(
				'name'    => $location['name'],
				'country' => $location['country'] ?? '',
				'lat'     => (float) $location['latitude'],
				'lon'     => (float) $location['longitude'],
				'series'  => array(),
			);
		}

		$destinations[ $key ]['series'][] = $photo;
	}

	return array_values( $destinations );
}

/**
 * Gets the most recent published photo series of a given narration.
 *
 * @param string $slug  Narration term slug.
 * @param int    $limit Maximum number of series.
 * @return WP_Post[]
 */
function soc_get_photos_by_narration( string $slug, int $limit ): array {
	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'narration',
					'field'    => 'slug',
					'terms'    => array( $slug ),
				),
			),
		)
	);

	return array_values(
		array_filter(
			$query->posts,
			static fn( WP_Post $photo ): bool => soc_get_photo_cover_id( $photo->ID ) > 0
		)
	);
}

/**
 * Gets the hero polaroids shown on the front page.
 *
 * sliceofcactus-astro's index.astro hardcodes 12 polaroids: a mix of
 * picsum.photos placeholders and a couple of real static assets, each with
 * a hand-placed position (left/top/rotation/z-index/width). There is no
 * real data behind any of them. Here every slot keeps Astro's exact
 * position but is filled with real, recently published content — Photo
 * (voyage), Création (dessin/coloriage), Récit, Projet 52 and Color Your
 * Life — and is simply dropped when that category has nothing published
 * yet, rather than falling back to a placeholder image.
 *
 * @return array<int, array{style: string, href: string, caption: string, image_id: int}>
 */
function soc_get_home_hero_polaroids(): array {
	$voyage    = soc_get_photos_by_narration( 'voyage', 3 );
	$coloriage = soc_get_creation_archive_items( 'coloriage' );
	$dessin    = soc_get_creation_archive_items( 'dessin' );
	$color     = soc_get_color_your_life_series();
	$p52       = soc_get_photos_by_narration( 'projet-52', 1 );
	$recits    = soc_get_recit_archive_items();

	$dessin_caption = static function ( WP_Post $creation ): string {
		$technique = soc_get_creation_technique_label( $creation->ID );

		return '' !== $technique
			? sprintf(
				/* translators: %s: dessin technique. */
				__( 'Dessin · %s', 'sliceofcactus' ),
				$technique
			)
			: __( 'Dessin', 'sliceofcactus' );
	};

	$coloriage_caption = static fn( WP_Post $creation ): string => sprintf(
		/* translators: %s: coloriage title. */
		__( 'Coloriage · %s', 'sliceofcactus' ),
		get_the_title( $creation )
	);

	$photo_caption = static fn( WP_Post $photo ): string => sprintf(
		/* translators: %s: photo series title. */
		__( 'Photo · %s', 'sliceofcactus' ),
		get_the_title( $photo )
	);

	$candidates = array(
		array(
			'style'   => 'left:3%;top:2%;--r:-12deg;--z:4;--w:1.02',
			'href'    => get_post_type_archive_link( 'photo' ),
			'caption' => __( 'Photo · Voyage', 'sliceofcactus' ),
			'post'    => $voyage[0] ?? null,
			'cover'   => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
		),
		array(
			'style'   => 'left:28%;top:0%;--r:7deg;--z:6;--w:.9',
			'href'    => home_url( '/voyage-carte/' ),
			'caption' => __( 'Explorer · la carte', 'sliceofcactus' ),
			'post'    => $voyage[0] ?? null,
			'cover'   => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
		),
		array(
			'style'      => 'left:50%;top:3%;--r:-6deg;--z:5;--w:1.05',
			'href'       => get_post_type_archive_link( 'photo' ),
			'post'       => $voyage[1] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
			'caption_cb' => $photo_caption,
		),
		array(
			'style'   => 'left:72%;top:1%;--r:10deg;--z:7;--w:.88',
			'href'    => home_url( '/color-your-life/' ),
			'caption' => __( 'Par couleur', 'sliceofcactus' ),
			'post'    => $color[0] ?? null,
			'cover'   => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
		),
		array(
			'style'      => 'left:1%;top:34%;--r:6deg;--z:7;--w:.95',
			'href'       => soc_get_creation_rubrique_archive_link( 'dessin' ),
			'post'       => $dessin[0] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_creation_cover_id( $p->ID ),
			'caption_cb' => $dessin_caption,
		),
		array(
			'style'   => 'left:25%;top:32%;--r:-5deg;--z:11;--w:.82',
			'href'    => get_post_type_archive_link( 'recit' ),
			'caption' => __( 'Récits', 'sliceofcactus' ),
			'post'    => $recits[0] ?? null,
			'cover'   => static fn( WP_Post $p ) => absint( get_post_thumbnail_id( $p->ID ) ),
		),
		array(
			'style'      => 'left:48%;top:34%;--r:-9deg;--z:8;--w:1.06',
			'post'       => $coloriage[0] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_creation_cover_id( $p->ID ),
			'caption_cb' => $coloriage_caption,
			'href_cb'    => static fn( WP_Post $p ) => get_permalink( $p ),
		),
		array(
			'style'      => 'left:71%;top:33%;--r:7deg;--z:6;--w:.9',
			'href'       => get_post_type_archive_link( 'photo' ),
			'post'       => $voyage[2] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
			'caption_cb' => $photo_caption,
		),
		array(
			'style'   => 'left:8%;top:64%;--r:5deg;--z:6;--w:.9',
			'href'    => soc_get_creation_rubrique_archive_link( 'coloriage' ),
			'caption' => __( 'Coloriages', 'sliceofcactus' ),
			'post'    => $coloriage[1] ?? null,
			'cover'   => static fn( WP_Post $p ) => soc_get_creation_cover_id( $p->ID ),
		),
		array(
			'style'      => 'left:31%;top:66%;--r:-7deg;--z:10;--w:1.0',
			'href'       => soc_get_creation_rubrique_archive_link( 'dessin' ),
			'post'       => $dessin[1] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_creation_cover_id( $p->ID ),
			'caption_cb' => $dessin_caption,
		),
		array(
			'style'   => 'left:54%;top:64%;--r:11deg;--z:8;--w:.88',
			'href'    => home_url( '/projet-52/' ),
			'caption' => __( 'Projet 52', 'sliceofcactus' ),
			'post'    => $p52[0] ?? null,
			'cover'   => static fn( WP_Post $p ) => soc_get_photo_cover_id( $p->ID ),
		),
		array(
			'style'      => 'left:76%;top:66%;--r:-5deg;--z:7;--w:.92',
			'post'       => $coloriage[2] ?? null,
			'cover'      => static fn( WP_Post $p ) => soc_get_creation_cover_id( $p->ID ),
			'caption_cb' => $coloriage_caption,
			'href_cb'    => static fn( WP_Post $p ) => get_permalink( $p ),
		),
	);

	$polaroids = array();

	foreach ( $candidates as $candidate ) {
		$post = $candidate['post'];

		if ( ! $post instanceof WP_Post ) {
			continue;
		}

		$image_id = $candidate['cover']( $post );

		if ( ! $image_id ) {
			continue;
		}

		$href = isset( $candidate['href_cb'] ) ? $candidate['href_cb']( $post ) : $candidate['href'];

		if ( ! $href ) {
			continue;
		}

		$polaroids[] = array(
			'style'    => $candidate['style'],
			'href'     => $href,
			'caption'  => isset( $candidate['caption_cb'] ) ? $candidate['caption_cb']( $post ) : $candidate['caption'],
			'image_id' => $image_id,
		);
	}

	return $polaroids;
}

/**
 * Gets the photo series featured in the front page's "36 poses" filmstrip.
 *
 * sliceofcactus-astro's index.astro fabricates a full 36-pose placeholder
 * filmstrip (picsum.photos, hardcoded to a fake "Lisbonne, marée basse"
 * series) unrelated to any real content. Here the filmstrip shows the most
 * recently published real voyage series instead, falling back to the most
 * recent photo series of any narration.
 *
 * @return WP_Post|null
 */
function soc_get_home_featured_photo(): ?WP_Post {
	$voyage = soc_get_photos_by_narration( 'voyage', 1 );

	if ( ! empty( $voyage ) ) {
		return $voyage[0];
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'photo',
			'post_status'         => 'publish',
			'posts_per_page'      => 1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	return ! empty( $query->posts ) ? $query->posts[0] : null;
}

/**
 * Gets the récits shown in the front page's "Récits à la une" section.
 *
 * @param int $limit Maximum number of récits.
 * @return WP_Post[]
 */
function soc_get_home_recits( int $limit = 3 ): array {
	return array_slice( soc_get_recit_archive_items(), 0, $limit );
}

/**
 * Gets the published posts of a given type carrying a résonance term.
 *
 * Résonances are the one functional evolution over sliceofcactus-astro
 * (see CLAUDE.md): they connect Photo, Création and Récit, so the
 * résonance archive queries all three through this single helper.
 *
 * @param WP_Term $term      Résonance term.
 * @param string  $post_type 'photo', 'creation' or 'recit'.
 * @return WP_Post[]
 */
function soc_get_resonance_items( WP_Term $term, string $post_type ): array {
	$query = new WP_Query(
		array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'resonance',
					'field'    => 'term_id',
					'terms'    => array( $term->term_id ),
				),
			),
		)
	);

	return $query->posts;
}
