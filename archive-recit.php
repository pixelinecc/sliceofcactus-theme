<?php
/**
 * Récits archive, migrated from sliceofcactus-astro/src/pages/recits/index.astro.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, (int) get_query_var( 'paged' ) );
$total = $wp_query->found_posts;
?>
<main id="main-content" class="soc-recit-archive rubrique-page">

	<div class="mag-runhead">
		<span><?php esc_html_e( 'Slice of Cactus — Récits', 'sliceofcactus' ); ?></span>
		<span><?php esc_html_e( 'Carnets d\'écriture', 'sliceofcactus' ); ?></span>
		<span>
			<?php
			printf(
				/* translators: %s: number of récits. */
				esc_html( _n( '%s récit', '%s récits', $total, 'sliceofcactus' ) ),
				esc_html( number_format_i18n( $total ) )
			);
			?>
		</span>
	</div>

	<div class="journal-name">
		<h1><?php esc_html_e( 'Récits', 'sliceofcactus' ); ?></h1>
		<p class="sub"><?php esc_html_e( 'Carnets d\'écriture, en marge des images', 'sliceofcactus' ); ?></p>
		<div class="journal-folio">
			<span><?php esc_html_e( 'Slice of Cactus', 'sliceofcactus' ); ?></span>
			<span><?php esc_html_e( 'Édition N°1', 'sliceofcactus' ); ?></span>
			<span>
				<?php
				printf(
					/* translators: %s: number of récits. */
					esc_html( _n( '%s récit', '%s récits', $total, 'sliceofcactus' ) ),
					esc_html( number_format_i18n( $total ) )
				);
				?>
			</span>
		</div>
	</div>

	<?php if ( have_posts() ) : ?>
		<?php
		$post_index = 0;
		$in_cols    = false;

		while ( have_posts() ) :
			the_post();
			$is_lead = ( 1 === $paged && 0 === $post_index );

			if ( $is_lead ) :
				?>
				<a class="journal-lead" href="<?php the_permalink(); ?>">
					<span class="kicker">
						<?php
						printf(
							/* translators: %s: date. */
							esc_html__( 'La une · %s', 'sliceofcactus' ),
							esc_html( soc_get_recit_date_label( get_the_ID() ) )
						);
						?>
					</span>
					<h2 class="journal-lead__title"><?php the_title(); ?></h2>
					<p class="journal-lead__ex"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<span class="journal-lead__more"><?php esc_html_e( 'Lire le récit →', 'sliceofcactus' ); ?></span>
				</a>
				<?php
			else :
				if ( ! $in_cols ) {
					echo '<div class="journal-cols">';
					$in_cols = true;
				}
				?>
				<a class="entry" href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php
						the_post_thumbnail(
							'thumbnail',
							array(
								'class'   => 'entry__thumb',
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
					<?php endif; ?>
					<span class="entry__k"><?php echo esc_html( soc_get_recit_date_label( get_the_ID() ) ); ?></span>
					<h3 class="entry__t"><?php the_title(); ?></h3>
					<p class="entry__ex"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</a>
				<?php
			endif;

			++$post_index;
		endwhile;

		if ( $in_cols ) {
			echo '</div>';
		}
		?>
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
		<nav class="journal-pagination" aria-label="<?php esc_attr_e( 'Pagination des récits', 'sliceofcactus' ); ?>">
			<?php echo $pagination; ?>
		</nav>
	<?php endif; ?>

</main>
<?php
get_footer();