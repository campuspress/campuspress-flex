<?php
$dir_id = isset( $_REQUEST['cp-dir-id'] ) ? $_REQUEST['cp-dir-id'] : false;
$data   = new CPDirectoryEntryData( get_post(), $dir_id );

$dir_link_class = apply_filters( 'cp_dir_link_class', 'cp-dir-item-dir-link' );
$dir_link_text  = apply_filters( 'cp_dir_link_text', '<i class="nav-icon nav-icon-position-before cps-icon cps-dashicon dashicons-arrow-left-alt2" aria-hidden="true"></i>' . __( 'Go Back To Directory', 'cp-dir' ) );
$dir_link   = $data->get_dir_link();

$fields = $data->get_fields();

if ( $fields ) {
	echo '<div class="cp-dir-item-fields">';
	foreach ( $fields as $field ) {
		$value = cp_dir_get_field_value( get_the_ID(), $field );
		if ( $value['content'] ) {
			?>
			<div class="cp-dir-item-field cp-dir-item-field-<?php echo esc_attr( $field['field_name'] ); ?>">
				<strong><?php echo $field['label']; ?>:</strong> <?php echo $value['content']; ?>
			</div>
			<?php
		}
	}
	echo '</div>';
}

echo $content;

if ( $dir_link ) {
	echo '<p class="cp-dir-item-dir-link-holder"><a class="' . esc_attr( $dir_link_class ) . '" href="' . esc_url( $dir_link ) . '">' . $dir_link_text . '</a></p>';
}
