<?php
/*
Plugin Name: Bootstrap Multi-language Responsive Gallery

Plugin URI: http://www.augustinfotech.com

Description: Bootstrap Multi-language Responsive Gallery is a simple WordPress plugin to display gallery on your website.

Version: 1.0

Text Domain: wpt

Author: August Infotech

Author URI: http://www.augustinfotech.com
*/

define( 'GALL_PLUGIN_URL', 			plugin_dir_url( __FILE__) );
define( 'GALL_PLUGIN_PATH',			plugin_dir_path( __FILE__ ) );
define( 'GALL_PLUGIN_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'GALL_PLUGIN_VERSION', 	    '1.0' );

/* ---------------------------------------------------------------------------
 * Load the plugin required files
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'gallery_plugin_load_function' ) ) :
function gallery_plugin_load_function() {
	
	global $post;
	
	require_once( 'wpt-posttype-gallery.php' );
	require_once( 'gallery-shortcode.php' );
	
    // Add a filter to the attributes metabox to inject template into the cache.
    add_filter(
		'page_attributes_dropdown_pages_args','wpt_register_gallery_project_templates'
	);


    // Add a filter to the save post to inject out template into the page cache
    add_filter(
		'wp_insert_post_data', 
		'wpt_register_gallery_project_templates' 
	);

    	
	// Add a filter to the template include to determine if the page has our 
	// template assigned and return it's path
    add_filter(
		'template_include', 
		'wpt_view_gallery_project_template'
	);
	
}
endif; // gallery_plugin_load_function
add_action( 'plugins_loaded','gallery_plugin_load_function' );


/* ---------------------------------------------------------------------------
 * Required fucntions and hooks for Plugin
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'ai_plugin_rate_us' ) ) : 
function ai_plugin_rate_us( $footer_text ) {
	global $typenow;

	if ( $typenow == 'gallery' ) {
		$rate_text = sprintf( __( 'Thank you for using Bootstrap Multi-language Responsive Gallery ! Please <a href="%1$s" target="_blank">rate us</a> on <a href="%1$s" target="_blank">WordPress.org</a> and <a href="%2$s" target="_blank">Like us</a> on <a href="%2$s" target="_blank">Facebook</a> to stay with us.', 'wpt' ),
			'https://wordpress.org/support/view/plugin-reviews/bootstrap-multi-language-responsive-gallery?filter=5#postform',
			'https://www.facebook.com/August.Infotech'
		);

		return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
	} else {
		return $footer_text;
	}
}
endif; // ai_plugin_rate_us

 
if ( ! function_exists( 'wpt_register_gallery_project_templates' ) ) :
function wpt_register_gallery_project_templates( $atts ) {
	

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();

        if ( empty( $templates ) ) {
                $templates = array();
        } 
        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');
        
        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, array('template-gallery.php'     => 'Gallery'));
        
        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

} 
endif; //wpt_register_gallery_project_templates

/**
 * Checks if the template is assigned to the page
 */
 if ( ! function_exists( 'wpt_view_gallery_project_template' ) ) :
 function wpt_view_gallery_project_template( $template ) {

        global $post;
        
        $filename = get_post_meta( 
			$post->ID, '_wp_page_template', true 
		);
		
        if( !empty($filename) ){
			
	        $file = plugin_dir_path(__FILE__).$filename;
	      
	        // Just to be safe, we check if the file exist first
	        if( file_exists( $file ) ) {
	                return $file;
	        } 
       }
        
        return $template;

} 
endif; //wpt_view_gallery_project_template

      	
/* ---------------------------------------------------------------------------
 * Activate Hook Plugin
 * --------------------------------------------------------------------------- */

if ( function_exists('register_activation_hook') )
register_activation_hook(__FILE__,'gallery_plugin_activate');


if ( ! function_exists( 'gallery_plugin_activate' ) ) :

function gallery_plugin_activate() {
	add_option('gallery_title','Gallery','', 'yes');
	add_option('gallery_content','Lorem ipsum dolor sit amet, consectetur adipiscing elit.','', 'yes');
	add_option('gallery_post_count','8','', 'yes');
	add_option('gallery_layout','2col','', 'yes');	
	add_option('gallery_order_by','title','', 'yes');
	add_option('gallery_order', 'ASC', '', 'yes');
}
endif; //gallery_plugin_activate

/* ---------------------------------------------------------------------------
 * Uninstall Hook Plugin
 * --------------------------------------------------------------------------- */


if ( function_exists('register_uninstall_hook') )
register_uninstall_hook(__FILE__,'wpt_gallery_plugin_droped'); 

if ( ! function_exists( 'wpt_gallery_plugin_droped' ) ) :

function wpt_gallery_plugin_droped() { 

	delete_option('gallery_title','Gallery','', 'yes');
	delete_option('gallery_content','Lorem ipsum dolor sit amet, consectetur adipiscing elit.','', 'yes');
	delete_option('gallery_post_count','8','', 'yes');
	delete_option('gallery_layout','2col','', 'yes');	
	delete_option('gallery_order_by','title','', 'yes');
	delete_option('gallery_order', 'ASC', '', 'yes');

}
endif; //wpt_gallery_plugin_droped


/* ---------------------------------------------------------------------------
 * Pagination functions Plugin
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'wpt_gallery_pagination' ) ):  
function wpt_gallery_pagination() {
   
	global $wp_query;
	
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;  
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	if( empty( $paged ) ) $paged = 1;
	$prev = $paged - 1;							
	$next = $paged + 1;
	
	$end_size = 1;
	$mid_size = 2;
	$show_all = true;
	$dots = false;	
	if( ! $total = $wp_query->max_num_pages ) $total = 1;
	
	if( $total > 1 )
	{
		
		if( $paged >1 ){
			echo '<li><a class="prev_page" href="'. get_pagenum_link($current-1) .'">'. __('&lsaquo;','wpt') .'</a></li>';
		}

		for( $i=1; $i <= $total; $i++ ){
			
			if ( $i == $current ){
				echo '<li class="active">';
					echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				echo '</li>';
				$dots = true;
			} else {
				if ( $show_all || ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total - $end_size ) ){
					echo '<li>';
					   echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				    echo '</li>';
					$dots = true;
				} elseif ( $dots && ! $show_all ) {
					echo '<span class="page">...</span>&nbsp;';
					$dots = false;
				}
			}
		}
		
		if( $paged < $total ){
			echo '<li><a class="next_page" href="'. get_pagenum_link($page+1) .'">'. __('&rsaquo;','wpt') .'</a></li>';
		}

	}	
}
endif; //wpt_gallery_pagination
?>