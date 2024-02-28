<?php
// Co-Author Plus integration
if ( function_exists( 'coauthors_posts_links' ) ) {
    if ( ! function_exists( 'cpschool_coauthor_integration' ) ) {
        add_filter( 'cpschool_author_link', 'cpschool_coauthor_integration' );
        function cpschool_coauthor_integration( $author_link ) {
            if ( function_exists( 'coauthors_posts_links' ) ) {
                $author_link = coauthors_posts_links( null, null, null, null, false );
            }
            return $author_link;
        }
    }
}