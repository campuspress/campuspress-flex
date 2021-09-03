<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="entry-col col-12">
	<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<header <?php cpschool_class( 'entry-header', 'entry-header' ); ?>>
			<small>
				<a href="<?php echo esc_url(get_permalink()); ?>" aria-hidden="true">
					<?php echo str_replace( array( 'http://', 'https://' ), '', esc_url( get_permalink() ) ); ?>
				</a>
			</small>

			<?php
			$content_indicator = false;
			$icon_class        = cpschool_get_post_type_icon_class( get_post_type() );
			if ( $icon_class ) {
				$post_type_object  = get_post_type_object( get_post_type() );
				$label             = $post_type_object->labels->singular_name;
				$content_indicator = '<span class="entry-type-idicator cps-icon ' . $icon_class . '" aria-hidden="true" title="' . sprintf( esc_html__( 'This is a "%s"', 'cpschool' ), $label ) . '"></span><span class="sr-only">' . sprintf( esc_html__( '(This is a "%s")', 'cpschool' ), $label ) . '</span>';
			}
			the_title(
				sprintf(
					'<h2 class="entry-title h-style-disable">%s<a href="%s" rel="bookmark">',
					$content_indicator,
					esc_url( get_permalink() )
				),
				'</a></h2>'
			);
			?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php
			$cpschool_get_post_meta = cpschool_get_post_meta( get_the_ID(), is_singular() );
			if ( $cpschool_get_post_meta ) {
				?>
				<div class="entry-meta">
					<?php echo cpschool_get_post_meta( get_the_ID(), is_singular() ); ?>
				</div><!-- .entry-meta -->
				<?php
			}
			?>
		</footer><!-- .entry-footer -->
	</article><!-- #post-## -->
</div>
