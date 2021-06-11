<?php
$hero_style = cpschool_get_hero_style();

function img_placement( $img_position, $img_class ){
	global $hero_style;

	$thumbnail_post_id = false;
	if ( is_singular() ) {
		$thumbnail_post_id = get_the_ID();
	} elseif ( is_home() ) {
		$thumbnail_post_id = get_option( 'page_for_posts' );
	}
	$thumbnail_size = 'hero';
	if ( $hero_style == $img_position && ! is_customize_preview() ) {
		$thumbnail_size = 'large';
	}
	if ( is_singular() && has_post_thumbnail( $thumbnail_post_id ) ) {
		if ( $img_class == 'hero-image-top' ){
			?>
			<div class="hero-image-holder hero-featured-image-holder hero-image-top" data-aos="fade" data-aos-duration="1000">
			<?php
		} else { ?>
			<div class="hero-image-holder hero-featured-image-holder hero-image-bottom" data-aos="fade" data-aos-duration="1000">
			<?php
		}
				echo get_the_post_thumbnail( $thumbnail_post_id, $thumbnail_size ); ?>
			</div>
		<?php
	} elseif ( ( $hero_style != $img_position || is_customize_preview() ) && $hero_default_images = get_theme_mod( 'hero_main_default_images' ) ) {
		$thumbnail_id = $hero_default_images[ mt_rand( 0, count( $hero_default_images ) - 1 ) ]['id'];
		?>
	
		<div <?php cpschool_class( 'hero-main-default-image-holder', 'hero-image-holder hero-default-image-holder' ); ?> data-aos="fade" data-aos-duration="1000">
			<?php echo wp_get_attachment_image( $thumbnail_id, $thumbnail_size ); ?>
		</div>
		<?php
	}
}

if ( $hero_style || ( is_customize_preview() && ( ! is_singular() || ! get_post_meta( get_the_ID(), 'cps_hero_title_disable', true ) ) ) ) {
	$title = cpschool_get_page_title();
	?>
	<header id="hero-main" <?php cpschool_class( 'hero-main', 'hero jumbotron jumbotron-fluid has-background has-hero-main-bg-color-background-color' ); ?> aria-label="<?php esc_html_e( 'page title and basic information', 'cpschool' ); ?>">
		
		<?php
			if ($hero_style == 'img-above-title' || is_customize_preview()){
				img_placement( 'img-above-title', 'hero-image-top' );
			}
			?>

		<div class="hero-content container" data-aos="fade" data-aos-delay="500" data-aos-duration="1000">
			<?php
			if ( cpschool_is_breadcrumb_enabled( 'hero' ) || is_customize_preview() ) {
				cpschool_show_breadcrumb( 'hero-breadcrumb' );
			}
			?>

			<?php if ( in_array( $hero_style, array( 'full-title-over-img', 'img-under-title','img-above-title' ) ) || is_customize_preview() ) { ?>
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
						<?php 
						if ( $subtitle ) {
							echo $subtitle;
						}
						if ( $meta ) {
							echo $meta;
						} 
						?>
					</div>
					<?php
				}
				?>
			<?php } ?>
		</div>
		
		<?php
		if ($hero_style != 'img-above-title' || is_customize_preview()){
			img_placement( 'img-under-title', 'hero-image-bottom' );
		}
		?>
		 
	</header>
	<?php
}
