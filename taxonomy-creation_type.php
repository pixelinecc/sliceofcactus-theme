<?php
/**
 * Creation_type taxonomy archive controller: /dessin/ and /coloriage/.
 *
 * One template serves both terms, matching sliceofcactus-astro's
 * dessin/index.astro and coloriage/index.astro: the visual distinction is a
 * body class and CSS accent, plus a few conditional blocks, not a separate
 * template file. The creation_type taxonomy (acf-json/taxonomy_soc_creation_type.json)
 * is public with an empty rewrite slug, so WordPress resolves /dessin/ and
 * /coloriage/ to this file natively — no custom rewrite rule needed.
 *
 * @package SliceOfCactus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main-content" class="soc-creation-archive-main">
	<?php get_template_part( 'template-parts/taxonomy/creation-type', 'book-grid' ); ?>
</main>
<?php
get_footer();
