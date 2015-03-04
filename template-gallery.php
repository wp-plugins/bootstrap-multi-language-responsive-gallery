<?php
/**
 * Template Name: Gallery
 * Description: A Page Template that display gallery items.
 *
 * @package Portfolio
 * @author Purplethemes
 */

get_header(); 

global $wp_query;
$post_id = $wp_query->get_queried_object_id();
$gallery_post_count = get_option( 'gallery_post_count' );
$gallery_orderby = get_option( 'gallery_order_by' );
$gallery_order = get_option( 'gallery_order' );

switch ( get_post_meta($post_id, 'Layout', true) ) {
	case 'left_sidebar':
		$class = 'left';
	    break;
	case 'right_sidebar':
		$class = 'right';
		break;
	
	default:
		$class = '';
		break;
}

 
if($class == 'left'){
  
    $right_class = 'col-xs-12 col-sm-9 col-md-9 pull-right';
    $left_class = 'col-xs-12 col-sm-3 col-md-3 pull-left';
    $class = 'left';
}
     
elseif($class == 'right'){
    
    $right_class = 'col-xs-12 col-sm-9 col-md-9';
    $left_class = 'col-xs-12 col-sm-3 col-md-3';
    $class = 'right';
}

else {
	$class = '';
}

?>

<div class="container">
    <article class="row"> 
        <section class="project-gallery">  
           <?php
                if($class) echo'<article class="' .$right_class.'">'; 
                
                                $gallery_args = array( 
                            		'post_type' => 'gallery',
                            		'posts_per_page' => ( ! empty($gallery_post_count)) ? $gallery_post_count : '8' ,
                            		'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
                            		'order' => ( ! empty($gallery_order)) ? $gallery_order : 'ASC' ,
                            		'orderby' => ( ! empty($gallery_orderby)) ? $gallery_orderby : 'date',
                                );

                                $temp = $wp_query;
                                $wp_query = null;
                                $wp_query = new WP_Query();
                                $wp_query->query( $gallery_args );
                     
                                	echo '<div class="grid">';
                                            if ($wp_query->have_posts()) {
                                             
                                                while ($wp_query->have_posts()) {
                                            	
                                                    $wp_query->the_post();
                                                    include( plugin_dir_path(__FILE__).'content-gallery.php' );
                                                }
                                                      
                                    echo '</div>';
                                    
                                    echo '<article class="col-xs-12 col-sm-12 col-md-12 text-right">';
                                    	echo '<ul class="pagination">';
                                    			 wpt_gallery_pagination();
                                    	echo '</ul>';
                					echo '</article>';
                                            } 
                    
                                $wp_query = $temp;
                                wp_reset_query(); 
                                the_post();
                                    
                	if($class)	echo '</article>';    
               
                if($class){ 
                echo '<article class="' .$left_class. '">';
               			echo'<aside>';
                            echo'<div class="sidebar">';
                                get_sidebar();
                            echo '</div>';
                        echo '</aside>';
                echo'</article>';
        
                } 
 
?>

        </section> <!--project-section end-->
    </article>
</div>

<?php  get_footer(); ?>

