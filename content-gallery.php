<?php
/**
 * The template for displaying content in the template-gallery.php template
 *
 * @package Portfolio
 * @author Purplethemes
 */

$layout = get_option('gallery_layout');
$cnt = 0;
switch($layout){
    case '2col':
		$layout_class = 'col-xs-6 col-sm-6 col-md-6';
		$cnt = 1;
        break;
	case '3col':
		$layout_class = 'col-xs-6 col-sm-4 col-md-4';
		$cnt = 2;
		break;
	case '4col':
		$layout_class = 'col-xs-6 col-sm-3 col-md-3';
		$cnt = 3;
		break;
    default :
        $layout_class = '';
		break;
}

$gallery_listing_full_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full');
$gallery_listing_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'gallery-listing');
$gallery_listing_imgURL = $gallery_listing_imgArray[0];
$full_title =  get_the_title();

?>

<article class="<?php echo $layout_class; ?>">
    <div class="plan-container">
        <div class="plan-img">
        <?php if(!empty($gallery_listing_imgURL)) { ?>
        	<img title="<?php echo $full_title;  ?>" alt="<?php _e('Plan Name', 'wpt'); ?>" class="img-responsive" src="<?php  echo $gallery_listing_imgURL; ?>">
        <?php } else { 
        		if($cnt == 1){ ?>
					<img title="<?php echo $full_title;  ?>" alt="<?php _e('Plan Name', 'wpt'); ?>" class="img-responsive" src="<?php  echo GALL_PLUGIN_URL. 'images/no-img-gallery-555x415.jpg'; ?>" >
				<?php } else if($cnt == 2){ ?>
					<img title="<?php echo $full_title;  ?>" alt="<?php _e('Plan Name', 'wpt'); ?>" class="img-responsive" src="<?php  echo GALL_PLUGIN_URL. 'images/no-img-gallery-555x415.jpg'; ?>" >
				<?php } else if($cnt == 3){ ?>
					<img title="<?php echo $full_title;  ?>" alt="<?php _e('Plan Name', 'wpt'); ?>" class="img-responsive" src="<?php  echo GALL_PLUGIN_URL. 'images/no-img-gallery-555x415.jpg'; ?>">
				<?php } ?>
       	
		<?php } ?>
            
            <div class="plan-entry-hover">
            <?php if(!empty($gallery_listing_full_imgArray)) { ?>
                <a class="gallery" href="<?php echo $gallery_listing_full_imgArray[0]; ?>" title="<?php echo $full_title; ?><br/><span class='lb-desc'></span>"><i class="glyphicon glyphicon-zoom-in"></i></a>
            <?php } else { ?>
				<a class="gallery" href="<?php echo GALL_PLUGIN_URL. 'images/no-img-gallery.jpg'; ?>" title="<?php echo $full_title; ?><br/><span class='lb-desc'><?php echo the_content(); ?></span>"><i class="glyphicon glyphicon-zoom-in"></i></a>
			<?php } ?>    
                
            </div>
        </div>
    </div>
</article>

 