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

$entries       = $data->get_entries();
$entries_count = count( $entries );

$filters            = $data->get_filters();
$taxonomies_filters = $data->get_taxonomy_filters();

$posts_per_page = $data->get_posts_per_page( $entries_count );
$aria_label     = apply_filters( 'cp_dir_directory_label', $label, $data );
$filters_logic  = apply_filters( 'cp_dir_filters_logic', ( isset( $atts['filters_logic'] ) && $atts['filters_logic'] ) ? $atts['filters_logic'] : '' );
$pagination     = apply_filters( 'cp_dir_pagination', ( $posts_per_page && isset( $atts['pagination'] ) && $atts['pagination'] ) ? true : false, $data );

$content_class = 'cp-dir-content';
if ( !$entries_count ) {
	$content_class .= ' cp-dir-content--no-results';
}
?>
<div class="<?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr( $dir_id ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>" data-source="<?php echo esc_attr( $data->post_type_object->name ); ?>" data-filters-logic="<?php echo esc_attr( $filters_logic ); ?>">
	<?php
	if ( $filters || $taxonomies_filters ) {
		?>
		<form class="<?php echo apply_filters( 'cp_dir_controls_class', 'cp-dir-controls', $data ); ?>" aria-controls="<?php echo esc_attr( $dir_id ); ?>-content">
			<fieldset>
				<legend class="cp-dir-sr-info screen-reader-text">
					<?php echo apply_filters( 'cp_dir_directory_refresh_info', __( 'Items will instantly refresh upon filtering.', 'cp-dir' ), $data ); ?>
				</legend>
				
				<?php include( apply_filters( 'cp_dir_path_directory_filters', $this->dir . '/cp-directory-files/blocks/cp-dir/template-parts/directory-filters.php', $data ) ); ?>
			</fieldset>
		</form>
		<?php
	}
	?>
	<div class="<?php echo esc_attr( apply_filters( 'cp_dir_content_class', $content_class, $data ) ); ?>" id="<?php echo esc_attr( $dir_id ); ?>-content" aria-label="<?php echo esc_attr( apply_filters( 'cp_dir_directory_entries_label', sprintf( __( '%s Entries', 'cp-dir' ), $label ), $data ) ); ?>">
		<div class="cp-dir-sr-info cp-dir-sr-info-count screen-reader-text" aria-live="polite" aria-atomic="true" aria-relevant="all">
			<?php printf( __( '%s results found.', 'cp-dir' ), '<span class="cp-dir-sr-info-count-number">' . $entries_count . '</span>' ); ?>
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

			if ( $filters || $taxonomies_filters ) {
				$field_js = json_encode( $data->get_fields_js() );
				$args     = array(
					'valueNames'  => $data->get_fields_js(),
					'listClass'   => 'cp-dir-content-list',
					'searchClass' => 'cp-dir-field-search',
					'searchDelay' => 250,
					'page'        => esc_js( $posts_per_page ? $posts_per_page : $data->get_entries_limit() ),
				);
				if( $pagination ) {
					$args['pagination'] = array(
						'paginationClass' => 'cp-dir-pagination',
						'item' => "<li><button class='page'></button></li>"
					);
				}
				ob_start();
				?>
				<script>
				cpDirectories['<?php echo esc_attr( $dir_id ); ?>'] = new List( '<?php echo esc_attr( $dir_id ); ?>', <?php echo json_encode( $args ); ?> );
				</script>
				<?php
				$inline_script = str_replace( array( '<script>', '</script>' ), '', ob_get_clean() );
				wp_add_inline_script( 'cp-dir-block', $inline_script );

				add_action(
					'wp_footer',
					function() {
						wp_enqueue_script( 'cp-dir-block' );
					}
				);
			}
		}
		?>
		<div class="cp-dir-no-results-info" aria-hidden="true">
			<?php echo apply_filters( 'cp_dir_directory_no-results_info', __( 'No results found.', 'cp-dir' ) ); ?>
		</div>
		<?php if( $pagination ) { ?>
			<nav class="cp-dir-pagination-holder" aria-label="<?php echo esc_attr( sprintf( __('%s pagination'), $aria_label ) ); ?>">
				<ul class="cp-dir-pagination"></ul>
			</nav>
		<?php } ?>
	</div>
</div>
