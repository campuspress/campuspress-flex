<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="main-wrapper wrapper" id="index-wrapper">
	<div <?php cpschool_class( 'content', 'container' ); ?> id="content">
		<div class="row">
			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'template-parts/global-templates/left-sidebar-check', get_post_type() ); ?>

			<main class="site-main" id="main">
				<?php if ( have_posts() ) : ?>
					<?php get_template_part( 'template-parts/global-templates/pagetitle', get_post_type() ); ?>

					<?php /* Start the Loop */ ?>

					<div <?php cpschool_class( 'entries-row', 'entries-row row' ); ?>>

						<?php
						while ( have_posts() ) :
							the_post();
							?>
							<?php get_template_part( 'template-parts/loop-templates/content', cpschool_get_content_format() ); ?>
						<?php endwhile; ?>
					</div>
				<?php else : ?>
					<?php get_template_part( 'template-parts/loop-templates/content', 'none' ); ?>
				<?php endif; ?>
			</main><!-- #main -->

			<!-- The pagination component -->
			<?php cpschool_pagination(); ?>

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'template-parts/global-templates/right-sidebar-check', get_post_type() ); ?>
		</div><!-- .row -->
	</div><!-- #content -->
</div><!-- #index-wrapper -->

<?php
get_footer();
