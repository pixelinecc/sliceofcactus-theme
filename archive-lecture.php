<?php
/**
 * Lectures archive: a grid of book cards.
 *
 * Reads the main loop. Filters are a plain GET form: genre is the native
 * query var of the lecture_genre taxonomy; statut and tri are applied in
 * soc_filter_lecture_archive_query() (inc/queries.php).
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$total       = $wp_query->found_posts;
$archive_url = get_post_type_archive_link( 'lecture' );
$filters     = soc_get_lecture_filters();
$genres_list = get_terms( array( 'taxonomy' => 'lecture_genre' ) );
$genres_list = is_array( $genres_list ) ? $genres_list : array();
$is_filtered = '' !== $filters['genre'] || '' !== $filters['statut'] || 'recent' !== $filters['tri'];
?>
<main id="main-content" class="soc-recit-archive rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Lectures', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Carnet de lecture', 'sliceofcactus' ); ?></span>
		<span>
			<?php
			printf(
				/* translators: %s: number of lectures. */
				esc_html( _n( '%s lecture', '%s lectures', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<div class="journal-name">
		<h1><?php esc_html_e( 'Lectures', 'sliceofcactus' ); ?></h1>
		<p class="sub"><?php esc_html_e( 'Livres lus, avis et notes', 'sliceofcactus' ); ?></p>
	</div>

	<?php /* Plain GET form: genre is the taxonomy's native query var; statut/tri are read in soc_filter_lecture_archive_query(). */ ?>
	<form class="lecture-filters" method="get" action="<?php echo esc_url( $archive_url ); ?>">
		<?php if ( ! empty( $genres_list ) ) : ?>
			<label>
				<span><?php esc_html_e( 'Genre', 'sliceofcactus' ); ?></span>
				<select name="lecture_genre">
					<option value=""><?php esc_html_e( 'Tous', 'sliceofcactus' ); ?></option>
					<?php foreach ( $genres_list as $genre ) : ?>
						<option value="<?php echo esc_attr( $genre->slug ); ?>" <?php selected( $filters['genre'], $genre->slug ); ?>><?php echo esc_html( $genre->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		<?php endif; ?>

		<label>
			<span><?php esc_html_e( 'Statut', 'sliceofcactus' ); ?></span>
			<select name="statut">
				<option value=""><?php esc_html_e( 'Tous', 'sliceofcactus' ); ?></option>
				<?php foreach ( soc_get_lecture_status_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filters['statut'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>
			<span><?php esc_html_e( 'Trier par', 'sliceofcactus' ); ?></span>
			<select name="tri">
				<?php foreach ( soc_get_lecture_sort_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filters['tri'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<button type="submit"><?php esc_html_e( 'Filtrer', 'sliceofcactus' ); ?></button>
		<?php if ( $is_filtered ) : ?>
			<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Réinitialiser', 'sliceofcactus' ); ?></a>
		<?php endif; ?>
	</form>

	<?php if ( have_posts() ) : ?>
		<div class="lecture-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				$author        = (string) get_field( 'soc_lecture_auteur' );
				$serie         = (string) get_field( 'soc_lecture_serie' );
				$note          = soc_get_lecture_note();
				$is_unfinished = 'termine' !== (string) get_field( 'soc_lecture_statut' );
				$genres        = get_the_terms( get_the_ID(), 'lecture_genre' );
				$genre_name    = is_array( $genres ) && ! empty( $genres ) ? $genres[0]->name : '';
				$book_title    = soc_get_lecture_book_title();
				$serie         = 0 === strcasecmp( $serie, $book_title ) ? '' : $serie;
				$kicker        = implode( ' · ', array_filter( array( $genre_name, $serie ) ) );
				?>
				<a class="lecture-card" href="<?php the_permalink(); ?>">
					<span class="lecture-card__cover">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php
							the_post_thumbnail(
								'medium',
								array(
									'alt'     => '',
									'loading' => 'lazy',
								)
							);
							?>
						<?php endif; ?>
						<?php if ( null !== $note ) : ?>
							<span class="lecture-note">
								<b><?php echo esc_html( soc_format_lecture_note( $note ) ); ?></b><span>/10</span>
							</span>
						<?php endif; ?>
					</span>
					<?php if ( '' !== $kicker || $is_unfinished ) : ?>
						<span class="lecture-card__kicker">
							<?php echo esc_html( $kicker ); ?>
							<?php if ( $is_unfinished ) : ?>
								<em><?php echo esc_html( soc_get_lecture_choice_label( 'soc_lecture_statut' ) ); ?></em>
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<h2 class="lecture-card__title"><?php echo esc_html( $book_title ); ?></h2>
					<?php if ( '' !== $author ) : ?>
						<span class="lecture-card__author"><?php echo esc_html( $author ); ?></span>
					<?php endif; ?>
				</a>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<p class="lecture-empty"><?php esc_html_e( 'Aucune lecture ne correspond à ces critères.', 'sliceofcactus' ); ?></p>
	<?php endif; ?>

	<?php
	$pagination = paginate_links(
		array(
			'prev_text' => __( '← Précédent', 'sliceofcactus' ),
			'next_text' => __( 'Suivant →', 'sliceofcactus' ),
			'type'      => 'list',
		)
	);
	?>
	<?php if ( $pagination ) : ?>
		<nav class="journal-pagination" aria-label="<?php esc_attr_e( 'Pagination des lectures', 'sliceofcactus' ); ?>">
			<?php echo $pagination; ?>
		</nav>
	<?php endif; ?>

</main>
<?php
get_footer();
