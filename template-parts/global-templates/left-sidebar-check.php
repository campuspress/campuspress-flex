<?php
/**
 * Left sidebar check.
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$active_sidebars   = cpschool_get_active_sidebars();
$content_col_md_width = 12 - 4 * count( $active_sidebars );
$content_col_xl_width = 12 - 3 * count( $active_sidebars );
?>

<?php if ( in_array( 'sidebar-left', $active_sidebars ) ) : ?>
	<?php get_template_part( 'template-parts/sidebar-templates/sidebar', 'left' ); ?>
<?php endif; ?>

<div class="col-md-<?php echo $content_col_md_width; ?> col-xl-<?php echo $content_col_xl_width; ?> content-area" id="primary">
