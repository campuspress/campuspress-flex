<?php
$data = new CPDirectoryData( $atts );

if ( ! $data ) {
	return;
}

$label = $data->post_type_object->label;

$class_name = 'wp-block-cp-dir-cp-dir cp-dir';
if ( ! empty( $atts['className'] ) ) {
	$class_name .= ' ' . $atts['className'];
}

$dir_id = $data->get_directory_id();

$entries = $data->get_entries();
$entries_count = count( $entries );

$filters            = $data->get_filters();
$taxonomies_filters = $data->get_taxonomy_filters();
$posts_per_page     = $data->get_posts_per_page( $entries_count );
?>
<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $dir_id ); ?>" aria-label="<?php echo esc_attr( apply_filters( 'cp_dir_directory_label', $label, $data ) ); ?>" data-source="<?php echo esc_attr($data->post_type_object->name); ?>">
	<?php
	if ( $filters || $taxonomies_filters ) {
		?>
		<form class="cp-dir-controls" aria-controls="<?php echo esc_attr( $dir_id ); ?>-content">
			<div class="cp-dir-sr-info screen-reader-text">
				<?php echo apply_filters( 'cp_dir_directory_refresh_info', __( 'Items will instantly refresh upon filtering.', 'cp-dir' ), $data ); ?>
			</div>
			
			<?php include( apply_filters( 'cp_dir_path_directory_filters', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-filters.php', $data ) ); ?>
		</form>
		<?php
	}
	?>
	<div class="cp-dir-content" id="<?php echo esc_attr( $dir_id ); ?>-content" aria-label="<?php echo apply_filters( 'cp_dir_directory_entries_label', sprintf( __( '%s Entries', 'cp-dir' ), $label ) ); ?>">
		<div class="cp-dir-sr-info screen-reader-text" aria-live="polite">
			<?php printf( __( '%s results found.', 'cp-dir' ), '<span class="cp-dir-sr-info-count">' . $entries_count . '</span>' ); ?>
			<?php
			if ( $posts_per_page ) {
				printf( __( 'First %s results are being shown', 'cp-dir' ), '<span class="cp-dir-sr-info-per-page">' . $posts_per_page . '</span>' );
			}
			?>
		</div>
		<?php
		if ( $entries_count ) {
			$fields = $data->get_fields();

			include( apply_filters( 'cp_dir_path_directory_content', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-content.php', $data ) );

			include( apply_filters( 'cp_dir_path_directory_after', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-after.php', $data ) );
			
			$field_js = json_encode( $data->get_fields_js() );
			ob_start();
			?>
			<script>
			cpDirectories['<?php echo esc_attr( $dir_id ); ?>'] = new List( '<?php echo esc_attr( $dir_id ); ?>', {
					valueNames: <?php echo $field_js; ?>,
					listClass: 'cp-dir-content-list',
					searchClass: 'cp-dir-field-search',
					searchDelay: 250,
					page: <?php echo $posts_per_page ? $posts_per_page : $data->get_entries_limit(); ?>,
			} );
			</script>
			<?php
			$inline_script = str_replace( array( '<script>', '</script>' ), '', ob_get_clean() );
			wp_add_inline_script( 'cp-dir-block', $inline_script );

			add_action('wp_footer', function() {
				wp_enqueue_script( 'cp-dir-block' );
			});
		}
		?>
	</div>
</div>
