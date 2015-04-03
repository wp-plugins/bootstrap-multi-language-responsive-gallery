<?php
/*---------------------------------------------------------------------------
* Gallery Custom Post type
---------------------------------------------------------------------------*/


if ( ! class_exists( 'Gallery_Post_Type' ) ) :

	class Gallery_Post_Type {

		function __construct() {

			// Runs when the plugin is activated
			register_activation_hook( __FILE__, array( &$this, 'plugin_activation' ) );

			add_action( 'admin_menu', array( $this, 'gallery_setting_admin_menu' ) );
			
			// Add support for translations
			load_plugin_textdomain( 'wpt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Adds the gallery post type and taxonomies
			add_action( 'init',array( &$this, 'plugin_activation' ) );

			// Thumbnail support for gallery posts
			add_image_size( 'cpt-logo-thumbnail',100,100 ); // Admin listing thumbnail
			add_image_size( 'gallery-listing', 555, 415, true ); // gallery - listing (2col,3col,4col)
            add_image_size( 'gallery-homepage', 240, 240, true ); // gallery - shortcode- box
	        add_image_size( 'galllery-homepagelarge', 799, 539, true ); //gallery - shortcode - lightbox (large)

			// Adds thumbnails to column view
			add_filter( 'manage_edit-gallery_columns', array( &$this, 'add_gallery_thumbnail_column'), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'display_gallery_thumbnail' ), 10, 1 );

			// Show gallery post counts in the dashboard
			add_action( 'dashboard_glance_items', array( &$this, 'add_gallery_counts' ) );

			// Give the gallery menu item a unique icon
			add_action( 'admin_head', array( &$this, 'gallery_icon' ) );
			
			add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_frontside_scripts' ), 0 );
			
			add_filter('widget_text', 'do_shortcode');

		}
		
		function plugin_frontside_scripts() {
			
		        /* included javascript section */
		        
		        wp_enqueue_script('jquery');
		        
		        wp_enqueue_script( 'jquery-bootstrap-js', GALL_PLUGIN_URL .'js/bootstrap.min.js', false, GALL_PLUGIN_VERSION, true );
		        
				wp_enqueue_script( 'jquery-gallery-colorbox', GALL_PLUGIN_URL. 'js/gallery-colorbox.js', false, GALL_PLUGIN_VERSION, true );
				
				 wp_enqueue_script( 'jquery-colorbox', GALL_PLUGIN_URL. 'js/jquery.colorbox.js', false, GALL_PLUGIN_VERSION, true );
				
		        
		        /* included javascript section end */
		        
		        /* css section  */
		        
		        wp_enqueue_style('jquery.bootstrap', GALL_PLUGIN_URL.'css/bootstrap.css', array(), GALL_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.font-awesome', GALL_PLUGIN_URL.'css/font-awesome.min.css', array(), GALL_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.gallery', GALL_PLUGIN_URL.'css/gallery.css', array(), GALL_PLUGIN_VERSION);
		        	
		        wp_enqueue_style('jquery.colorbox', GALL_PLUGIN_URL.'css/colorbox.css', array(), GALL_PLUGIN_VERSION);  
                
                /* css section end  */                    
		}

		/**---------------------------------------------------------------------------
		 * Flushes rewrite rules on plugin activation to ensure gallery posts don't 404
		 * http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
		 ---------------------------------------------------------------------------*/

		function plugin_activation() {
			$this->gallery_init();
			flush_rewrite_rules();
		}

		function gallery_init() {

			/**---------------------------------------------------------------------------
			 * Enable the Gallery custom post type
			 * http://codex.wordpress.org/Function_Reference/register_post_type
			 ---------------------------------------------------------------------------*/

			$labels = array(
				'name' => __( 'Gallery', 'wpt' ),
				'singular_name' => __( 'Gallery Item', 'wpt' ),
				'add_new' => __( 'Add New Item', 'wpt' ),
				'add_new_item' => __( 'Add New Gallery Item', 'wpt' ),
				'edit_item' => __( 'Edit Gallery Item', 'wpt' ),
				'new_item' => __( 'Add New Gallery Item', 'wpt' ),
				'view_item' => __( 'View Item', 'wpt' ),
				'search_items' => __( 'Search Gallery', 'wpt' ),
				'not_found' => __( 'No gallery items found', 'wpt' ),
				'not_found_in_trash' => __( 'No gallery items found in trash', 'wpt' )
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments','page-attributes' ),
				'capability_type' => 'post',
				'rewrite' => array("slug" => "galleries"), // Permalinks format
                'menu_position' => 5,
                'menu_icon' => 'dashicons-format-gallery',
				'has_archive' => true
			);

			$args = apply_filters('wpt_args', $args);

			register_post_type( 'gallery', $args );
            
            flush_rewrite_rules();
	

		}

		/**---------------------------------------------------------------------------
		 * Add Columns to Gallery Edit Screen
		 * http://wptheming.com/2010/07/column-edit-pages/
		 ---------------------------------------------------------------------------*/

           // Add thumbnail to custom column
           
    		function add_gallery_thumbnail_column( $gallery_columns ) {

    			$column_gallery_thumbnail = array( 'gallery_featured_image' => __('Gallery Thumbnail','wpt' ) );
    			$gallery_columns = array_slice( $gallery_columns, 0, 1, true ) + $column_gallery_thumbnail + array_slice( $gallery_columns, 1, NULL, true );
    			return $gallery_columns;
    		}

            
            function display_gallery_thumbnail($gallery_columns) {
                global $post;
                $gallery_image_thumbnail = get_the_post_thumbnail( $post->ID, 'cpt-logo-thumbnail' );
                if ($gallery_columns == 'gallery_featured_image') {
                	if(!empty($gallery_image_thumbnail)) {
                	
						echo $gallery_image_thumbnail;
					}
					else {
						echo '<img src="'.GALL_PLUGIN_URL. 'images/no-img-gallery.jpg'.'" alt="" style="width:100px;height:75px;"/>';
					}
            
                }
            }


			
        /**---------------------------------------------------------------------------
		* Added submenu setting page in menu of gallery.
		*
		* Function Name: gallery_setting_admin_menu.
		*
		*---------------------------------------------------------------------------*/
		
		function gallery_setting_admin_menu() {
							
			add_submenu_page( 'edit.php?post_type=gallery', __( 'Gallery Settings', 'wpt' ), __( 'Gallery Settings', 'wpt' ), 'manage_options', 'gallery-settings', array( $this, 'gallery_settings_page' ) );
		
		}
		
		function gallery_settings_page() {
			
			if(isset($_REQUEST['update_gallery_settings']))
			{
				if ( !isset($_POST['wpt_gallery_nonce']) || !wp_verify_nonce($_POST['wpt_gallery_nonce'],'gallery_general_setting') )
				{
				    _e('Sorry, your nonce did not verify.', 'wpt');
				    exit;
				} 
				
				else
				{
					$gallery_title= !empty($_POST['gallery_title']) ? $_POST['gallery_title'] : 'Portfolio';
				  	update_option('gallery_title',$gallery_title);
				  	
				  	$gallery_content = !empty($_POST['gallery_content']) ? $_POST['gallery_content'] : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
				  	update_option('gallery_content',$gallery_content);
				  	
				  	$gallery_post_count = !empty($_POST['gallery_post_count']) ? $_POST['gallery_post_count'] : '8';
				  	update_option('gallery_post_count',$gallery_post_count);
				  	
				  	$gallery_layout = !empty($_POST['gallery_layout']) ? $_POST['gallery_layout'] : '2col';
				  	update_option('gallery_layout', $gallery_layout);
				  	
				  	$gallery_order_by= !empty($_POST['gallery_order_by']) ? $_POST['gallery_order_by'] : 'title';
				    update_option('gallery_order_by',$gallery_order_by);
				    
				    $gallery_order= !empty($_POST['gallery_order']) ? $_POST['gallery_order'] : 'ASC';
				    update_option('gallery_order',$gallery_order);
				    
				}
			}
			
			
			?>
			
			<form id="gallery-setting" method="post" action="" enctype="multipart/form-data" >
			
				<h2 style='margin-bottom: 10px;' ><?php _e( 'Gallery General Settings', 'wpt' ); ?></h2>
					
					<table id="gallery-table" cellpadding="20">
					 
					 	<tr>
					 	<?php
					 	$check_gallery_layout = get_option('gallery_layout');
						$default='';
						if(isset($check_gallery_layout)){
							if($check_gallery_layout == ''){
								$default='checked';
							}
						}
						else
						 $default='checked';
					 	?>
						 	<th><?php _e('Gallery Page Layout','wpt'); ?> <br/>
						 		<i><?php _e('(Layout for gallery items list. Choose between 2, 3 or 4 column layout)', 'wpt'); ?></i>
						 	</th>
						 	
						 	<td>
								<input type="radio" name="gallery_layout" value="2col" <?php if (isset ($check_gallery_layout ) ) checked($check_gallery_layout, '2col' ); ?> <?php echo $default;?>/>
								<img name="gallery_layout" src="<?php echo GALL_PLUGIN_URL. 'images/two-column.png'; ?>">
							  	
							  	<input type="radio" name="gallery_layout" value="3col" <?php if (isset ($check_gallery_layout ) ) checked($check_gallery_layout, '3col' ); ?> />      
								<img name="gallery_layout" src="<?php echo GALL_PLUGIN_URL. 'images/three-column.png'; ?>">
							  	
							  	<input type="radio" name="gallery_layout" value="4col" <?php if (isset ($check_gallery_layout ) ) checked($check_gallery_layout, '4col' ); ?> />
								<img name="gallery_layout" src="<?php echo GALL_PLUGIN_URL. 'images/four-column.png'; ?>">
	  						</td>
	  						
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_gallery_post_number = get_option('gallery_post_count');
					 	$gallery_post_count = !empty($check_gallery_post_number) ? $check_gallery_post_number : '8';
					 	?>
					 		<th><?php _e('Number of Posts', 'wpt'); ?><br/>
					 			<i><?php _e('(Specify the number of post to be displayed per page)', 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<input type="text" id="gallery_post_count" name="gallery_post_count" value="<?php _e($gallery_post_count, 'wpt'); ?>" /><br/><br/>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 		<th><?php _e('Order By', 'wpt'); ?><br/>
					 			<i><?php _e('(Gallery item order by column )', 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<select name="gallery_order_by">
									<?php
										$wpt_curr_sel_orderby_val=get_option('gallery_order_by');
										$wpt_orderby=array('menu_order'=>'Manual order','date'=>'Date', 'title'=>'Title');
										foreach($wpt_orderby as $wpt_k=>$wpt_v){?>
												<option value="<?php _e($wpt_k,'wpt');?>" <?php selected( $wpt_curr_sel_orderby_val,$wpt_k ,$echo = true);?>><?php _e($wpt_v,'wpt');?></option>										
									<?php } ?>
								
								</select>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 		<th><?php _e('Order', 'wpt'); ?><br/>
					 			<i><?php _e('(Gallery items order)' , 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<select name="gallery_order">
									<?php
										$wpt_curr_sel_order_val=get_option('gallery_order');
										$wpt_order = array('ASC' => 'Ascending','DESC' => 'Descening');
										foreach($wpt_order as $wpt_k=>$wpt_v){?>
												<option value="<?php _e($wpt_k,'wpt');?>" <?php selected( $wpt_curr_sel_order_val,$wpt_k ,$echo = true);?>><?php _e($wpt_v,'wpt');?></option>										
									<?php } ?>
									
								</select>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 		<th>
					 			<h2><?php _e('To display Title and Content for Gallery Shortcode','wpt'); ?></h2>
					 		</th>
					 		
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_gallery_title = get_option('gallery_title');
					 	$gallery_title = !empty($check_gallery_title) ? $check_gallery_title : 'Gallery';
					 	?>
					 		<th><?php _e('Title  :','wpt');?><br/>
					 			<i><?php _e('(Specify the title to be displayed)', 'wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<input type="text" id="gallery_title" name="gallery_title" value="<?php _e( $gallery_title, 'wpt'); ?>" /><br/><br/>
					 		</td>
					 		
					 	</tr>
					 	
					 	<tr>
					 	<?php
					 	$check_gallery_content = get_option('gallery_content');
					 	$gallery_content = !empty($check_gallery_content) ? $check_gallery_content : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
					 	$gallery_content =  stripslashes ( $gallery_content );
					 	?>
					 		<th><?php _e('Content :', 'wpt'); ?><br/>
					 			<i><?php _e('(Specify the content to be displayed)','wpt'); ?></i>
					 		</th>
					 		
					 		<td>
					 			<textarea rows="7" cols="49" style="resize:none" id="gallery_content" name="gallery_content" ><?php _e($gallery_content,'wpt'); ?>									
					 			</textarea><br/><br/>
					 		</td>
					 		
					 	</tr>
					 
					</table>
					 
					<?php wp_nonce_field( 'gallery_general_setting', 'wpt_gallery_nonce' ); ?>
				    <p class="submit">
				        <input id="wpt-submit" class="button-primary" type="submit" name="update_gallery_settings" value="<?php _e( 'Save Settings', 'wpt' ) ?>" />
				    </p> 
				    
				     <tr>
				    
				    	<td colspan="3" align="center">
				    	<p><strong><?php _e('Note:','wpt'); ?></strong></p>
				    		<p><?php _e('You can add the gallery shortcode using [gallery] in any page having template other than gallery template.','wpt'); ?></p>
				    		<p><?php _e('Attributes such as count, orderby and order can be passed in the shortcode.','wpt'); ?></p>
				    		<p><?php _e('Eg: [gallery count="2" orderby ="title" order="asc" ]','wpt'); ?></p>
				    	</td>
				    </tr>
			
			</form>
			
		<?php }
		
		
			/**---------------------------------------------------------------------------
			 * Add Gallery count to "Right Now" Dashboard Widget
			 ---------------------------------------------------------------------------*/

		    function add_gallery_counts() {
			if ( ! post_type_exists( 'gallery' ) ) {
				return;
			}

			$num_posts = wp_count_posts( 'gallery' );
			$num = number_format_i18n( $num_posts->publish );
			$text = _n( 'Gallery Item', 'Gallery Items', intval($num_posts->publish) );
			if ( current_user_can( 'edit_posts' ) ) {
				
				$output = "<a href='edit.php?post_type=gallery'>$num $text</a>";
				
			}
			echo '<li class="post-count gallery-count">' . $output . '</li>';

			if ($num_posts->pending > 0) {
				$num = number_format_i18n( $num_posts->pending );
				$text = _n( 'Gallery Item Pending', 'Gallery Items Pending', intval($num_posts->pending) );
				if ( current_user_can( 'edit_posts' ) ) {
					$num = "<a href='edit.php?post_status=pending&post_type=gallery'>$num</a>";
				}
				echo '<li class="post-count gallery-count">' . $output . '</li>';
			}
		}

    		/**---------------------------------------------------------------------------
    		 * Displays the custom post type icon in the dashboard
    		 ---------------------------------------------------------------------------*/

		    function gallery_icon() { ?>
            	<style type="text/css" media="screen">
           			.gallery-count a:before{content:"\f128"!important}
        		</style>
			<?php }
		
	}

	new Gallery_Post_Type;

endif;

?>