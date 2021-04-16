<?php
if ( $posts_per_page ) {
    ?>
    <div class="cp-dir-pagination" data-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
        <div class="cp-dir-control-load-more">
            <button class="<?php echo apply_filters( 'cp_dir_load_more_button_class', __( 'cp-dir-btn cp-dir-control-load-more-btn', 'cp-dir' ) );?>" aria-controls="<?php echo esc_attr( $dir_id ); ?>-content">
                <?php echo apply_filters( 'cp_dir_load_more_label', __( 'Load More', 'cp-dir' ) );?>
            </button>
        </div>
        <button class="cp-dir-sr-load-jump-btn screen-reader-text" style="display: none;">
            <?php echo __( 'Go to first recently loaded item', 'cp-dir' );?>
        </button>
    </div>
    <?php
}
