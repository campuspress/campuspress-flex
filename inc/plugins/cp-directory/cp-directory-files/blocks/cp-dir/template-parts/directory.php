<?php
$data = new CPDirectoryData( $atts );

if ( ! $data ) {
	return;
}

$class_name = 'wp-block-cp-dir-cp-dir cp-dir';
if ( ! empty( $atts['className'] ) ) {
	$class_name .= ' ' . $atts['className'];
}

$dir_id = $data->get_directory_id();

$entries = $data->get_entries();

$filters            = $data->get_filters();
$taxonomies_filters = $data->get_taxonomy_filters();
?>
<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $dir_id ); ?>" aria-label="<?php esc_attr_e( 'Directory', 'cp-dir' ); ?>">
	<?php
	if ( $filters || $taxonomies_filters ) {
		?>
		<form class="cp-dir-controls" aria-controls="<?php echo esc_attr( $dir_id ); ?>-content">
			<div class="cp-dir-sr-info screen-reader-text">
				<?php _e( 'Directory instantly refresh upon filtering.', 'cp-dir' ); ?>
			</div>
			
			<?php include( apply_filters( 'cp_dir_path_directory_filters', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-filters.php', $data ) ); ?>
		</form>
		<?php
	}
	?>
	<div class="cp-dir-content" id="<?php echo esc_attr( $dir_id ); ?>-content" aria-label="<?php esc_attr_e( 'Directory Entries', 'cp-dir' ); ?>">
		<div class="cp-dir-sr-info screen-reader-text" aria-live="polite">
			<?php printf( __( '%s results found', 'cp-dir' ), '<span class="cp-dir-sr-info-count">' . count( $entries ) . '</span>' ); ?>
		</div>
		<?php
		if ( $entries ) {
			$fields = $data->get_fields();

			include( apply_filters( 'cp_dir_path_directory_content', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-content.php', $data ) );

			wp_enqueue_script( 'cp-dir-block' );

			$field_js = json_encode( $data->get_fields_js() );
			ob_start();
			?>
			<script>
			cpDirectories['<?php echo esc_attr( $dir_id ); ?>'] = new List( '<?php echo esc_attr( $dir_id ); ?>', {
					valueNames: <?php echo $field_js; ?>,
					listClass: 'cp-dir-content-list',
					searchClass: 'cp-dir-field-search',
			} );
			</script>
			<?php
			$inline_script = str_replace( array( '<script>', '</script>' ), '', ob_get_clean() );
			wp_add_inline_script( 'cp-dir-block', $inline_script );
		}
		?>
	</div>
</div>
