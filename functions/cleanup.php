<?php

// Fire all our initial functions at the start
add_action('after_setup_theme','theme_namespace_start', 16);

function theme_namespace_start() {

    // launching operation cleanup
    add_action('init', 'theme_namespace_head_cleanup');
    
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'theme_namespace_remove_wp_widget_recent_comments_style', 1 );
    
    // clean up comment styles in the head
    add_action('wp_head', 'theme_namespace_remove_recent_comments_style', 1);
    
    // clean up gallery output in wp
    add_filter('gallery_style', 'theme_namespace_gallery_style');
    
    // adding sidebars to Wordpress
    add_action( 'widgets_init', 'theme_namespace_register_sidebars' );
    
    // cleaning up excerpt
    add_filter('excerpt_more', 'theme_namespace_excerpt_more');

} /* end joints start */

//The default wordpress head is a mess. Let's clean it up by removing all the junk we don't need.
function theme_namespace_head_cleanup() {
	// Remove category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// Remove post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// Remove EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// Remove Windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// Remove index link
	remove_action( 'wp_head', 'index_rel_link' );
	// Remove previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// Remove start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// Remove links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// Remove WP version
	remove_action( 'wp_head', 'wp_generator' );
} /* end Joints head cleanup */

// Remove injected CSS for recent comments widget
function theme_namespace_remove_wp_widget_recent_comments_style() {
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );
   }
}

// Remove injected CSS from recent comments widget
function theme_namespace_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// Remove injected CSS from gallery
function theme_namespace_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

// This removes the annoying […] to a Read More link
function theme_namespace_excerpt_more($more) {
	global $post;
	// edit here if you like
return '<a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __('Read', 'theme_namespace') . get_the_title($post->ID).'">'. __('... Read more &raquo;', 'theme_namespace') .'</a>';
}

////**** NEED TO REVIEW ****////
//This is a modified the_author_posts_link() which just returns the link. This is necessary to allow usage of the usual l10n process with printf()
function theme_namespace_get_the_author_posts_link() {
	global $authordata;
	if ( !is_object( $authordata ) )
		return false;
	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s', 'theme_namespace' ), get_the_author() ) ), // No further l10n needed, core will take care of this one
		get_the_author()
	);
	return $link;
}