<?php
/**
 * Adjust editor
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cpschool_add_editor_styles' ) ) {
	add_action( 'enqueue_block_editor_assets', 'cpschool_add_editor_styles', 20 );

	/**
	 * Registers blocks stylesheet for the theme.
	 */
	function cpschool_add_editor_styles() {
		$css_version = filemtime( get_template_directory() . '/css/block-editor.min.css' );
		wp_enqueue_style( 'cpschool-gutenberg', get_template_directory_uri() . '/css/block-editor.min.css', false, $css_version );

		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
		remove_filter( 'block_editor_settings_all', 'wp_add_editor_classic_theme_styles' );
	}
}

if ( ! function_exists( 'cpschool_block_editor_settings' ) ) {
	add_filter( 'block_editor_settings', 'cpschool_block_editor_settings', 10, 2 );

	function cpschool_block_editor_settings( $editor_settings, $post ) {
		$editor_settings['styles'][] = array( 'css' => '.editor-styles-wrapper { font-family: "Inter var"; }' );

		return $editor_settings;
	}
}
