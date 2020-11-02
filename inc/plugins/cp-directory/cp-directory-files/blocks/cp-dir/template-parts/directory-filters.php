<?php if( in_array( 'search', $filters ) ) { ?>
    <div class="cp-dir-control cp-dir-control-text cp-dir-control-search">
        <?php $field_id = $dir_id . '-search'; ?>
        <label class="screen-reader-text" for="<?php esc_attr_e($field_id); ?>">Search</label>
        <input class="cp-dir-field cp-dir-field-search" id="<?php esc_attr_e($field_id); ?>" name="search" type="text" placeholder="<?php esc_attr_e( 'Search &hellip;', 'cp-dir' ); ?>" value="">
    </div>
<?php } ?>
<?php 
if( $taxonomies_filters ) {
    foreach( $taxonomies_filters as $filter ) {
    ?>
        <div class="cp-dir-control cp-dir-control-select" data-field-name="<?php esc_attr_e( $filter['field_name'] ); ?>">
            <label class="screen-reader-text" for="<?php esc_attr_e( $filter['select_id'] ); ?>"><?php printf( __( 'Filter By %s', 'cp-dir' ), ucfirst( $filter['label'] ) ); ?></label>
            <?php 
            wp_dropdown_categories( array(
                'show_option_all' => '<span aria-hidden="true">' . sprintf( __( 'Filter "%s"', 'cp-dir' ), ucfirst( $filter['label'] ) ) . '</span>',
                'taxonomy' => $filter['taxonomy'],
                'hierarchical' => true,
                'orderby' => 'name',
                'name' => 'taxonomies[' . $filter['taxonomy'] . '][]',
                'child_of' => $filter['parent_id'],
                'id' => $filter['select_id'],
                'class' => 'cp-dir-field cp-dir-field-tax',
                'hide_if_empty' => true
            ) ); 
            ?>
        </div>
    <?php
    }
}
?>
<?php if( in_array( 'clear', $filters ) ) { ?>
    <div class="cp-dir-control cp-dir-control-button cp-dir-control-clear">
        <button disabled><?php _e( 'Clear Results', 'cp-dir' ); ?></button>
    </div>
<?php } ?>