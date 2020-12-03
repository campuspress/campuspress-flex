<div id="modal-search" class="modal fade modal-slide-in-top modal-close-inline modal-site-width modal-padding-lg" tabindex="-1" role="dialog" aria-label="<?php echo esc_attr( 'search', 'cpschool' ); ?>" aria-hidden="true">
	<div class="modal-dialog site-width-max" role="document">
		<div class="modal-content has-background has-header-main-bg-color-background-color">
			<div class="modal-header pb-0">
				<button type="button" class="close" data-dismiss="modal">
					<i aria-hidden="true" class="cps-icon cps-icon-close"></i>
					<span class="sr-only"><?php _e( 'close search', 'mabts' ); ?></span>
				</button>
			</div>
			<div class="modal-body">
				<form <?php cpschool_class( 'modal-search-form', 'search-form d-flex' ); ?> method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="sr-only" for="search-form-header"><?php _e( 'Search', 'mabts' ); ?></label>
					<input id="search-form-header" class="form-control form-control-lg" type="search" placeholder="<?php _e( 'Search...', 'mabts' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
					<button class="btn btn-secondary btn-lg" type="submit" aria-controls="search-form-header">
						<i aria-hidden="true" class="cps-icon cps-icon-search"></i>
						<span class="sr-only"><?php _e( 'Search Site', 'mabts' ); ?></span>
					</button>
				</form>
			</div>
		</div>
	</div>
</div><!-- #modal-search -->
