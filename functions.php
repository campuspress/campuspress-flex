<?php
/**
 * Functions and definitions
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$cpschool_includes = array(
	'/theme-settings.php',
	'/setup.php',
	'/hooks-custom.php',
	'/plugins/calendar-plus.php',
	'/plugins/advanced-custom-fields-setup.php',
	'/plugins/kirki/kirki.php',
	'/plugins/menu-icons/wp-menu-icons.php',
	'/plugins/breadcrumbs.php',
	'/plugins/cp-directory-setup.php',
	'/widgets.php',
	'/enqueue.php',
	'/template-tags.php',
	'/hooks-wp.php',
	'/customizer.php',
	'/comments.php',
	'/class-wp-bootstrap-navwalker.php',
	'/editor.php',
);

$cpschool_includes = apply_filters( 'cpschool_includes', $cpschool_includes );

foreach ( $cpschool_includes as $file ) {
	$filepath = locate_template( 'inc' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}
