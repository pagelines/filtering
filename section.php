<?php
/*
	Section: Filtering
	Author: elSue
	Author URI: http://www.elsue.com
	Description: Filter your posts or custom post types by category or hierachal custom taxonomy
	Class Name: Filtering
	Cloning: false
	Workswith: content, template, main
	Version: 1.0
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
	function section_styles(){
		
		wp_enqueue_script( 'isotope', $this->base_url.'/js/jquery.isotope.min.js');
		wp_enqueue_script( 'filtering', $this->base_url.'/js/filtering.js');
		wp_enqueue_script( 'equalize', $this->base_url.'/js/equalizecols.js');
		wp_enqueue_script( 'easing', $this->base_url.'/js/jquery.easing.js');
		
		}

	function section_head() {
	
		?>
		<script>
		
		jQuery(window).load(function(){
		jQuery(".filtering-image").equalizeCols(); // Make image container equal
		jQuery(".item-info").equalizeCols(); // Make row heights equal
		
		
		jQuery('.filtering-image').each(function() {
	        var container_height = jQuery(this).height()+'px';
	        jQuery(this).css('line-height', container_height);
       
    	});

		
});
</script>
<?php


		if(ploption( 'filtering_thumb_frame', $this->oset )) {
		?>
		<script>
	 	jQuery(document).ready(function() {
			jQuery('.filtering-image').addClass('pl-imageframe');

		});
		</script>
		<?php
		}
		
	}



	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);

		$post_type_array = array();
		// Builtin types needed.
			$builtin = array(
			'post',
			
			);
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
	    			'box-sets',
	    			'banner-sets',
	    			'feature-sets',

				);
				// Builtin types needed.
				$builtin = array(
				    'category',
				    'post_tag'
				    
				);
				// All CPTs.
				$taxs = get_taxonomies( array(
				    'public'   => true,
				    '_builtin' => false
				) );
				// remove Excluded CPTs from All CPTs.
				foreach($exclude_taxonomies as $exclude_taxonomy)
				    unset($taxs[$exclude_taxonomy]);
				// Merge Builtin types and 'important' CPTs to resulting array to use as argument.
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
						'filtering_excludes' => array(
							'default'		=> '',
							'type' 			=> 'text',
							'inputlabel'	=> __( 'Enter Excluded Categories, Terms or Tags  ( if multiple, separate using a comma )', 'filtering'),				

						),
						'filtering_includes' => array(
							'default'		=> '',
							'type' 			=> 'text',
							'inputlabel'	=> __( 'Enter Categories, Terms or Tags to Include  ( if multiple, separate using a comma )', 'filtering'),				

						),
						'filtering_number' => array(
							
							'type' 			=> 'text_small',
							'inputlabel'	=> __( 'Number of posts to show. If left blank no pagination will occur, all posts will display based on post type and taxonomy chosen.', 'filtering'),				

						),
						
						'filtering_item_width' => array(
							'type' 			=> 'text_small',
							'default'		=> '250px',
							
							'inputlabel' 		=> __( 'Width of  each item item. Default is "250px". Enter just the width value, px will be added for you.' , 'filtering'),
						),
						'filtering_all_phrase' => array(
							'type' 			=> 'text',
							'default'		=> 'Show All',
							
							'inputlabel' 		=> __( 'Word/Phrase to use for All Items (Default is "Show All")' , 'filtering'),
						),

						'filtering_image_type' => array(
								'type' 		=> 'select',
								'default'	=> 'images',
								'selectvalues'	=> array(
										'images'	=> array('name' => __( 'Show Image on Top', 'filtering') ), 
										'only_images'	=> array('name' => __( "Show Only the Image", 'filtering') ),
										'only_text'	=> array('name' => __( "Text Only, no image", 'filtering') )

									), 
								'inputlabel' => __( 'Image Display Option (default is "Show Image on Top")', 'filtering'),				

						),
					 
						
						
					),
				),
				
					'filtering_excerpt_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Filtering Excerpt Options', 'filtering'), 
						'shortexp'	=> __('Options for formatting box excerpts.', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(
							'filtering_show_excerpt' => array(
									
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Show the excerpt?', 'filtering'),
								),
													
							'filtering_excerpt_length' => array(
								'default'		=> '20',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Max number of words for excerpts', 'filtering'),
							),
							
						),
					),
					'filtering_image_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Extra Image Options (Optional)', 'filtering'), 
						'shortexp'	=> __('By default Filtering uses the featured image from your posts and are thumbnail sized (thumbnail sizes are set in WordPress Settings -> Media). If you want your images to be another size, enter the maximum width and maximum height below. You can also upload an image to use when no featured image is present. ', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(
							
						 	'filtering_image_width' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								
								'inputlabel' 		=> __( 'Maximum Image Width.', 'filtering'),
							),
							'filtering_image_height' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								
								'inputlabel' 		=> __( 'Maximum Image Height', 'filtering'),
							),	
								
							'filtering_default_image' => array(
								
								'type' 			=> 'image_upload',
								'inputlabel' 		=> __( 'Upload an image to use when no thumbnail present (optional)', 'filtering'),
							),
							
							'filtering_thumb_frame' => array(
								'default'		=> '',
								'type' 			=> 'check',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Frame To Images', 'filtering'),
							),
							

							),
					 	),
					
					
					'filtering_ordering' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Ordering Options', 'filtering'), 
						'shortexp'	=> __('Optionally control the ordering of the boxes', 'filtering'),
						'exp'		=> __('The easiest way to order boxes is using a post type order plugin for WordPress. However, if you would like to do it algorithmically, we have provided these options for you.', 'filtering'),
						'selectvalues'	=> array(
							
							'filtering_orderby' => array(
								'type'			=> 'select',
								'default'		=> 'ID',
								'inputlabel'	=> 'Order Boxes By (If Not With Post Type Order Plugin)',
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
										'DESC' 		=> array('name' => __( 'Descending', 'filtering') ),
										'ASC' 		=> array('name' => __( 'Ascending', 'filtering') ),
									),
									'inputlabel'=> __( 'Select sort order', 'filtering'),
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
        $filtering_class = ( ploption( 'filtering_class', $this->oset ) ) ? ploption( 'filtering_class', $this->oset ) : null;
		

		
			printf( '<div class="section-filtering %s">' , $filtering_class);
				$this->draw_filtering();
				
			echo '</div>';
 

    }
  
    
    

// Draw Filtering Container

    function draw_filtering() {
    	global $wp_query;
    	global $post; 
        global $filtering_ID;
        $oset = array('post_id' => $filtering_ID);

        // Query Variables

    	$filtering_type = ( ploption( 'filtering_post_type', $this->oset ) ) ? ploption( 'filtering_post_type', $this->oset ) : 'post';
		$filtering_tax = ( ploption( 'filtering_taxonomy', $this->oset ) ) ? ploption( 'filtering_taxonomy', $this->oset ) : 'category';
		$filtering_orderby = ( ploption( 'filtering_orderby', $this->oset ) ) ? ploption( 'filtering_orderby', $this->oset ) : 'ID';
		$filtering_order = ( ploption( 'filtering_order', $this->oset ) ) ? ploption( 'filtering_order', $this->oset ) : 'DESC';
		$filtering_excludes = ( ploption( 'filtering_excludes', $this->oset ) ) ? ploption( 'filtering_excludes', $this->oset ) : '';
      	$filtering_includes = ( ploption( 'filtering_includes', $this->oset ) ) ? ploption( 'filtering_includes', $this->oset ) : '';
      	$filtering_number = ( ploption( 'filtering_number', $this->oset ) ) ? ploption( 'filtering_number', $this->oset ) : null;
      
      	if(ploption( 'filtering_number', $this->oset ) ) {
      		$filtering_item_number = $filtering_number;

      	} else {
      		$filtering_item_number = -1;
      	}
      	// Setup Query Terms

		// Convert Excluded Terms Names into IDs
      	$excludes = '';
        if($filtering_excludes) {
         $exclude_terms = explode(", ", $filtering_excludes);
            foreach ($exclude_terms as $exclude_term) {
                $term = get_term_by( 'name',  $exclude_term,  $filtering_tax  );
                // Check to see if term exists in Taxonomy
                $exclude = term_exists($exclude_term, $filtering_tax);
                
                if ($exclude !== 0 && $exclude !== null) {
                 $exclude_term_array[] = $term->term_id;         
             } else {
             	$exclude_term_array[] = '';
             }
            }

            $excludes= implode(", ", $exclude_term_array); 

        }

        // Convert Excluded Terms Names into IDs
      	$includes = '';
        if($filtering_includes) {
         $include_terms = explode(", ", $filtering_includes);
            foreach ($include_terms as $include_term) {
                $term = get_term_by( 'name',  $include_term,  $filtering_tax  );
                // Check to see if term exists in Taxonomy
                $included = term_exists($include_term, $filtering_tax);
                
                if ($included !== 0 && $included !== null) {
                 $include_term_array[] = $term->term_id;         
             } else {
             	$include_term_array[] = '';
             }
            }

            $includes = implode(", ", $include_term_array); 

        }
      
      // Get Terms
    $args2 = array('exclude'=>$excludes, 'include'=>$includes);

      // Check to see if category or other taxonomy
    if($filtering_tax != 'category')	{
	  $terms = get_terms($filtering_tax, $args2);
		} else {
	  $terms = get_categories('exclude='.$excludes.' ');
	}
	  // Get terms to include in $filtering query
      $include = array();

		foreach ( $terms as $term )
		    $include[] = $term->term_id;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

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
				'terms' => $include
				
			)
		));  

        // Filtering Query
		$filtering = new WP_Query( $args );
		

        // Option Variables

        $filtering_width = (ploption('filtering_item_width' , $this->oset)) ? ploption('filtering_item_width' , $this->oset).'px' : '250px';
		$filtering_show_excerpt = (ploption('filtering_show_excerpt' , $this->oset)) ? ploption('filtering_show_excerpt' , $this->oset) : '' ;
		$filtering_excerpt_len = (ploption('filtering_excerpt_length' , $this->oset)) ? (ploption('filtering_excerpt_length' , $this->oset)) : '20';
        $filtering_all_phrase = (ploption('filtering_all_phrase', $this->oset)) ? (ploption('filtering_all_phrase', $this->oset)) : 'Show All';
		$filtering_image = (ploption('filtering_image_type' , $this->oset)) ? ploption('filtering_image_type' , $this->oset) : 'images';
       	$filtering_image_width = (ploption('filtering_image_width' , $this->oset)) ? ploption('filtering_image_width' , $this->oset) : '';
		$filtering_image_height = (ploption('filtering_image_height' , $this->oset)) ? ploption('filtering_image_height' , $this->oset) : '';
       	$filtering_default =  ( ploption( 'filtering_default_image', $this->oset ) ) ? ploption( 'filtering_default_image', $this->oset ) : '' ;
       	$thumbnail_width = get_option( 'thumbnail_size_w' );
		$thumbnail_height = get_option( 'thumbnail_size_h' );

		// Set image width off settings or return thumbnail sizes
		if((ploption('filtering_image_width' , $this->oset))) {
			$image_width = $filtering_image_width . 'px';
			$image_height = $filtering_image_height . 'px';
		 	
		}
		else  {

			$image_height = $thumbnail_height . 'px';
			$image_width = $thumbnail_width . 'px';	
			
			
		}
        
        // Draw Filtering Navigation

        ?>

        <nav class="filtering-nav-wrap">
           <ul class="options clearfix">
				
		<?php 
			printf('<li><a href="#show-all" data-filter="*" class="selected">%s</a></li>' , $filtering_all_phrase);


			foreach( $terms as $term ){ ?>

		    	<li><a href="#" data-filter=".<?php echo $term->slug?>"><?php echo $term->name?></a></li>

		    <?php } ?>

			</ul>
           
        </nav>

        <div class="filtering clearfix">
        <?php

        // Start Filtering Container and Loop

        while ($filtering->have_posts() ) : $filtering->the_post();

        	
			// Get Post Terms
        	$terms = get_the_terms($post->ID , $filtering_tax );
			$terms_string = '';
	 		foreach ( $terms as $term ) :     
      			$terms_string = $terms_string.$term->slug.' '; 
         	endforeach;   

			// Start Drawing Item
         	printf( '<div class="item %s " style="width: %s; margin-right: 10px;">' , $terms_string, $filtering_width);

			echo '<div class="inner-item">';

			// Draw image as long as not only_text
			if($filtering_image != 'only_text')	{
			
				// Image variables
				$permalink = get_permalink($post->ID);
				$title = get_the_title($post->ID);
				$image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
				
				printf('<div class="filtering-image center">');
				
				// If has featured image
		 		if ( $image ) {

		 			if((ploption('filtering_image_width' , $this->oset))) {
				
					printf('<a href="%s"><img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/></a>' , $permalink, $image,  $title, $image_width, $image_height);			
					} else {
							?>
		
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
	
		<?php
					}
				} else {

					// If uploaded a default image
					if($filtering_default) {
					
						printf('<a href="%s"><img src="%s" alt="%s" style="max-width: %s; max-height: %s;"/></a>' , $permalink, $filtering_default,  $title, $image_width, $image_height);

					} else {

						// Return no image if none found
						null;

				}
		
	 	} // End Image

	 
		echo '</div>';

	 } // End Show Image

	 // Draw Title as long as not only_images

	 if($filtering_image != 'only_images')	{
	?>
		<div class="item-info">
			<div class="item-title">
				<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			</div>

	<?php

		// Draw excerpt as long as true
	
    	if(ploption('filtering_show_excerpt', $this->oset)) {
    		// Get post excerpt
            	if($post->post_excerpt != ''){
				$filtering_excerpt = $post->post_excerpt;
			}else {
				$filtering_excerpt = custom_trim_excerpt(apply_filters('the_content', $post->post_content), $filtering_excerpt_len ); 
			}

    		
    		printf('<div class="item-excerpt">%s</div>', $filtering_excerpt );
    	}

	echo '</div>';
	
	 } // End Draw Title and Excerpt
	
	echo '</div></div>';


	
	endwhile; // End loop



	echo '</div>';



	$total_pages = $filtering->max_num_pages;
 
if ($total_pages > 1){
 
$current_page = max(1, get_query_var('paged'));
 
echo '<div class="pagination pagination-centered">';
 
echo paginate_links(array(
'base' => get_pagenum_link(1) . '%_%',
'format' => '/page/%#%',
'current' => $current_page,
'total' => $total_pages,
'type' => 'list',
'prev_text' => 'Prev',
'next_text' => 'Next'
));
 
echo '</div>';

}


 } // End Draw Taxonomy


}		