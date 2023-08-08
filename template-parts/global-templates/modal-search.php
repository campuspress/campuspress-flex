<div id="modal-search" class="modal fade modal-slide-in-top modal-close-inline modal-site-width modal-padding-lg" tabindex="-1" role="dialog" aria-label="<?php echo esc_attr( 'search', 'cpschool' ); ?>" aria-hidden="true">
	<div class="modal-dialog site-width-max" role="document">
		<div class="modal-content has-background has-header-main-bg-color-background-color">
			<div class="modal-header pb-0">
				<button type="button" class="close" data-dismiss="modal">
					<i aria-hidden="true" class="cps-icon cps-icon-close"></i>
					<span class="sr-only"><?php _e( 'close search', 'cpschool' ); ?></span>
				</button>
			</div>
			<div class="modal-body">
				<?php 
				get_search_form( array(
					'field_class' => 'field form-control form-control-lg',
					'btn_class' => 'submit btn btn-primary btn-lg',
				) ); 
				?>
			</div>
		</div>
	</div>
</div><!-- #modal-search -->
