<?php
/**
 * The template for displaying search forms
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( empty( $args['id'] ) ) {
	$args['id'] = 'searchform';
}
if ( empty( $args['field_class'] ) ) {
	$args['field_class'] = 'field form-control';
}
if ( empty( $args['btn_class'] ) ) {
	$args['btn_class'] = 'submit btn btn-primary';
}
?>

<form method="get" id="<?php echo esc_attr( $args['id'] ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
	<label class="sr-only" for="<?php echo esc_attr( $args['id'] . '-s' ); ?>"><?php esc_html_e( 'Search', 'cpschool' ); ?></label>
	<div class="input-group">
		<input class="<?php echo esc_attr( $args['field_class'] ); ?>" id="<?php echo esc_attr( $args['id'] . '-s' ); ?>" name="s" type="text" placeholder="<?php esc_attr_e( 'Search...', 'cpschool' ); ?>" value="<?php the_search_query(); ?>">
		<span class="input-group-append">
			<button class="<?php echo esc_attr( $args['btn_class'] ); ?>" id="<?php echo esc_attr( $args['id'] . '-submit' ); ?>" name="submit" type="submit">
				<i aria-hidden="true" class="cps-icon cps-icon-search"></i>
				<span class="sr-only"><?php _e( 'Search Site', 'cpschool' ); ?></span>
			</button>
		</span>
	</div>
</form>
