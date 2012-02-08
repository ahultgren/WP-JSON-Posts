<?php
/*
Plugin Name: WP JSON Posts
Plugin URI: http://andreashultgren.se/
Description: My foundation for all other AJAX-based plugins. Echoes posts as JSON instead of using the template. Works for the front page, static pages, single posts, custom post types, archives etc.
Version: 1.0.3/11.1229
Author: Andreas Hultgren
Author URI: http://andreashultgren.se/
License: GPL3
*/
/*
TODO:
	Is it possible to prevent featuring of the plugin in Wordpress when loaded by another pluin?
	Alternatively detect if the plugin already is active and prevent multiple excecution?
		E.g if( function_exists('wp_jsonposts_ti') )
	Documentation according to phpDOC
	Add possibility to choose whether or not the built in cf's should be returned
*/
/**
 * Changelog
 * 
 * 1.0.2/11.1108 Added support for custom taxonomies. the_category is DEPRECATED in favor of the_terms['category'].
 * 1.0.3/11.1229 Added support for custom fields. 
*/
add_filter( 'template_include', 'wp_jsonposts_ti' );
function wp_jsonposts_ti( $template ){
	// Send &moreposts as a GET request to enable json return of post data
	if( isset($_GET['jsonposts']) ){
		global $query_string;
		global $post;
		
		// Query number of posts and number of existing posts. Send &posts_per_page and &offset as a GET request
		$my_query_string = $query_string 
			. '&posts_per_page=' . (isset($_GET['posts_per_page']) ? htmlspecialchars($_GET['posts_per_page']) : 10)
			. (isset($_GET['offset']) ? '&offset=' . htmlspecialchars($_GET['offset']) : '');

		query_posts($my_query_string);
		
		// Store the data in an array that automagically will be converted to json
		$json = array();
		if( have_posts() ):
			while(have_posts()): the_post();
				$the_post = array(
					'ID' => $post->ID,
					'the_title' => get_the_title(),
					'the_content' => wjp_get_the_content_formatted(),
					'the_excerpt' => get_the_excerpt(),
					'the_date' => get_the_date('Y-m-d H:i'),
					'the_permalink' => get_permalink(),
					'the_post_thumbnail' => wp_get_attachment_image_src(
						get_post_thumbnail_id($post->ID), 
						(isset($_GET['imgsize']) ? htmlspecialchars($_GET['imgsize']) : 'small' )
					),
					'the_category' => get_the_category()
				);
				
				// the_category needs some speical treatment. Why wordpress, WHY?! aren't the url included in the category object?!
				for( $i = 0, $l = count($the_post['the_category']); $i < $l; $i++ ){
					$the_post['the_category'][$i]->cat_url = get_category_link($the_post['the_category'][$i]->cat_ID);
				}
				
				// If the user has specified one or multiple taxonomies the terms will be included in the returned data.
				// This only accepts the taxonomy name.
				if( isset($_GET['the_terms']) ){
					$wanted_taxs = explode('-',htmlspecialchars($_GET['the_terms']));
					
					// Some fulhack to remove the id-key of the taxonomy that rendered the information useless in jSON
					foreach( $wanted_taxs as $tax ){
						$terms = get_the_terms(get_the_ID(), $tax);
						$i = 0;
						
						if( $terms && count($terms) > 0 ){
							foreach( $terms as $term ){
								foreach( $term as $key => $term_property ) {
									$the_terms[$tax][$i][$key] = $term_property;
								}
								$the_terms[$tax][$i]['tax_url'] = get_term_link(0 + $the_terms[$tax][$i]['term_id'], $tax);
								
								$i++;
							}
						}
					}
					$the_post['the_terms'] = $the_terms;
				}
				
				// Add custom fields
				$cfs = get_post_custom();
				// Fulhack to remove built in cf's
				//## Add possibility to choose whether or not the built in cf's should be returned
				foreach( $cfs as $key => $value ){
					if( substr($key, 0, 1) === '_' ){
						unset($cfs[$key]);
					}
				}
				if( count($cfs) ){
					$the_post['custom_fields'] = $cfs;
				}
				
				// Remove stuff that the user want excluded
				// Send a dash-separated string named &exclude as a GET request
				// Preferably done using Array.join('-')
				if( isset($_GET['exclude']) ){
					$excludes = explode('-', $_GET['exclude']);
					foreach( $excludes as $exclude ){
						if( array_key_exists(htmlspecialchars($exclude), $the_post) ){
							unset($the_post[htmlspecialchars($exclude)]);
						}
					}
				}
				
				// The magic
				$json[] = $the_post;
			endwhile;
		endif;
		
		// Print the JSON
		echo json_encode($json);
		
		// Stop the template from printing
		return false;
	}
	else {
		return $template;
	}
}

if( !function_exists('wjp_get_the_content_formatted') ){
	function wjp_get_the_content_formatted ($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
		$content = get_the_content($more_link_text, $stripteaser, $more_file);
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
}