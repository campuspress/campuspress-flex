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
				let posts_per_page = <?php echo absint( $atts['posts_per_page'] ); ?>;
				cpDirectories['<?php echo esc_attr( $dir_id ); ?>'] = new List('<?php echo esc_attr( $dir_id ); ?>', {
					valueNames: <?php echo $field_js; ?>,
					listClass: 'cp-dir-content-list',
					searchClass: 'cp-dir-field-search',
					<?php
					if( 0 < $atts['posts_per_page'] ) :
					?>
					pagination: true,
					page: posts_per_page,
					<?php
					endif;
					?>

				});

				let list = cpDirectories['<?php echo esc_attr( $dir_id ); ?>'];

				cp_dir_update_button = (list) => {
					// If all the elements are visible already, hide load more button.
					if (list.matchingItems.length === list.visibleItems.length) {
						jQuery('#cp-dir-load-more').removeClass('d-block').addClass('d-none');
					} else {
						jQuery('#cp-dir-load-more').removeClass('d-none').addClass('d-block');
					}
					return;
				}

				cp_dir_load_more = (list) => {
					let show_items = 0;

					// Return early if there is no list
					if ('undefined' === typeof (list)) {
						return;
					}

					show_items = parseInt(list.visibleItems.length) + posts_per_page;
					list.show(1, show_items);
				}

				// Handle Show more button click.
				jQuery('#cp-dir-load-more').on('click', () => {
					cp_dir_load_more(list);
				});

				// Handle Update event to show/hide show more button
				list.on('updated', () => {
					cp_dir_update_button(list);
				});

			</script>
			<?php
			$inline_script = str_replace( array( '<script>', '</script>' ), '', ob_get_clean() );
			wp_add_inline_script( 'cp-dir-block', $inline_script );

			if( 0 < $atts['posts_per_page'] ) :
			?>
				<ul class="pagination d-none"></ul>
				<button class="btn btn-primary d-block mx-auto" id="cp-dir-load-more" data-action="load-entries"
				        data-page="<?php echo absint( $data->paged ); ?>" aria-controls="<?php echo esc_attr( $dir_id ); ?>-content">
					<?php
					echo apply_filters( 'cp-dir-load-more-label', __( 'More Staff', 'cp-dir' ) );
					?>
				</button>
			<?php
			endif;
		}
		?>
	</div>
</div>
