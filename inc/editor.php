<?php
/**
 * Adjust editor
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cpschool_add_editor_styles' ) ) {
	// Must run on after_setup_theme so add_editor_style() loads into the iframed canvas (WP 6.2+).
	add_action( 'after_setup_theme', 'cpschool_add_editor_styles' );

	/**
	 * Registers the blocks stylesheet with the block editor canvas.
	 */
	function cpschool_add_editor_styles() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'css/block-editor.min.css' );
	}
}

if ( ! function_exists( 'cpschool_remove_classic_theme_styles' ) ) {
	// Keep this on enqueue_block_editor_assets: it must not run on front end requests,
	// where removing wp_enqueue_classic_theme_styles would drop core styles from the site.
	add_action( 'enqueue_block_editor_assets', 'cpschool_remove_classic_theme_styles', 20 );

	/**
	 * Removes the core classic theme styles from the block editor.
	 */
	function cpschool_remove_classic_theme_styles() {
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
		remove_filter( 'block_editor_settings_all', 'wp_add_editor_classic_theme_styles' );
	}
}

if ( ! function_exists( 'cpschool_block_editor_settings' ) ) {
	add_filter( 'block_editor_settings_all', 'cpschool_block_editor_settings', 10, 2 );

	function cpschool_block_editor_settings( $editor_settings, $post ) {
		$font_family = strtolower( trim( (string) get_theme_mod( 'body_font_family', 'public_sans' ) ) );

		// Keep only safe characters used by the theme's font slugs.
		$font_family = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $font_family );

		// Backward compatibility for legacy hardcoded value.
		if ( 'intervar' === $font_family ) {
			$font_family = 'inter';
		}

		if ( '' === $font_family ) {
			$font_family = 'public_sans';
		}

		$editor_settings['styles'][] = array(
			'css' => '.editor-styles-wrapper { font-family: ' . $font_family . ', system-ui, sans-serif; }',
		);

		return $editor_settings;
	}
}
