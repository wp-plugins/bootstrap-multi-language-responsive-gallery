<?php

add_shortcode( 'gallery', 'galleryShortcode' );

function galleryShortcode( $attr, $content = null )
{
    extract(shortcode_atts(array(
		'count' => '4',
		'orderby' => 'menu_order',
		'order' => 'DESC',
	), $attr));
	
	$args = array( 
    
		'post_type' => 'gallery',
		'posts_per_page' => intval($count),
		'paged' => -1,
        'post_status'=> 'publish',
		'orderby' => $orderby,
		'order' => $order,	
		'ignore_sticky_posts' =>1,
	);
	  
    $query = new WP_Query($args);
    if ($query->have_posts())
	{ 
        $gallery_title = get_option('gallery_title');
         if( empty( $gallery_title ) )
           $gallery_title = 'Gallery';
           
	    $gallery_content = get_option( 'gallery_content' );

	    $html = '';
		$html .='<section class="recent_gallery clearfix">';		
		$html .='<div class="title-area">';	
		$html .='<h2 class="section-title">'. $gallery_title. '</h2>';
		$html .='<div class="section-divider divider-inside-top"></div>';
		$html .='<p class="section-sub-text">'.$gallery_content. '</p>';
		$html .='</div>';
        $html .='<article class="gallery-list">';
        $html .='<section class="row">';
        
        while ($query->have_posts()) 
		{
                    $query->the_post();
                    $term_count = '';
                    
                   	$gallery_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'gallery-homepage');
                    $large_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'gallery-homepagelarge');
                    $gallery_imgURL = $gallery_imgArray[0];
                    $large_imgURL = $large_imgArray[0];
                    $full_title =  get_the_title();
                    $html .='<article class="col-xs-6 col-sm-3 col-md-3">';
                    $html .='<div class="gallery-container">';
                    $html .='<div class="gallery-img">';
                    
                    if(!empty($gallery_imgURL)) $html .='<img src="'  .$gallery_imgURL. '" class="img-responsive" alt="'. __('Project Name','wpt'). '" title="'. __('Project Name','wpt'). '" />';
                    else $html .='<img src="' .GALL_PLUGIN_URL. 'images/no-img-gallery-240x240.jpg'. '" class="img-responsive" alt="'. __('Project Name','wpt'). '" title="'. __('Project Name','wpt'). '" />';
                   
                    
                    if(!empty($large_imgURL)) $html .='<div class="gallery-entry-hover"> <a class="gallery" href="' .$large_imgURL. '" title="'. $full_title. '"><i class="glyphicon glyphicon-zoom-in"></i></a>';
                    else $html .='<div class="gallery-entry-hover"> <a class="gallery" href="' .GALL_PLUGIN_URL. 'images/no-img-gallery.jpg'. '" title="'. $full_title. '"><i class="glyphicon glyphicon-zoom-in"></i></a>';
                    
                    $html .='</div>';
                    $html .='</div>';
                    $html .='<h4>' .the_title(false, false, false). '</h4>';
                   
                    $html .='</div>';
                    $html .='</article>';
				} 
		$html .='</section>';
        $html .='</article>';
          
	}
    
	wp_reset_query();
	

	$html .='</section>';
		
    return $html;
	
}
?>