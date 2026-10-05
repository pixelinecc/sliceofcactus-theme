<?php
/**
 * Lecture article: the récit masthead and reading column, preceded by the
 * book's fiche (cover, author, note, genre, dates…).
 *
 * Shares the .article* classes of recit-article.php (single-recit.css); only
 * the fiche (.lecture-fiche, single-lecture.css) and the shared note badge
 * (.lecture-note, archive-lecture.css) are specific to lectures.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id     = get_the_ID();
$archive_url = get_post_type_archive_link( 'lecture' );
$author      = (string) get_field( 'soc_lecture_auteur', $post_id );
$format      = soc_get_lecture_choice_label( 'soc_lecture_format', $post_id );
$status      = soc_get_lecture_choice_label( 'soc_lecture_statut', $post_id );
$read_label  = soc_get_lecture_date_label( $post_id );
$serie       = (string) get_field( 'soc_lecture_serie', $post_id );
$tome        = (int) get_field( 'soc_lecture_tome', $post_id );
$pages       = (int) get_field( 'soc_lecture_pages', $post_id );
$note        = soc_get_lecture_note( $post_id );
$has_own_note = is_numeric( get_field( 'soc_lecture_note', $post_id ) );
$tomes       = soc_get_lecture_tomes( $post_id );
$summary     = trim( (string) get_field( 'soc_lecture_resume', $post_id ) );
$ressenti    = soc_get_lecture_choice_label( 'soc_lecture_ressenti', $post_id );
$traits_all  = soc_get_lecture_choices( 'soc_lecture_traits' );
$traits      = array_intersect_key( $traits_all, array_flip( array_filter( (array) get_field( 'soc_lecture_traits', $post_id ), 'is_string' ) ) );
$genres      = get_the_terms( $post_id, 'lecture_genre' );
$genres      = is_array( $genres ) ? $genres : array();

$genre_links = array_map(
	static fn( WP_Term $term ): string => sprintf(
		'<a href="%s">%s</a>',
		esc_url( add_query_arg( 'lecture_genre', $term->slug, $archive_url ) ),
		esc_html( $term->name )
	),
	$genres
);

if ( '' !== $serie && $tome > 0 ) {
	/* translators: 1: series name, 2: volume number. */
	$serie_label = sprintf( __( '%1$s, tome %2$d', 'sliceofcactus' ), $serie, $tome );
} else {
	$serie_label = $serie;
}

// Label => already-escaped HTML. Empty values are skipped in the loop below.
$traits_html = implode(
	' ',
	array_map(
		static fn( string $label ): string => '<span class="lecture-tag">' . esc_html( $label ) . '</span>',
		$traits
	)
);

$meta = array(
	__( 'Type', 'sliceofcactus' )   => esc_html( $format ),
	__( 'Genre', 'sliceofcactus' )  => implode( ', ', $genre_links ),
	__( 'Série', 'sliceofcactus' )  => esc_html( $serie_label ),
	__( 'Lu en', 'sliceofcactus' )  => esc_html( $read_label ),
	__( 'Pages', 'sliceofcactus' )  => $pages > 0 ? esc_html( number_format_i18n( $pages ) ) : '',
	__( 'Statut', 'sliceofcactus' ) => esc_html( $status ),
	__( 'Ressenti', 'sliceofcactus' ) => esc_html( $ressenti ),
	__( 'Aussi', 'sliceofcactus' )  => $traits_html,
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article' ); ?>>
	<div class="mag-runhead">
		<span>
			<?php if ( $archive_url ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( '← Toutes les lectures', 'sliceofcactus' ); ?></a>
			<?php endif; ?>
		</span>
		<span><?php esc_html_e( 'Lecture', 'sliceofcactus' ); ?></span>
		<span>
			<?php if ( '' !== $read_label ) : ?>
				<b><?php echo esc_html( $read_label ); ?></b>
			<?php endif; ?>
		</span>
	</div>

	<div class="article__masthead">
		<div class="article__masthead-inner">
			<h1 class="article__masthead-title"><?php the_title(); ?></h1>
			<div class="journal-folio">
				<span><?php esc_html_e( 'Slice of Cactus', 'sliceofcactus' ); ?></span>
				<?php if ( '' !== $author ) : ?>
					<span><?php echo esc_html( $author ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $format ) : ?>
					<span><?php echo esc_html( $format ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<section class="lecture-fiche" aria-label="<?php esc_attr_e( 'Fiche du livre', 'sliceofcactus' ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="lecture-fiche__cover">
				<?php the_post_thumbnail( 'medium', array( 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="lecture-fiche__info">
			<h2 class="lecture-fiche__title"><?php echo esc_html( soc_get_lecture_book_title( $post_id ) ); ?></h2>
			<?php if ( '' !== $author ) : ?>
				<p class="lecture-fiche__author">
					<?php
					/* translators: %s: book author. */
					printf( esc_html__( 'de %s', 'sliceofcactus' ), '<b>' . esc_html( $author ) . '</b>' );
					?>
				</p>
			<?php endif; ?>

			<dl class="lecture-fiche__meta">
				<?php foreach ( $meta as $label => $value ) : ?>
					<?php if ( '' === $value ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<div>
						<dt><?php echo esc_html( $label ); ?></dt>
						<dd><?php echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>

		<?php if ( null !== $note ) : ?>
			<div class="lecture-fiche__note">
				<span class="lecture-fiche__note-label">
					<?php echo esc_html( $has_own_note ? __( 'Note globale', 'sliceofcactus' ) : __( 'Note moyenne', 'sliceofcactus' ) ); ?>
				</span>
				<span class="lecture-note">
					<b><?php echo esc_html( soc_format_lecture_note( $note ) ); ?></b><span>/10</span>
				</span>

				<?php if ( ! empty( $tomes ) ) : ?>
					<ul class="lecture-fiche__tomes">
						<?php foreach ( $tomes as $tome_row ) : ?>
							<li>
								<span><?php echo esc_html( $tome_row['titre'] ); ?></span>
								<?php if ( null !== $tome_row['note'] ) : ?>
									<b><?php echo esc_html( soc_format_lecture_note( $tome_row['note'] ) ); ?>/10</b>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( '' !== $summary ) : ?>
		<section class="lecture-summary" aria-label="<?php esc_attr_e( 'Résumé de l’éditeur', 'sliceofcactus' ); ?>">
			<h2 class="lecture-summary__label"><?php esc_html_e( 'Résumé de l’éditeur', 'sliceofcactus' ); ?></h2>
			<?php echo wp_kses_post( wpautop( esc_html( $summary ) ) ); ?>
		</section>
	<?php endif; ?>

	<div class="lecture-notice">
		<?php esc_html_e( 'Ce qui suit peut contenir des informations sur le livre qui risquent de gâcher l’histoire si vous ne l’avez pas lu.', 'sliceofcactus' ); ?>
	</div>

	<div class="article__body">
		<?php the_content(); ?>
	</div>
</article>

<?php if ( $archive_url ) : ?>
	<a class="back-link back-link--center" href="<?php echo esc_url( $archive_url ); ?>">
		<?php esc_html_e( '← Retour aux lectures', 'sliceofcactus' ); ?>
	</a>
<?php endif; ?>
