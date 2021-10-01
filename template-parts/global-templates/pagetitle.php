<?php
do_action('cpschool_pagetitle_start');

$hero_style = cpschool_get_hero_style();

if ( in_array( $hero_style, array( false, 'disabled', 'full-title-under-img' ) ) || is_customize_preview() ) {
	$title = cpschool_get_page_title();

	if ( $title && ( ! is_home() || get_theme_mod( 'posts_main_hero' ) || is_customize_preview() ) ) {
		?>
		<header <?php cpschool_class( 'page-header', 'page-header' ); ?>>
			<?php
			if ( cpschool_is_breadcrumb_enabled( 'page' ) || is_customize_preview() ) {
				cpschool_show_breadcrumb( 'page-breadcrumb' );
			}
			?>
			<?php if( $title ) { ?>
				<h1 class="page-title entry-title"><?php echo $title; ?></h1>
			<?php } ?>

			<?php
			$subtitle = cpschool_get_page_subtitle();
			if ( is_singular() ) {
				$meta = cpschool_get_post_meta( get_the_ID(), is_singular() );
			} else {
				$meta = false;
			}
			if ( $subtitle || $meta ) {
				?>
				<div class="page-meta entry-meta">
					<?php if ( $subtitle ) { ?>
						<p>
							<?php echo $subtitle; ?>
						</p>
					<?php } ?>
					<?php if ( $meta ) { ?>
						<?php echo $meta; ?>
					<?php } ?>
				</div>
				<?php
			}
			?>
		</header><!-- .page-header -->

		<?php
		$entry_featured_image = ! $hero_style && is_singular() && has_post_thumbnail();

		/**
		 * Filters whether to show or not post featured image when Hero is disabled.
		 *
		 * @since 0.9.2
		 *
		 * @param bool $entry_featured_image Display Featured image or not.
		 */
		if ( apply_filters( 'entry_single_featured_image', $entry_featured_image ) ) { ?>
			<div <?php cpschool_class( 'entry-single-featured-image', 'entry-featured-image' ); ?>>
				<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
			</div>
			<?php
		}
	}
}

do_action('cpschool_pagetitle_end');
