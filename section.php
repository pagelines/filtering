<?php
/*
	Section: Filtering
	Author: elSue
	Author URI: http://www.elsue.com
	Description: Filter your posts or custom post types by category, tag or custom taxonomy
	Class Name: Filtering
	Cloning: false
	Workswith: content, template, main
	Failswith: archive, tag, category, author
	PageLines: true
	v3: true
	Filter: component
	Version: 1.7.2
	Demo: http://pagelines.ellenjanemoore.com/filtering-demo/
	
*/

/**
 * Filtering Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class Filtering extends PageLinesSection {
		

	/**
	* Load js
	*/
	const version = '1.7.2';

function section_styles(){
		
		wp_enqueue_script( 'isotope', $this->base_url.'/js/jquery.isotope.min.js', array( 'jquery' ), self::version, true);
		wp_enqueue_script( 'filtering', $this->base_url.'/js/filtering.js',array( 'jquery' ), self::version, true);
		wp_enqueue_script( 'equalize', $this->base_url.'/js/equalizecols.js',array( 'jquery' ), self::version, true);
		wp_enqueue_script( 'easing', $this->base_url.'/js/jquery.easing.js',array( 'jquery' ), self::version, true);
		
		}


	function section_head() {
		if(function_exists('pl_has_editor')){
				$filtering_clone = '#filtering'.$this->get_the_id();
			} else {
			   	$filtering_clone = '#filtering'.$this->oset['clone_id'];				
			}
        
	
		?>
		<script>
		
		jQuery(document).ready(function(){

			var mycontainer = jQuery('<?php echo $filtering_clone ?> .filtering');
			 mycontainer.isotope({
     			itemSelector: '.item',
     			layoutMode: 'masonry',
     			

     			
     			
  		}).imagesLoaded( function() {
  			jQuery(".filtering-image").equalizeCols();
			jQuery(".item-info").equalizeCols();
    	// trigger again after images have loaded
    		mycontainer.isotope('reLayout');
  		});
		

	   // filter items when filter link is clicked
	jQuery('<?php echo $filtering_clone ?> .options a').click(function(){
	  var selector = jQuery(this).attr('data-filter');
	  mycontainer.isotope({ filter: selector });
	    
	  var optionSet=jQuery(this).parents(".options");
		if(jQuery(this).hasClass("selected")){
			return false}
			jQuery(optionSet).find(".selected").removeClass("selected");
			jQuery(this).addClass("selected");
		return false;
	  });	  
			
        
	   // filter items when filter link is clicked
	jQuery('<?php echo $filtering_clone ?> select.select').change(function(){
		var filters = jQuery(this).val();

        mycontainer.isotope({
            filter: filters
        });
	   
	 }); 
			
			jQuery('body').addClass('filtering-section');
			// Get image height to vertically align image to bottom
			jQuery('.filtering-image').each(function() {
	        	var container_height = jQuery(this).height()+'px';
	        	jQuery(this).css('line-height', container_height);
	        	
    	});
			

		
});
		
</script>
<?php

		// Add frame around image if checked
		if($this->opt( 'filtering_thumb_frame' )) {
		?>
		<script>
	 	jQuery(document).ready(function() {
			jQuery('.filtering-image').addClass('pl-imageframe');

		});
		</script>
		<?php
		}
		
		$filtering_menu = ( $this->opt( 'filtering_menu' ) ) ? $this->opt( 'filtering_menu' ) : 'horizontal';
      
		if($filtering_menu !='horizontal') {
		?>
		<script>
	 	jQuery(document).ready(function() {
			jQuery('.filtering-nav-option').removeClass('visible-phone');
			jQuery('.filtering-nav-wrap').addClass('hidden').removeClass('hidden-phone');

		});
		</script>
		<?php
		}
		if($this->opt( 'filtering_mobile' )) {
		?>
		<script>
	 	jQuery(document).ready(function() {
			jQuery('.filtering-nav-option').removeClass('visible-phone').addClass('hidden');
			jQuery('.filtering-nav-wrap').removeClass('hidden-phone');

		});
		</script>
		<?php
		}
	}
	function section_persistent() {

		if(function_exists('pl_has_editor')){
			
			return;

		} else {
		   add_action( 'pre_get_posts', array(&$this, 'set_per_page'), 1 );
		   }
		
	 	

	}
	


	function set_per_page( $query ) {
    	
    	
    // Fixes pagination issue on archive pages
		global $wp_query;
		
		

	if($query->is_page()&&($query === $wp_query)) {
		return $query;
	}else {
		if ($this->opt( 'filtering_override_home')) {	
	    	if($query->is_home()&&($query === $wp_query)){
	    		$query->set( 'posts_per_page',  $this->opt( 'filtering_number'));
	    		return $query;

	    	}
		}
		if ($this->opt( 'filtering_override_archive')) {	
	    	if($query->is_post_type_archive()&&($query === $wp_query)){
	    		$query->set( 'posts_per_page',  $this->opt( 'filtering_number'));
	    		return $query;
	    	}
	    	if($query->is_tax()&&($query === $wp_query)){
	    		$query->set( 'posts_per_page',  $this->opt( 'filtering_number'));
	    		return $query;
	    	}
		}
	}

	
}
	function section_opts(){
		
		
		$wp_per_page = get_option('posts_per_page');
		$post_type_array = array();
		if(post_type_exists('boxes')) {
		// Builtin types needed.
			$builtin = array(
			'post',
			'boxes',
			
			);
		} else {
			$builtin = array(
			'post',
			
			
			);
		}	
			// All CPTs except builtins
			$cpts = get_post_types( array(
			'public'   => true,
			'_builtin' => false
			) );
			
			// Merge Builtin types and 'important' CPTs to resulting array to use as argument.
			$post_types = array_merge($builtin, $cpts);
		
			if(!empty($post_types)){
	
					foreach($post_types as $post_type){
	
						$post_type_array[$post_type] = array(
							'name' => $post_type,
							'label' => $post_type
						);
					}
				}

				// Get Taxonomies

				// Exclude some Pagelines Taxonomies
				$exclude_taxonomies = array(
	    			
	    			'banner-sets',
	    			'feature-sets',
	    			'accordion-sets',

				);
				// Builtin types needed.
				$builtin = array(
				    'category',
				    'post_tag'
				    
				);
				// All Taxonomies.
				$taxs = get_taxonomies( array(
				    'public'   => true,
				    '_builtin' => false
				) );
				// remove Excluded Taxonomies from All Taxonomies.
				foreach($exclude_taxonomies as $exclude_taxonomy)
				    unset($taxs[$exclude_taxonomy]);
				// Merge Builtin types and 'important' Taxonomies to resulting array to use as argument.
				$taxonomies = array_merge($builtin, $taxs);	
	
			$taxonomies_array = array();	
	
				if  ($taxonomies) {
  				foreach ($taxonomies  as $taxonomy ) {
    				$taxonomy_array[$taxonomy] = array(
 
    						'name' => $taxonomy,
							'label' => $taxonomy
						);
					}		
				}
			$options = array();	
		
				$options[] = array(
		            'key'           => 'filtering_setup',

					'type'		=> 'multi', 
					'title'		=> __('Filtering Setup Options', 'filtering'), 
					
					'opts'=> array(
						array(	
							'key' 			=> 'filtering_post_type',
							'default'		=> 'post',
							'type' 			=> 'select',
							'opts' 			=> $post_type_array,
							'label'	=> __( 'Select your post type to filter. Default is "post."', 'filtering'),
						), 
						array(
							'key'			=>	'filtering_taxonomy',
							'default'		=> 'category',
							'type' 			=> 'select',
							'opts' => $taxonomy_array,
							'label'	=> __( 'Select taxonomy. Make sure the taxonomy goes with the post type, i.e. category with posts. Default is "category"', 'filtering'),
						), 
					  ), 
					);
					$options[] = array(
					 'key'		=> 'filtering_terms_options',
					'type'		=> 'multi', 
					'title'		=> __('Enter Categories, Terms or Tags to Exclude or Include', 'filtering'), 
					'help'	=> __('Use this area to either Exclude or Include Categories, Terms or Tags (Not both). If your category or term has children you can exclude them, the default is to show category/term children.', 'filtering'),
					
					'opts'	=> array(
						array(
							'key'			=> 'filtering_terms',
							'default'		=> '',
							'type' 			=> 'text',
							'label'	=> __( 'Enter Categories, Terms or Tags  ( if multiple, separate using a comma )', 'filtering'),				

						),

						 array(
						 	'key'			=> 'filtering_terms_type',
							'type' 		=> 'select',
							'default'	=> 'exclude',
							'opts'	=> array(
										'exclude'	=> array('name' => __( 'Exclude (Default)', 'filtering') ), 
										'include'	=> array('name' => __( "Include", 'filtering') ),
										), 
							'label' => __( 'Exclude or Include these categories, terms or tags? Default is "Exclude."', 'filtering'),				
							),
							
						array(
							'key'			=> 'filtering_children',
							'default'		=>	null,
							'type' 			=> 'check',
							'size'			=> 'small',
							'label' 		=> __( 'Exclude Child Categories/Terms?', 'filtering'),
								),
					),
				);	
				$options[] = array(
					'key'		=> 'filtering_navigation',
					'type'		=> 'multi', 
					'title'		=> __('Filtering Navigation Options', 'filtering'), 
					
					'opts'	=> array(
						array(
								'key'			=> 'filtering_menu',
								'type'			=> 'select',
								'default'		=> 'horizontal',
								'label'	=> 'Menu Type on Desktop/Tablets (Default is Horizontal)',
								'opts' => array(
									'horizontal' 		=> array('name' => __( 'Horizontal (default)', 'filtering') ),
									'select' 			=> array('name' => __( 'Dropdown/Select', 'filtering') ),
														
								)
							),
						array(
									'key'			=> 'filtering_mobile',
									'default'		=>	null,
									'type' 			=> 'check',
									
									'label' 		=> __( 'Turn off Mobile Select Menu?', 'filtering'),
								),
						),
					);
					
					$options[] = array(	
					'key'		=> 'filtering_display',
					'type'		=> 'multi', 
					'title'		=> __('Filtering Display Options', 'filtering'), 
					
					'opts'	=> array(	
						array(
							'key'			=> 'filtering_item_width',
						
							'type' 			=> 'text',
							'default'		=> '250px',
							
							'label' 		=> __( 'Width of  each item. Default is "250px". Enter just the width value, px will be added for you.' , 'filtering'),
						),
						array(
							'key'			=> 'filtering_all_phrase',
							'type' 			=> 'text',
							'default'		=> __('Show All'),
							
							'label' 		=> __( 'Word/Phrase to use for All Items (Default is "Show All")' , 'filtering'),
						),
						
						
						array(
								'key'			=> 'filtering_orderby',
								'type'			=> 'select',
								'default'		=> 'ID',
								'label'	=> 'Order Posts By (If Not With Post Type Order Plugin)',
								'opts' => array(
									'ID' 		=> array('name' => __( 'Post ID (default)', 'filtering') ),
									'title' 	=> array('name' => __( 'Title', 'filtering') ),
									'date' 		=> array('name' => __( 'Date', 'filtering') ),
									'modified' 	=> array('name' => __( 'Last Modified', 'filtering') ),
									'rand' 		=> array('name' => __( 'Random', 'filtering') ),							
								)
							),
							array(
									'key'		=> 'filtering_order',
									'default' => 'DESC',
									'type' => 'select',
									'opts' => array(
										'DESC' 		=> array('name' => __( 'Descending (default)', 'filtering') ),
										'ASC' 		=> array('name' => __( 'Ascending', 'filtering') ),
									),
									'label'=> __( 'Select sort order', 'filtering'),
							),
						
						
					),
				);
					$options[] = array(	
					'key'			=> 'filtering_post_options',
						'type'		=> 'multi', 
						'title'		=> __('Post Options', 'filtering'), 
						
						'opts'	=> array(
							array(
							'key'			=> 'filtering_image_type',
								'type' 		=> 'select',
								'default'	=> 'images',
								'opts'	=> array(
										'images'	=> array('name' => __( 'Show Image on Top (Default)', 'filtering') ), 
										'only_images'	=> array('name' => __( "Show Only the Image", 'filtering') ),
										'only_text'	=> array('name' => __( "Text Only, no image", 'filtering') )

									), 
								'label' => __( 'Image Display Option (default is "Show Image on Top")', 'filtering'),				

						),
							array(
									'key'			=> 'filtering_show_info',
									'default'		=> null,
									'type' 			=> 'check',
									'size'			=> 'small',
									'label' 		=> __( 'Show post date and author?', 'filtering'),
								),
							array(
								'key'			=> 'filtering_date_format',
								'default'		=> 'F j, Y',
								'type' 			=> 'text',
								
								'label' 	=> __( 'Enter the date format e.g. F j, Y (Default)', 'filtering'),
							),
							 array(
							 		'key'			=> 'filtering_show_excerpt',
									'default'		=> null,
									'type' 			=> 'check',
									
									'label' 		=> __( 'Show the excerpt?', 'filtering'),
								),
													
							array(
								'key'			=> 'filtering_excerpt_length',
								'default'		=> '20',
								'type' 			=> 'text',
								
								'label' 	=> __( 'Max number of words for excerpts (Default is 20)', 'filtering'),
							),
							array(
								'key'			=> 'filtering_excerpt_more',
								'default'		=> '',
								'type' 			=> 'text',
								'label' 	=> __( 'Continue reading phrase (... display at end of excerpt if no phrase entered)', 'filtering'),
							),
							array(
							 		'key'			=> 'filtering_show_shortcodes',
									'default'		=> null,
									'type' 			=> 'check',
									
									'label' 		=> __( 'Show shortcodes in excerpt? Will display all shortcodes except [gallery]. Use with caution.', 'filtering'),
								),
								
							
						),
					);

					$options[] = array(	
					'key' 			=> 'filtering_image_formatting',
						'type'		=> 'multi', 
						'title'		=> __('Extra Image Options', 'filtering'), 
						
						'opts'	=> array(
							array(
								'key'			=> 'filtering_image_sizes',
								'type' 			=> 'select_imagesizes',
								'label' 		=> __( 'Select Thumb Size', 'filtering' ),
								'help'			=> __( 'You can select a thumbsize or enter image sizes below.', 'filtering' ),
							),
						 	array(
						 		'key'			=> 'filtering_image_width',
								'default'		=> '',
								'type' 			=> 'text',
								
								'label' 		=> __( 'Maximum Image Width', 'filtering'),
							),
							array(
								'key'			=> 'filtering_image_height',
								'default'		=> '',
								'type' 			=> 'text',
								
								'label' 		=> __( 'Maximum Image Height', 'filtering'),
							),	
								
							array(
								'key'			=> 'filtering_default_image',
								
								'type' 			=> 'image_upload',
								'label' 		=> __( 'Upload an image to use when no thumbnail is present (optional)', 'filtering'),
							),
							
							array(
								'key'			=> 'filtering_thumb_frame',
								'default'		=> '',
								'type' 			=> 'check',
								
								'label' 		=> __( 'Add A Frame To Images', 'filtering'),
							),
							

							),
					 	);
					
					$options[] = array(
					'key'			=>'filtering_pagination',
						'type'		=> 'multi', 
						'title'		=> __('Pagination (Optional)', 'filtering'), 
						
						'opts'	=> array(
							array(
								'key'			=> 'filtering_number',
								'type' 			=> 'count_select',
								'count_start'	=> (int) $wp_per_page,
								'count_number'	=> 999,
								'label' 	=> __( 'Number of posts to show. You can limit the number of posts here or leave at "Select" to show all posts', 'filtering'),				
								'help'			=>	'You cannot set the number of posts less than what is set in WordPress -> Settings -> Reading Settings',
							),
							
						
						),
					);
					
					
					
		
			return $options;
	}

	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);

		$post_type_array = array();
		if(post_type_exists('boxes')) {
		// Builtin types needed.
			$builtin = array(
			'post',
			'boxes',
			
			);
		} else {
			$builtin = array(
			'post',
			
			
			);
		}	
			// All CPTs except builtins
			$cpts = get_post_types( array(
			'public'   => true,
			'_builtin' => false
			) );
			
			// Merge Builtin types and 'important' CPTs to resulting array to use as argument.
			$post_types = array_merge($builtin, $cpts);
		
			if(!empty($post_types)){
	
					foreach($post_types as $post_type){
	
						$post_type_array[$post_type] = array(
							'name' => $post_type,
							'inputlabel' => $post_type
						);
					}
				}

				// Get Taxonomies

				// Exclude some Pagelines Taxonomies
				$exclude_taxonomies = array(
	    			
	    			'banner-sets',
	    			'feature-sets',
	    			'accordion-sets',

				);
				// Builtin types needed.
				$builtin = array(
				    'category',
				    'post_tag'
				    
				);
				// All Taxonomies.
				$taxs = get_taxonomies( array(
				    'public'   => true,
				    '_builtin' => false
				) );
				// remove Excluded Taxonomies from All Taxonomies.
				foreach($exclude_taxonomies as $exclude_taxonomy)
				    unset($taxs[$exclude_taxonomy]);
				// Merge Builtin types and 'important' Taxonomies to resulting array to use as argument.
				$taxonomies = array_merge($builtin, $taxs);	
	
			$taxonomies_array = array();	
	
				if  ($taxonomies) {
  				foreach ($taxonomies  as $taxonomy ) {
    				$taxonomy_array[$taxonomy] = array(
 
    						'name' => $taxonomy,
							'inputlabel' => $taxonomy
						);
					}		
				}
		
			$metatab_array = array(

				'filtering_setup' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Filtering Setup Options', 'filtering'), 
					'shortexp'	=> __('Basic setup options for handling of filtering.', 'filtering'),
					'exp'			=> __( '', 'filtering'),
					'selectvalues'	=> array(
						'filtering_post_type' => array(
							'default'		=> 'post',
							'type' 			=> 'select',
							'selectvalues' 			=> $post_type_array,
							'inputlabel'	=> __( 'Select your post type to filter. Default is "post."', 'filtering'),
						), 
						'filtering_taxonomy' => array(
							'default'		=> 'category',
							'type' 			=> 'select',
							'selectvalues' => $taxonomy_array,
							'inputlabel'	=> __( 'Select taxonomy. Make sure the taxonomy goes with the post type, i.e. category with posts. Default is "category"', 'filtering'),
						), 
					  ), 
					), 
					'filtering_terms_options' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Enter Categories, Terms or Tags to Exclude or Include', 'filtering'), 
					'shortexp'	=> __('Use this area to either Exclude or Include Categories, Terms or Tags (Not both). If your category or term has children you can exclude them, the default is to show category/term children.', 'filtering'),
					'exp'			=> __( '', 'filtering'),
					'selectvalues'	=> array(
						'filtering_terms' => array(
							'default'		=> '',
							'type' 			=> 'text',
							'inputlabel'	=> __( 'Enter Categories, Terms or Tags  ( if multiple, separate using a comma )', 'filtering'),				

						),

						'filtering_terms_type' => array(
									'type' 		=> 'select',
								'default'	=> 'exclude',
								'selectvalues'	=> array(
										'exclude'	=> array('name' => __( 'Exclude (Default)', 'filtering') ), 
										'include'	=> array('name' => __( "Include", 'filtering') ),
										), 
								'inputlabel' => __( 'Exclude or Include these categories, terms or tags? Default is "Exclude."', 'filtering'),				
							),
							
						'filtering_children' => array(
									'default'		=>	null,
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Exclude Child Categories/Terms?', 'filtering'),
								),
					),
				),	
					'filtering_navigation' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Filtering Navigation Options', 'filtering'), 
					'shortexp'	=> __('Options for type of navigation menu. By default a Horizontal Menu is shown on Desktop and Tablet Screens and a Select Menu on Smartphone Screens', 'filtering'),
					'exp'			=> __( '', 'filtering'),
					'selectvalues'	=> array(
						'filtering_menu' => array(
								'type'			=> 'select',
								'default'		=> 'horizontal',
								'inputlabel'	=> 'Menu Type on Desktop/Tablets (Default is Horizontal)',
								'selectvalues' => array(
									'horizontal' 		=> array('name' => __( 'Horizontal (default)', 'filtering') ),
									'select' 			=> array('name' => __( 'Dropdown/Select', 'filtering') ),
														
								)
							),
						'filtering_mobile' => array(
									'default'		=>	null,
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Turn off Mobile Select Menu?', 'filtering'),
								),
						),
					),
						
					'filtering_display' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Filtering Display Options', 'filtering'), 
					'shortexp'	=> __('Options for displaying of items.', 'filtering'),
					'exp'			=> __( '', 'filtering'),
					'selectvalues'	=> array(	
						
						'filtering_item_width' => array(
							'type' 			=> 'text_small',
							'default'		=> '250px',
							
							'inputlabel' 		=> __( 'Width of  each item. Default is "250px". Enter just the width value, px will be added for you.' , 'filtering'),
						),
						'filtering_all_phrase' => array(
							'type' 			=> 'text',
							'default'		=> __('Show All'),
							
							'inputlabel' 		=> __( 'Word/Phrase to use for All Items (Default is "Show All")' , 'filtering'),
						),
						
						
						'filtering_orderby' => array(
								'type'			=> 'select',
								'default'		=> 'ID',
								'inputlabel'	=> 'Order Posts By (If Not With Post Type Order Plugin)',
								'selectvalues' => array(
									'ID' 		=> array('name' => __( 'Post ID (default)', 'filtering') ),
									'title' 	=> array('name' => __( 'Title', 'filtering') ),
									'date' 		=> array('name' => __( 'Date', 'filtering') ),
									'modified' 	=> array('name' => __( 'Last Modified', 'filtering') ),
									'rand' 		=> array('name' => __( 'Random', 'filtering') ),							
								)
							),
							'filtering_order' => array(
									'default' => 'DESC',
									'type' => 'select',
									'selectvalues' => array(
										'DESC' 		=> array('name' => __( 'Descending (default)', 'filtering') ),
										'ASC' 		=> array('name' => __( 'Ascending', 'filtering') ),
									),
									'inputlabel'=> __( 'Select sort order', 'filtering'),
							),
						
						
					),
				),
				
					'filtering_post_options' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Post Options', 'filtering'), 
						'shortexp'	=> __('Options for what to show for each post.', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(
							'filtering_image_type' => array(
								'type' 		=> 'select',
								'default'	=> 'images',
								'selectvalues'	=> array(
										'images'	=> array('name' => __( 'Show Image on Top (Default)', 'filtering') ), 
										'only_images'	=> array('name' => __( "Show Only the Image", 'filtering') ),
										'only_text'	=> array('name' => __( "Text Only, no image", 'filtering') )

									), 
								'inputlabel' => __( 'Image Display Option (default is "Show Image on Top")', 'filtering'),				

						),
							'filtering_show_info' => array(
									'default'		=> null,
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Show post date and author?', 'filtering'),
								),
							'filtering_date_format' => array(
								'default'		=> 'F j, Y',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Enter the date format e.g. F j, Y (Default)', 'filtering'),
							),
							'filtering_show_excerpt' => array(
									'default'		=> null,
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Show the excerpt?', 'filtering'),
								),
													
							'filtering_excerpt_length' => array(
								'default'		=> '20',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Max number of words for excerpts (Default is 20)', 'filtering'),
							),
							'filtering_excerpt_more' => array(
								'default'		=> '',
								'type' 			=> 'text',
								'inputlabel' 	=> __( 'Continue reading phrase (... display at end of excerpt if no phrase entered)', 'filtering'),
							),
							
							
						),
					),
					'filtering_image_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Extra Image Options', 'filtering'), 
						'shortexp'	=> __('By default Filtering uses the featured image from your posts and are thumbnail sized (thumbnail sizes are set in WordPress Settings -> Media). If you want your images to be another size, enter the maximum width and maximum height below. You can also upload an image to use when no featured image is present. ', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(

							
						 	'filtering_image_width' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								
								'inputlabel' 		=> __( 'Maximum Image Width', 'filtering'),
							),
							'filtering_image_height' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								
								'inputlabel' 		=> __( 'Maximum Image Height', 'filtering'),
							),	
								
							'filtering_default_image' => array(
								
								'type' 			=> 'image_upload',
								'inputlabel' 		=> __( 'Upload an image to use when no thumbnail is present (optional)', 'filtering'),
							),
							
							'filtering_thumb_frame' => array(
								'default'		=> '',
								'type' 			=> 'check',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Frame To Images', 'filtering'),
							),
							

							),
					 	),
					
					
					'filtering_pagination' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Pagination Options', 'filtering'), 
						'shortexp'	=> __('Filtering works best without pagination and the default is no pagination. Use the settings below if you want to limit the number of posts per page. Some things to consider when using pagination: </br /><br />1) Using pagination on your Blog and Custom Post Types<ul style="padding-left:20px;"><li style="list-style: disc;">The best way to use pagination on these pages is to set the number of items equal to or more than what is in your WordPress -> Settings -> Reading options.</li><li style="list-style: disc;">If you set the number of items less than what is in your WordPress Settings then you must choose the option below to override the WordPress Settings or you will receive 404 errors on Filtering pages after page 1 or 2.</li><li style="list-style: disc;">Overriding WordPress Settings may affect the number of items displayed on other archive pages even though the Filtering Section is not active on that archive.</li> </ul>2) Only the categories/terms/tags that are present in the posts on the page will display in the Filtering navigation.', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(
							'filtering_number' => array(
							
							'type' 			=> 'text_small',
							'inputlabel'	=> __( 'Number of posts to show. Leave blank to show all posts (Suggested) otherwise enter a number (best 10 or more posts per page). ', 'filtering'),				

						),
						'filtering_override_home' => array(
							
							'type' 			=> 'check',
							'inputlabel'	=> __( 'Override WordPress Post Per Page Setting for Home/Blog Page? Only use if number of items is less than in WordPress Settings -> Reading.', 'filtering'),				

						),
						'filtering_override_archive' => array(
							
							'type' 			=> 'check',
							'inputlabel'	=> __( 'Override WordPress Post Per Page Setting for Custom Post Type Archive Page? Only use if number of items is less than in WordPress Settings -> Reading.', 'filtering'),				

						),	
							
						),
					),
					
					
					'filtering_styles' => array(
						'type'		=> 'multi_option', 
						'title' 		=> __( 'Custom CSS class', 'filtering'),
						'shortexp' 		=> __( 'Add a custom CSS class to this Filtering Section.', 'filtering'),
						'selectvalues'	=> array(
							'filtering_class' => array(
								'default'		=> '',
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Add custom css class to this Filtering Section. Try "custom-style" for different navigation style.', 'filtering'),
								),
							),
						),
			);
		
			$metatab_settings = array(
				'id' 		=> 'filtering_meta',
				'name' 		=> 'Filtering',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);

			register_metatab($metatab_settings, $metatab_array);
	}

	function section_template() {
		global $filtering;
        global $post; global $filtering_ID;
        $oset = array('post_id' => $filtering_ID);
        
        $filtering_class = ( $this->opt( 'filtering_class' ) ) ? $this->opt( 'filtering_class' ) : null;
				

		
			printf( '<div class="section-filtering %s">' , $filtering_class);

				$this->draw_navigation();
				$this->draw_filtering();
			
				
			echo '</div>';
 

    }
  


	

  	function taxonomy_query(){
  	 	global $filtering_ID;
        $oset = array('post_id' => $filtering_ID);
  		$filtering_tax = ( $this->opt( 'filtering_taxonomy' ) ) ? $this->opt( 'filtering_taxonomy' ) : 'category';
		$filtering_terms_type = ( $this->opt( 'filtering_terms_type' ) ) ? $this->opt( 'filtering_terms_type' ) : 'exclude';
     	$filtering_terms = ( $this->opt( 'filtering_terms' ) ) ? $this->opt( 'filtering_terms' ) : '';
      	$filtering_terms = str_replace(', ', ',', $filtering_terms); 

      	
  	// Setup Query Terms

		// Get Terms
      	
        if($filtering_terms) {
        	

        	$terms_list = '';
      		$terms_children = '';
      		$childterm = null;	
         	$filter_terms = explode(",", $filtering_terms);
         	
            foreach ($filter_terms as $filter_term) {
                $term = get_term_by( 'name',  $filter_term,  $filtering_tax  );
                $is_tag = is_tag($term);
                if($is_tag){
                 $termchildren = 0;
                 } else {
                 	$termchildren = get_term_children( $term->term_id, $filtering_tax );
                 }
                    // Get Children of Term
                if ($termchildren !== 0 && $termchildren !== null) {
	                foreach($termchildren as $termchild) {
	                	$childterm[] = $termchild;
	                }
	            }
                // Check to see if term exists in Taxonomy
                $filtering_check = term_exists($filter_term, $filtering_tax);
                if ($filtering_check !== 0 && $filtering_check !== null) {
                 	$term_array[] = $term->term_id;         
             	} else {
             		$term_array[] = '';
             	}
             }	
            	$terms_list= implode(", ", $term_array); 
            // See if want child terms in query too
            if($this->opt('filtering_children' )) {
            	$these_terms = $terms_list;
        	} else {
        		
        		if($childterm !== null) {
            	$terms_children= implode(", ", $childterm);
            	}else {
            	$terms_children = '';
            	}
          		$these_terms = $terms_list .',' .$terms_children; 
        	}
        	
        // See if terms are to be excluded or included and whether to show children
        	if($filtering_terms_type != 'exclude'){
        		if($this->opt('filtering_children' )) {
            	$args2 = array('include'=>$these_terms, 'parent'=>0);
            	} else {
            		$args2 = array('include'=>$these_terms);
            	}
        	} else {
        		if($this->opt('filtering_children' )) {
            	$args2 = array('exclude_tree'=>$these_terms);
            	} else {
            		$args2 = array('exclude'=>$these_terms);
            	}
        	}
	    	
	    } else {
	    	if($this->opt('filtering_children' )) {
            	$args2 = array('parent'=>0);
            	} else {
            		$args2 = null;
            	}

	    }

	    
	  
	
	 $terms = get_terms($filtering_tax, $args2);


	  return $terms;

  } 

  function filtering_query(){

  	global $filtering_ID;
  	$filtering_tax = ( $this->opt( 'filtering_taxonomy' ) ) ? $this->opt( 'filtering_taxonomy' ) : 'category';
		
        $oset = array('post_id' => $filtering_ID);

	 
        $terms = $this->taxonomy_query();
    
  	 // Query Variables

    	$filtering_type = ( $this->opt( 'filtering_post_type' ) ) ? $this->opt( 'filtering_post_type' ) : 'post';
		$filtering_tax = ( $this->opt( 'filtering_taxonomy' ) ) ? $this->opt( 'filtering_taxonomy' ) : 'category';
		$filtering_orderby = ( $this->opt( 'filtering_orderby' ) ) ? $this->opt( 'filtering_orderby' ) : 'ID';
		$filtering_order = ( $this->opt( 'filtering_order' ) ) ? $this->opt( 'filtering_order' ) : 'DESC';
		$filtering_number = ( $this->opt( 'filtering_number' ) ) ? $this->opt( 'filtering_number' ) : null;
      	
      	if($this->opt( 'filtering_number' ) ) {
      		$filtering_item_number = $filtering_number;

      	} else {
      		$filtering_item_number = -1;
      	}
      	
	 // Fix for individual taxonomy pages

		  // Get terms to include in $filtering query
	      $include = array();

			foreach ( $terms as $term )
			    $include[] = $term->term_id;
	

	

	if($this->opt('filtering_children')) {
		$include_children = false;
		}else {
			$include_children = true;
		}	

		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
		elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
		else { $paged = 1; }
		
	// Query arguments
	 $args = array(
	 	'post_type' => $filtering_type,
	 	'posts_per_page' => $filtering_item_number,
	 	'orderby'=>$filtering_orderby ,
	 	'order'=> $filtering_order,
	 	'paged' => $paged,
	 	
	 	'tax_query' => array(
			array(
				'taxonomy' => $filtering_tax,
				'field' => 'id',
				'terms' => $include,
				'include_children' => $include_children
				
			)
		)); 

		

        // Filtering Query
		$filtering_query = new WP_Query( $args );
		return $filtering_query;
		

  } 




// Draw Filtering Container

    function draw_filtering() {
    	global $wp_query;
    	global $post; 
    	global $text;
        global $filtering_ID;
        $oset = array('post_id' => $filtering_ID);
        
        if(is_tax()) {
        	$filtering = $wp_query;
        	$filtering_tax=   get_query_var( 'taxonomy' );
        	$term = get_query_var( 'term' );
         	$the_terms = get_term_by( 'slug',$term , $filtering_tax ); 
        
        
        $the_terms_id[] = $the_terms->term_id;
        $term_id = $the_terms->term_id;
      
        $termchildren = get_term_children( $term_id, get_query_var( 'taxonomy' ) );
        
        foreach ( $termchildren as $child ) {
			$term = get_term_by( 'slug', $child, $filtering_tax );
			$term_list[] = $term->term_id;
		} 
         $terms_merge = array_merge($the_terms_id, $termchildren);
         foreach ( $terms_merge as $term ) {
			$terms[] = get_term_by( 'id', $term, $filtering_tax );
			
		} 	
        	
        } else {
        $filtering = $this->filtering_query();
        $filtering_tax = ( $this->opt( 'filtering_taxonomy' ) ) ? $this->opt( 'filtering_taxonomy' ) : 'category';
        $terms = $this->taxonomy_query();
    }
        
        // Option Variables

        $filtering_width = ($this->opt('filtering_item_width' )) ? $this->opt('filtering_item_width' ).'px' : '250px';
		$filtering_show_excerpt = ($this->opt('filtering_show_excerpt' )) ? $this->opt('filtering_show_excerpt' ) : '' ;
		$filtering_excerpt_len = ($this->opt('filtering_excerpt_length' )) ? ($this->opt('filtering_excerpt_length' )) : '20';
        $filtering_date_format = ( $this->opt( 'filtering_date_format', $this->tset ) ) ? $this->opt( 'filtering_date_format', $this->tset ) : 'F, j Y';
		$filtering_image = ($this->opt('filtering_image_type' )) ? $this->opt('filtering_image_type' ) : 'images';
       	$filtering_default =  ( $this->opt( 'filtering_default_image' ) ) ? $this->opt( 'filtering_default_image' ) : '' ;
       	
		
      
         
         ?>

        <div class="filtering clearfix">
        <?php

        // Start Filtering Container and Loop
        
        while ($filtering->have_posts() ) : $filtering->the_post();

        	$date = get_the_date($filtering_date_format);
			// Get Post Terms
        	$terms = get_the_terms($post->ID , $filtering_tax );
			$terms_string = '';
	 		foreach ( $terms as $term ) :     
      			$terms_string = $terms_string.$term->slug.' '; 
         	endforeach;   
         	sprintf($date);
			// Start Drawing Item
			 
         	printf( '<div class="item %s " style="width: %s; margin-right: 10px;">' , $terms_string, $filtering_width);

			echo '<div class="inner-item ">';

			// Draw image as long as not only_text
			if($filtering_image != 'only_text')	{
			
				// Image variables
				$this->draw_image($filtering_tax, $filtering_default);
					
		}		

	 // Draw Title as long as not only_images
	 		
	 if($filtering_image != 'only_images')	{
	 			$title = esc_html(get_the_title($post->ID));
				$box_link = implode(' ' , get_post_meta($post->ID, 'the_box_icon_link'));
				
				if($filtering_tax == 'box-sets') {
					$permalink = $box_link;
				} else {
					$permalink = get_permalink($post->ID);
					}
				
				
	?>
		<div class="item-info">
			<div class="item-title">
				<?php
				if($permalink == null) {
				printf('<h4>%s</a></h4>' ,  $title); 
				} else {
					printf('<h4><a href="%s" title="%s">%s</a></h4>' , $permalink, $title, $title); 
				
				}
				?>
			</div>

	<?php

		if($this->opt('filtering_show_info')) {
			printf('<div class="post-info">%s By ' , $date);
			echo the_author_posts_link();
			echo '</div>';
		}

		// Draw excerpt as long as true
		
    	if($this->opt('filtering_show_excerpt')) {
    		// Get post excerpt
            	if($post->post_excerpt != ''){
				$filtering_excerpt = $post->post_excerpt;
			}elseif($filtering_tax == 'box-sets'){
				if(get_post_meta($post->ID, 'box_more_text')) :
				$filtering_excerpt_more = implode(' ' , get_post_meta($post->ID, 'box_more_text'));
				else :
				$filtering_excerpt_more = ($this->opt('filtering_excerpt_more' , $this->tset)) ? ($this->opt('filtering_excerpt_more' , $this->tset)) : '';
				endif;
	   			$box_link = implode(' ' , get_post_meta($post->ID, 'the_box_icon_link'));
				$more_text = ' <a href="'. $box_link .'">... '.$filtering_excerpt_more.'</a>';
				if($box_link== null) :
					$filtering_excerpt = $post->post_content;
				else :
				$filtering_excerpt = $post->post_content .' ' . $more_text;
				endif;

			}else {
				if($this->opt('filtering_show_shortcodes' )) {	
						$filtering_excerpt = $this->filtering_trim_excerpt_tags($content);					
				} else {
					$filtering_excerpt=$this->filtering_trim_excerpt($text);
				}				
			}

    		printf('<div class="item-excerpt">%s</div>', $filtering_excerpt );
    		
    	}

	echo '</div>';
	
	 } // End Draw Title and Excerpt
	
	echo '</div></div>';


	
	endwhile; // End loop



	echo '</div>';



    $total_pages = $filtering->max_num_pages;
  	$big = 999999999; // need an unlikely integer
        if ($total_pages > 1){
          
        if ( get_query_var('paged') ) { $current_page = max(1, get_query_var('paged')); }
		else { $current_page = max(1, get_query_var('page')); }
		
          
        echo '<div class="pagination pagination-centered">';
          
        echo paginate_links(array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?page=%#%',
			'current' => $current_page,
            'total' => $total_pages,
            'type' => 'list',
            'prev_text' => __('Prev' , 'filtering'),
            'next_text' => __('Next' , 'filtering')
        ));
  
    echo '</div>';
     
    }
	


 } 



 // End Draw Filtering

 // Draw Navigation

   function draw_navigation() {
  	global $wp_query;
    	global $post; 
    	global $text;
        global $filtering_ID;
        $oset = array('post_id' => $filtering_ID);
        $filtering_all_phrase = ($this->opt('filtering_all_phrase', $this->tset)) ? ($this->opt('filtering_all_phrase', $this->tset)) : __( 'Show All', 'filtering' );
        
        
        if(is_tax()) {
        	$filtering = $wp_query;
        	$filtering_tax=   get_query_var( 'taxonomy' );
        	$term = get_query_var( 'term' );
         	$the_terms = get_term_by( 'slug',$term , $filtering_tax ); 
        
        
        $the_terms_id[] = $the_terms->term_id;
        $term_id = $the_terms->term_id;
      
        $termchildren = get_term_children( $term_id, get_query_var( 'taxonomy' ) );
        
        foreach ( $termchildren as $child ) {
			$term = get_term_by( 'slug', $child, $filtering_tax );
			$term_list[] = $term->term_id;
		} 
         $terms_merge = array_merge($the_terms_id, $termchildren);
         foreach ( $terms_merge as $term ) {
			$terms[] = get_term_by( 'id', $term, $filtering_tax );
			
		} 	
        	
        } else {
        $filtering = $this->filtering_query();
        $filtering_tax = ( $this->opt( 'filtering_taxonomy' ) ) ? $this->opt( 'filtering_taxonomy' ) : 'category';
        $terms = $this->taxonomy_query();
    }
  	  $term_list = array();
         while ($filtering->have_posts() ) : $filtering->the_post();
	
			// Get Post Terms
        	$post_terms = get_the_terms($post->ID , $filtering_tax );

		 		foreach ( $post_terms as $term ) :   
	      			
	      			if( !in_array( $term->term_id, $term_list ) ){
	      				$term_list[] = $term->term_id;
	      				
	    				}
	    				
	         	endforeach;   
         	endwhile;		
         	
        
  
  	$nav_terms = array();
         if(is_tax()) {
         			
         		foreach($terms as $term) :
         			
         		$nav_terms[] = $term->term_id;
         	
         		endforeach;
         	
         	 

         } else {
         foreach($terms as $term) :
         		
         $parent = $term->parent;
         if( in_array( $term->term_id, $term_list ) ){
         			
         	if($this->opt('filtering_children')) {
         		if($term->parent > 0) {
         			 null;
         		} else {
         			$nav_terms[] = $term->term_id;
         		}
         	} else {
         		$nav_terms[] = $term->term_id;
         	}			
	    				
	    }


        endforeach; 
        }

        //	$nav_terms = implode(",", $nav_terms);
        	
    	//	$nav_terms = get_terms($filtering_tax, array('order'=>'DESC', 'include' => $nav_terms));
    		
        	
         ?>

        <nav class="filtering-nav-wrap hidden-phone">
           <ul class="options clearfix">
			
		<?php 
		if(is_tax()) {
			null;
		} else {
			printf('<li><a href="#show-all" data-filter="*" class="selected">%s</a></li>' , $filtering_all_phrase);
		
		}	
			foreach( $nav_terms as $term_id ){
				$term = get_term( $term_id, $filtering_tax ); ?>

		    	<li><a href="#" data-filter=".<?php echo $term->slug?>"><?php echo $term->name?></a></li>

		    <?php } ?>

			</ul>
           
        </nav>



        <nav class="filtering-nav-option visible-phone">
           <select class="select">
			
		<?php 
		if(is_tax()) {
			null;
		} else {
			printf('<option value="*">%s</option>' , $filtering_all_phrase);
		}

			foreach( $nav_terms as $term_id ){
				$term = get_term( $term_id, $filtering_tax ); ?>
				<option value=".<?php echo $term->slug?>"><?php echo $term->name?></option>
		    	
		    <?php } ?>

			</select>
           
        </nav>
        <?php
  }


 function draw_image($filtering_tax, $filtering_default) {
 	
 	global $post;
 
 	// Image variables
 		$filtering_image_width = ($this->opt('filtering_image_width' )) ? $this->opt('filtering_image_width' ) : '';
		$filtering_image_height = ($this->opt('filtering_image_height' )) ? $this->opt('filtering_image_height' ) : '';
       	$thumbnail_width = get_option( 'thumbnail_size_w' );
		$thumbnail_height = get_option( 'thumbnail_size_h' );

 				// Set image width off settings or return thumbnail sizes
		if($filtering_image_width > 0) {
			$image_width = $filtering_image_width . 'px';	
		}
		else  {	
			$image_width = $thumbnail_width . 'px';		
		}
		if($filtering_image_height > 0) {	
			$image_height = $filtering_image_height . 'px';		 	
		}
		else  {
			$image_height = $thumbnail_height . 'px';			
			
		}

			$title = esc_html(get_the_title($post->ID));
			$box_image = get_post_meta($post->ID,'the_box_icon');
			$box_link = implode(' ' , get_post_meta($post->ID, 'the_box_icon_link'));
			
			if($filtering_tax == 'box-sets') {
				$image = implode('', $box_image);
				$permalink = $box_link;
			} else {
				$permalink = get_permalink($post->ID);
				$image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			}
			
			
			printf('<div class="filtering-image center">');
				
				// If has featured image
		 if ( $image ) :

		 			if($permalink == null) {
					printf('<img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/>' , $image,  $title, $image_width, $image_height);			
					} else {
						printf('<a href="%s"><img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/></a>' , $permalink, $image,  $title, $image_width, $image_height);			
					
					}
		 else :

					// If uploaded a default image
					if($filtering_default) {
						if($permalink == null) :
							printf('<img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/>' , $filtering_default,  $title, $image_width, $image_height);
						else: 
							printf('<a href="%s"><img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/></a>' , $permalink, $filtering_default,  $title, $image_width, $image_height);
						endif;
					} else {

						// Return no image if none found
						null;

				}
		
	 	endif; // End Image

	 
		echo '</div>';

	 

 }
  	

 	function filtering_trim_excerpt_tags($content) {
 		global $post;
		$filtering_excerpt_len = ($this->opt('filtering_excerpt_length' )) ? ($this->opt('filtering_excerpt_length' )) : '20';
	    $filtering_excerpt_more = ($this->opt('filtering_excerpt_more' , $this->tset)) ? ($this->opt('filtering_excerpt_more' , $this->tset)) : '';
	   	$permalink = get_permalink($post->ID);
	   	$filtering_more = ' <a href="'. $permalink .'">'.$filtering_excerpt_more.'</a>';
 		if ( '' == $content ) {
	 		$content = get_the_content();
	 		$excerpt_length = intval($filtering_excerpt_len);
	 		$words = explode(' ', $content, $excerpt_length + 1);
			$content = wp_trim_words( $content , $filtering_excerpt_len );
			$content = strip_tags($content, '[, ]');
			
			if (count($words) > $excerpt_length) {
				$content = $content . ' ' . $filtering_more;
			} else {
				$content = $content;
			}
			$content = apply_filters('the_content' , $content );
			remove_shortcode('gallery');

		}
		return $content;
 	}



	function filtering_trim_excerpt($text) { // Fakes an excerpt if needed
		global $post;
		$filtering_excerpt_len = ($this->opt('filtering_excerpt_length' )) ? ($this->opt('filtering_excerpt_length' )) : '20';
	    $filtering_excerpt_more = ($this->opt('filtering_excerpt_more' , $this->tset)) ? ($this->opt('filtering_excerpt_more' , $this->tset)) : '';
	   	$permalink = get_permalink($post->ID);
				

		if ( '' == $text ) {
			$text = get_the_content('');

			$text = strip_shortcodes( $text );

			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]>', $text);
			$text = strip_tags($text);

			$excerpt_length = $filtering_excerpt_len;
			$words = explode(' ', $text, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				array_push($words, ' <a href="'. $permalink .'">... '.$filtering_excerpt_more.'</a>');
				$text = implode(' ', $words);
			}
		}
		return $text;

	}


}		