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

$recits = soc_get_recit_archive_items();
$total  = count( $recits );
$first  = $total > 0 ? $recits[0] : null;
$rest   = $total > 0 ? array_slice( $recits, 1 ) : array();
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

	<?php if ( $first ) : ?>
		<?php
		$first_meta = array_filter(
			array(
				soc_get_recit_date_label( $first->ID ),
				soc_get_recit_location_label( $first->ID ),
			),
			static fn( string $part ): bool => '' !== $part
		);
		?>
		<a class="journal-lead" href="<?php echo esc_url( get_permalink( $first ) ); ?>">
			<span class="kicker">
				<?php
				printf(
					/* translators: %s: date and place. */
					esc_html__( 'La une · %s', 'sliceofcactus' ),
					esc_html( implode( ' · ', $first_meta ) )
				);
				?>
			</span>
			<h2 class="journal-lead__title"><?php echo esc_html( get_the_title( $first ) ); ?></h2>
			<p class="journal-lead__ex"><?php echo esc_html( get_the_excerpt( $first ) ); ?></p>
			<span class="journal-lead__more"><?php esc_html_e( 'Lire le récit →', 'sliceofcactus' ); ?></span>
		</a>
	<?php endif; ?>

	<div class="journal-cols">
		<?php foreach ( $rest as $recit ) : ?>
			<?php
			$entry_meta = array_filter(
				array(
					soc_get_recit_date_label( $recit->ID ),
					soc_get_recit_location_label( $recit->ID ),
				),
				static fn( string $part ): bool => '' !== $part
			);
			?>
			<a class="entry" href="<?php echo esc_url( get_permalink( $recit ) ); ?>">
				<?php if ( has_post_thumbnail( $recit ) ) : ?>
					<?php
					echo get_the_post_thumbnail(
						$recit,
						'thumbnail',
						array(
							'class'   => 'entry__thumb',
							'alt'     => '',
							'loading' => 'lazy',
						)
					);
					?>
				<?php endif; ?>
				<span class="entry__k"><?php echo esc_html( implode( ' · ', $entry_meta ) ); ?></span>
				<h3 class="entry__t"><?php echo esc_html( get_the_title( $recit ) ); ?></h3>
				<p class="entry__ex"><?php echo esc_html( get_the_excerpt( $recit ) ); ?></p>
			</a>
		<?php endforeach; ?>
	</div>

</main>
<?php
get_footer();