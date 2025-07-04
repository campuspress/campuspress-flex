<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package cpschool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'cpschool_body_classes' ) ) {
	add_filter( 'body_class', 'cpschool_body_classes' );

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	function cpschool_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		//check if current page has sidebar set
		if ( cpschool_get_active_sidebars() ) {
			$classes[] = 'has-sidebar';
		}

		$hero_style = cpschool_get_hero_style();
		if ( $hero_style ) {
			$classes[] = 'has-hero';

			// Adds a class for single pages with featured images set
			if ( ( is_singular() && has_post_thumbnail() ) || ( $hero_style != 'img-under-title' && get_theme_mod( 'hero_main_default_images' ) ) ) {
				$classes[] = 'has-hero-image';
				if ( has_post_thumbnail() ) {
					$classes[] = 'has-featured-image';
				}
			}
		}

		if ( is_singular() ) {
			$classes[] = 'singular';

			// Adds a class that adjust default top margin for content.
			$top_margin = get_post_meta( get_the_ID(), 'cps_top_margin', true );
			if ( $top_margin == 'remove' ) {
				$classes[] = 'main-wrapper-margin-top-disabled';

				if ( ! $hero_style ) {
					$pull_under = get_post_meta( get_the_ID(), 'cps_content_pull_under', true );
					if ( $pull_under ) {
						$classes[] = 'main-wrapper-pull-under';
						$classes[] = 'has-hero';
					}
				}
			}

			// Adds a class that adjust default bottom margin for content.
			$bottom_margin = get_post_meta( get_the_ID(), 'cps_bottom_margin', true );
			if ( $bottom_margin == 'remove' ) {
				$classes[] = 'main-wrapper-margin-bottom-disabled';
			}
		} else {
			// Adds a class of hfeed to non-singular pages.
			$classes[] = 'hfeed';
			$classes[] = 'entries-list';
		}

		// Adds class for customizer. TODO we schould have special stylesheet to load instead.
		if ( is_customize_preview() ) {
			$classes[] = 'is-customizer';
		}

		// Adds special class to handle secondary header being under primary.
		if ( get_theme_mod( 'header_secondary_under_primary' ) == true ) {
			$classes[] = 'navbar-secondary-under-main';
		}

		return $classes;
	}
}

if ( ! function_exists( 'cpschool_pingback' ) ) {
	add_action( 'wp_head', 'cpschool_pingback' );

	/**
	 * Add a pingback url auto-discovery header for single posts of any post type.
	 */
	function cpschool_pingback() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="' . esc_url( get_bloginfo( 'pingback_url' ) ) . '">' . "\n";
		}
	}
}

if ( ! function_exists( 'cpschool_mobile_web_app_meta' ) ) {
	add_action( 'wp_head', 'cpschool_mobile_web_app_meta' );

	/**
	 * Add mobile-web-app meta.
	 */
	function cpschool_mobile_web_app_meta() {
		echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
		echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
		echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
	}
}

if ( ! function_exists( 'cpschool_adjacent_post_link_change' ) ) {
	add_filter( 'previous_post_link', 'cpschool_adjacent_post_link_change' );
	add_filter( 'next_post_link', 'cpschool_adjacent_post_link_change' );

	/**
	 * Add Mega Menu Menu items
	 */
	function cpschool_adjacent_post_link_change( $output ) {
		$output = str_replace( 'rel="', 'class="btn btn-secondary" rel="', $output );

		return $output;
	}
}

if ( ! function_exists( 'cpschool_show_reusable_blocks_admin' ) ) {
	add_filter( 'register_post_type_args', 'cpschool_show_reusable_blocks_admin', 10, 2 );

	/**
	 * Add site info hook to WP hook library.
	 */
	function cpschool_show_reusable_blocks_admin( $args, $post_type ) {
		if ( is_admin() && $post_type == 'wp_block' ) {
			$args['show_in_menu']        = true;
			$args['_builtin']            = false;
			$args['labels']['name']      = __( 'Reusable Blocks' );
			$args['labels']['menu_name'] = __( 'Reusable Blocks' );
			$args['menu_icon']           = 'dashicons-screenoptions';
			$args['menu_position']       = 58;
			$args['show_in_nav_menus']   = true;
		}

		return $args;
	}
}


if ( ! function_exists( 'cpschool_custom_excerpt_more' ) ) {
	add_filter( 'excerpt_more', 'cpschool_custom_excerpt_more' );

	/**
	 * Removes the ... from the excerpt read more link
	 *
	 * @param string $more The excerpt.
	 *
	 * @return string
	 */
	function cpschool_custom_excerpt_more( $more ) {
		if ( ! is_admin() ) {
			$more = '';
		}
		return $more;
	}
}

if ( ! function_exists( 'cpschool_all_excerpts_get_more_link' ) ) {
	add_filter( 'get_the_excerpt', 'cpschool_all_excerpts_get_more_link', 20, 2 );

	/**
	 * Adds a custom read more link to all excerpts, manually or automatically generated
	 *
	 * @param string $post_excerpt Posts's excerpt.
	 *
	 * @return string
	 */
	function cpschool_all_excerpts_get_more_link( $post_excerpt, $post ) {
		if ( ! is_admin() || wp_doing_ajax() ) {
			$post_excerpt = $post_excerpt . '...';

			if ( is_search() ) {
				$search_style = get_theme_mod( 'search_results_style' );
				if ( $search_style != 'posts_list' ) {
					$hide = true;
				}
			}
			if ( ! isset( $hide ) ) {
				if( is_customize_preview() ) {
					$hide = false;
				}
				else {
					$hide = get_theme_mod( 'entries_lists_hide_continue_reading' );
				}
			}

			if ( ! $hide ) {
				$classes      = cpschool_class( 'read-more-link', 'btn btn-secondary cpschool-read-more-link', true );
				$post_excerpt = $post_excerpt . '<span class="cpschool-read-more-link-holder"><a class="' . esc_attr( implode( ' ', $classes ) ) . '" href="' . esc_url( get_permalink( $post->ID ) ) . '">' . sprintf( __( 'Continue Reading %s', 'cpschool' ), '<span class="sr-only">' . get_the_title( $post->ID ) . '</span>' ) . '</a></span>';
			}
		}
		return $post_excerpt;
	}
}

if ( ! function_exists( 'cpschool_get_the_archive_title_prefix' ) ) {
	add_filter( 'get_the_archive_title_prefix', 'cpschool_get_the_archive_title_prefix' );

	/**
	 * Removes "Archives:" for post type archives titles.
	 *
	 * @param string $prefix Prefix for archive title.
	 *
	 * @return string
	 */
	function cpschool_get_the_archive_title_prefix( $prefix ) {
		if ( $prefix && is_post_type_archive() ) {
			$archives_prefix = _x( 'Archives:', 'post type archive title prefix' );
			if ( $prefix == $archives_prefix ) {
				$prefix = '';
			}
		}

		return $prefix;
	}
}

if ( ! function_exists( 'cpschool_comment_reply_link' ) ) {
	add_filter( 'comment_reply_link', 'cpschool_comment_reply_link' );

	/**
	 * Adds button class to comments reply link
	 *
	 * @param string $link Comments reply link html.
	 *
	 * @return string
	 */
	function cpschool_comment_reply_link( $link ) {
		$link = str_replace( 'comment-reply-link', 'comment-reply-link btn btn-secondary btn-sm', $link );

		return $link;
	}
}

if ( ! function_exists( 'cpschool_cancel_comment_reply_link' ) ) {
	add_filter( 'cancel_comment_reply_link', 'cpschool_cancel_comment_reply_link' );

	/**
	 * Adds button class to comments cancel reply link
	 *
	 * @param string $link Comments reply link html.
	 *
	 * @return string
	 */
	function cpschool_cancel_comment_reply_link( $link ) {
		$link = str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" class="comment-reply-link btn btn-secondary btn-sm"', $link );

		return $link;
	}
}

if ( ! function_exists( 'cpschool_custom_logo_remove_link' ) ) {
	add_filter( 'get_custom_logo', 'cpschool_custom_logo_remove_link', 10, 2 );

	/**
	 * Remove custom logo generated link
	 *
	 * @param string $html
	 *
	 * @param int $blog_id
	 *
	 * @return string
	 */
	function cpschool_custom_logo_remove_link( $html, $blog_id ) {
		return strip_tags( $html, array( 'img' ) );
	}
}

if ( ! function_exists( 'cpschool_custom_logo_image_attrs' ) ) {
	add_filter( 'get_custom_logo_image_attributes', 'cpschool_custom_logo_image_attrs', 15, 3 );

	/**
	 * @param array $custom_logo_attr Custom logo image attributes.
	 * @param int   $custom_logo_id   Custom logo attachment ID.
	 * @param int   $blog_id          ID of the blog to get the custom logo for.
	 *
	 * @return array
	 */
	function cpschool_custom_logo_image_attrs( $custom_logo_attr, $custom_logo_id, $blog_id ) {

		if ( empty( $custom_logo_attr['alt'] ) && is_front_page() ) {
			$custom_logo_attr['alt'] = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );

			if ( $custom_logo_attr['alt'] ) {
				$custom_logo_attr['title'] = get_the_title( $custom_logo_id );
			}
		} else {
			$custom_logo_attr['alt']   = '';
			$custom_logo_attr['title'] = 'Home';
		}
		$custom_logo_attr['class'] = 'img-fluid';

		return $custom_logo_attr;
	}
}

if ( ! function_exists( 'cpschool_add_acf_notice_on_themes_page' ) ) {
	add_action( 'admin_notices', 'cpschool_add_acf_notice_on_themes_page' );

	/**
	 * Displays an admin notice on the Themes page if ACF is not active.
	 *
	 * @return void
	 */
	function cpschool_add_acf_notice_on_themes_page() {
		// Check if we are on the Themes page.
		$current_screen = get_current_screen();

		if ( $current_screen && 'themes' === $current_screen->id ) {
			// Check if ACF is active.
			if ( ! class_exists( 'ACF' ) ) {
				?>
				<div class="notice notice-warning">
					<p>
						<?php esc_html_e( 'To enable all CampusPress Flex features, please install the "Advanced Custom Fields" plugin.', 'cpschool' ); ?>
					</p>
				</div>
				<?php
			}
		}
	}
}
