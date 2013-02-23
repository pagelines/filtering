<?php

/*
	Section: Filtering
	Author: elSue
	Author URI: http://www.elsue.com
	Description: Filters by category.
	Class Name: Filtering
	Cloning: true
	Workswith: content, template, main
	
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
		wp_enqueue_script( 'equalize', $this->base_url.'/js/jquery.equalizer.min.js');
		wp_enqueue_script( 'easing', $this->base_url.'/js/jquery.easing.js');
		
		}

	function section_head( $clone_id ) {  
		global $pagelines_ID;
		$oset = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);		
		
		$filtering_max_height = ( ploption( 'filtering_max_height', $oset ) ) ? ploption( 'filtering_max_height', $oset ) : '';
		if ($filtering_max_height != ''){
			echo '<style type="text/css">.top_thumbs .abboxgraphic, .only_thumbs .abboxgraphic{max-height:'.$filtering_max_height.'px;overflow:hidden;}</style>';
		}

	

		?>

  <script>
	jQuery(document).ready(function (){
		

				    jQuery(document).ready(function(){
				    jQuery.Isotope.prototype._getCenteredMasonryColumns = function() {
				    this.width = this.element.width();

				    var parentWidth = this.element.parent().width();

				                  // i.e. options.masonry && options.masonry.columnWidth
				    var colW = this.options.masonry && this.options.masonry.columnWidth ||
				                  // or use the size of the first item
				                  this.$filteredAtoms.outerWidth(true) ||
				                  // if there's no items, use size of container
				                  parentWidth;

				    var cols = Math.floor( parentWidth / colW );
				    cols = Math.max( cols, 1 );

				    // i.e. this.masonry.cols = ....
				    this.masonry.cols = cols;
				    // i.e. this.masonry.columnWidth = ...
				    this.masonry.columnWidth = colW;
				  };

				  jQuery.Isotope.prototype._masonryReset = function() {
				    // layout-specific props
				    this.masonry = {};
				    // FIXME shouldn't have to call this again
				    this._getCenteredMasonryColumns();
				    var i = this.masonry.cols;
				    this.masonry.colYs = [];
				    while (i--) {
				      this.masonry.colYs.push( 0 );
				    }
				  };

				  jQuery.Isotope.prototype._masonryResizeChanged = function() {
				    var prevColCount = this.masonry.cols;
				    // get updated colCount
				    this._getCenteredMasonryColumns();
				    return ( this.masonry.cols !== prevColCount );
				  };

				  jQuery.Isotope.prototype._masonryGetContainerSize = function() {
				    var unusedCols = 0,
				        i = this.masonry.cols;
				    // count unused columns
				    while ( --i ) {
				      if ( this.masonry.colYs[i] !== 0 ) {
				        break;
				      }
				      unusedCols++;
				    }

				    return {
				          height : Math.max.apply( Math, this.masonry.colYs ),
				          // fit container to columns that have been used;
				          width : (this.masonry.cols - unusedCols) * this.masonry.columnWidth
				        };
				  };




				  	var mycontainer = jQuery('.filtering');

				      // add randomish size classes
				      mycontainer.find('.item').each(function(){
				        var $this = jQuery(this),
				            number = parseInt( $this.find('.item-title').text(), 10 );
				        if ( number % 7 % 2 === 1 ) {
				          $this.addClass('width2');
				        }
				        if ( number % 3 === 0 ) {
				          $this.addClass('height2');
				        }
				      });

    
				      mycontainer.isotope({
					      itemSelector: '.item', 

					      
					      masonry: {
    						columnWidth: 250,
    						height: 250
							}
					  });
     
			        
			   // filter items when filter link is clicked
			jQuery('#options a').click(function(){
			  var selector = jQuery(this).attr('data-filter');
			  mycontainer.isotope({ filter: selector });
			  return false;  
			  });
		  
		});
		
	
		
});	
	
  </script>
 <?php
	
	}

	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);

		$post_type_array = array();
		// Builtin types needed.
			$builtin = array(
			'post',
			
			);
			// All CPTs.
			$cpts = get_post_types( array(
			'public'   => true,
			'_builtin' => false
			) );
			
			// Merge Builtin types and 'important' CPTs to resulting array to use as argument.
			$post_types = array_merge($builtin, $cpts);
		

			

		//	$post_types=get_post_types(array('public'   => true, '_builtin' => false)); 
			
				if(!empty($post_types)){
	
					foreach($post_types as $post_type){
	
						$post_type_array[$post_type] = array(
							'name' => $post_type,
							'inputlabel' => $post_type
						);

					}

		
				}

				$exclude_taxonomies = array(
	    			'box-sets',
	    			'banner-sets',
	    			'feature-sets'
				);
				// Builtin types needed.
				$builtin = array(
				    'categories',
				    
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
					'title'		=> __('Filtering Setup Options', 'pagelines'), 
					'shortexp'	=> __('Basic setup options for handling of filltering.', 'pagelines'),
					'exp'			=> __( '', 'pagelines'),
					'selectvalues'	=> array(
						'filtering_post_type' => array(
							'default'		=> 'posts',
							'type' 			=> 'select',
							'selectvalues' 			=> $post_type_array,
							'inputlabel'	=> __( 'List your page/post IDs To Query ( comma separated )', 'pagelines'),
						), 
						'filtering_taxonomy' => array(
							'default'		=> 'categories',
							'type' 			=> 'select',
							'selectvalues' => $taxonomy_array,
							'inputlabel'	=> __( 'Category To Show ( if the default category taxonomy is supported by the chosen post type )', 'pagelines'),
						), 
						'filtering_excludes' => array(
							'default'		=> '',
							'type' 			=> 'text',
							'inputlabel'	=> __( 'Excluded Categories/Terms. Must be slug ( if multiple, separate using a comma )', 'pagelines'),				

						),
						
						'filtering_col_number' => array(
							'type' 			=> 'count_select',
							'default'		=> '3',
							'count_number'	=> '5', 
							'count_start'	=> '1',
							'inputlabel' 		=> __( "Boxes Per Row", 'pagelines'),
						), 
						'filtering_items' => array(
							'default'		=> '6',
							'type' 			=> 'text_small',
							'size'			=> 'small',
							'inputlabel' 	=> __( 'Maximum Boxes To Show', 'pagelines'),
						),
						
					),
				),
				
					'filtering_excerpt_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Excerpt Options', 'pagelines'), 
						'shortexp'	=> __('Options for formatting box excerpts.', 'pagelines'),
						'exp'		=> __('', 'pagelines'),
						'selectvalues'	=> array(
							'filtering_show_excerpt' => array(
									'default'		=> 0,
									'type' 			=> 'check',
									'size'			=> 'small',
									'inputlabel' 		=> __( 'Show the excerpt?', 'pagelines'),
								),
													
							'filtering_excerpt_length' => array(
								'default'		=> '20',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Max number of words for excerpts', 'pagelines'),
							),
							
						),
					),
					'filtering_image_formatting' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Image Options', 'pagelines'), 
						'shortexp'	=> __('Options for formatting box images.', 'pagelines'),
						'exp'		=> __('', 'pagelines'),
						'selectvalues'	=> array(
							
							'filtering_thumb_type' => array(
								'type' 		=> 'select',
								'default'	=> 'inline_thumbs',
								'selectvalues'	=> array(
										'inline_thumbs'	=> array('name' => __( 'Image At Left', 'pagelines') ),
										'top_thumbs'	=> array('name' => __( 'Image On Top', 'pagelines') ), 
										'only_thumbs'	=> array('name' => __( "Only The Image, No Text", 'pagelines') ),
										'only_text'	=> array('name' => __( "Only The Text, No Image", 'pagelines') )

									), 
								'inputlabel' => __( 'Box Thumb Style (optional - defaults to "At Left")', 'pagelines'),				

							),
							'filtering_thumb_size' => array(
								'default'		=> '64',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Enter the max image size in pixels (optional)', 'pagelines'),
							),
							'filtering_max_image_attachment' => array(
								'default'		=> '600',
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Enter the maximum attachment size in pixels e.g. "300"</br>– OR –</br>as an attachment string e.g. "thumbnail"', 'pagelines'),
							),
							'filtering_thumb_frame' => array(
								'default'		=> 0,
								'type' 			=> 'check',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Frame To Images', 'pagelines'),
							),
							'filtering_max_height' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Max-Height To Images ( to keep them tidy when on top : use a pixel value )', 'pagelines'),
							),
						),
					),
					
					
					'filtering_ordering' => array(
						'type'		=> 'multi_option', 
						'title'		=> __('Box Ordering Options', 'pagelines'), 
						'shortexp'	=> __('Optionally control the ordering of the boxes', 'pagelines'),
						'exp'		=> __('The easiest way to order boxes is using a post type order plugin for WordPress. However, if you would like to do it algorithmically, we have provided these options for you.', 'pagelines'),
						'selectvalues'	=> array(
							
							'filtering_orderby' => array(
								'type'			=> 'select',
								'default'		=> 'ID',
								'inputlabel'	=> 'Order Boxes By (If Not With Post Type Order Plugin)',
								'selectvalues' => array(
									'ID' 		=> array('name' => __( 'Post ID (default)', 'pagelines') ),
									'title' 	=> array('name' => __( 'Title', 'pagelines') ),
									'date' 		=> array('name' => __( 'Date', 'pagelines') ),
									'modified' 	=> array('name' => __( 'Last Modified', 'pagelines') ),
									'rand' 		=> array('name' => __( 'Random', 'pagelines') ),							
								)
							),
							'filtering_order' => array(
									'default' => 'DESC',
									'type' => 'select',
									'selectvalues' => array(
										'DESC' 		=> array('name' => __( 'Descending', 'pagelines') ),
										'ASC' 		=> array('name' => __( 'Ascending', 'pagelines') ),
									),
									'inputlabel'=> __( 'Select sort order', 'pagelines'),
							),
						),
					),
					
					
					'filtering_styles' => array(
						'type'		=> 'multi_option', 
						'title' 		=> __( 'Custom CSS class', 'pagelines'),
						'shortexp' 		=> __( 'Add a custom CSS class to this set of boxes.', 'pagelines'),
						'selectvalues'	=> array(
							'filtering_class' => array(
								'default'		=> '',
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Add custom css class to these boxes (Hint: try "custom1" with "thumbs on top" mode)', 'pagelines'),
								),
							),
						),
			);
		
			$metatab_settings = array(
				'id' 		=> $this->id.'meta',
				'name' 		=> $this->name,
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);

			register_metatab($metatab_settings, $metatab_array);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id = null ) {    
			global $post;
			$currPage = $post->ID;
		
		// Options
			$filtering_per_row = ( ploption( 'filtering_col_number', $this->oset) ) ? ploption( 'filtering_col_number', $this->oset) : 3; 
			$filtering_post_type = ( ploption( 'filtering_post_type', $this->oset ) ) ? ploption( 'filtering_post_type', $this->oset ) : null;
			$filtering_type = ( ploption( 'filtering_post_type', $this->oset ) ) ? ploption( 'filtering_post_type', $this->oset ) : null;
			$filtering_tax = ( ploption( 'filtering_taxonomy', $this->oset ) ) ? ploption( 'filtering_taxonomy', $this->oset ) : null;
			$filtering_excludes = ( ploption( 'filtering_excludes', $this->oset ) ) ? ploption( 'filtering_excludes', $this->oset ) : '';
        
        
			
			//print_r($post_type_opts);
			
			$this->thumb_type = ( ploption( 'filtering_thumb_type', $this->oset) ) ? ploption( 'filtering_thumb_type', $this->oset) : 'inline_thumbs';	
			$this->thumb_size = ploption('filtering_thumb_size', $this->oset);
			$this->framed = ploption('filtering_thumb_frame', $this->oset);
						
			
		// Actions			
			// Set up the query for this page
				$filtering_orderby = ( ploption('filtering_orderby', $this->oset) ) ? ploption('filtering_orderby', $this->oset) : 'ID';
				$filtering_order = ( ploption('filtering_order', $this->oset) ) ? ploption('filtering_order', $this->oset) : 'DESC';
				
				$filtering_params = array( 'orderby'	=> $filtering_orderby, 'order' => $filtering_order );
				$filtering_params[ 'posts_per_page' ] = ( ploption('filtering_items', $this->oset) ) ? ploption('filtering_items', $this->oset) : $filtering_per_row;
				$filtering_params[ 'post_type' ] = $filtering_post_type;
				
				
			
				$filtering_params[ 'no_found_rows' ] = 1;

				$filtering = new WP_Query( $filtering_params );
				//echo '<pre>'.print_r($filtering).'</pre>';
				if(empty($filtering->posts)){
					echo setup_section_notify($this, __('<strong>No Posts Returned</strong><br>Please double check your query params', 	'pagelines') );
					return;
				}
				
					$excludes = '';
        if($filtering_excludes) {

         $exclude_terms = explode(", ", $filtering_excludes);
            foreach ($exclude_terms as $exclude_term) {

                $term = get_term_by( 'name',  $exclude_term,  $filtering_tax  );
                 $exclude_term_array[] = $term->term_id;         

            }

            $excludes= implode(", ", $exclude_term_array);
            
            $args2 = array('exclude'=>$excludes);	
	$terms = get_terms($filtering_tax, $args2);
      
       


        ?>

        <nav class="filtering-nav-wrap">
           <ul id="options" class="clearfix">
			<li><a href="#show-all" data-filter="*" class="selected">All</a></li>

		<?php 

			
			foreach( $terms as $term ){ ?>

		    <li><a href="#" data-filter=".<?php echo $term->slug?>"><?php echo $term->name?></a></li>

		    <?php } ?>
		

		</ul>
            <div class="clear"></div>
        </nav>

			<?php	} 
    		

			// Grid Args
				$args = array( 'per_row' => $filtering_per_row, 'callback' => array(&$this, 'draw_items') );

			// Call the Grid

					printf('<div class="filtering fix">%s</div>', grid( $filtering, $args ));
				
				
	}


	/**
	*
	* @TODO document
	*
	*/
	function draw_items($filtering, $args){ 
		global $post; 
        global $pagelines_ID;

		setup_postdata($filtering); 
		
		$oset = array('post_id' => $filtering->ID);
	 	$filtering_link = get_permalink($filtering->ID);
		$filtering_excerpt_len = ( ploption( 'filtering_excerpt_length', $this->oset ) ) ? ploption( 'filtering_excerpt_length', $this->oset ) : 20;
		$filtering_image_dim = ( ploption( 'filtering_max_image_attachment', $this->oset ) ) ? ploption( 'filtering_max_image_attachment', $this->oset ) : 600;
		$filtering_tax = ( ploption( 'filtering_taxonomy', $this->oset ) ) ? ploption( 'filtering_taxonomy', $this->oset ) : null;
			
		$filtering_box_icon_id = get_post_thumbnail_id($filtering->ID);
		if( is_numeric($filtering_image_dim) )
			$filtering_box_icon_ar = wp_get_attachment_image_src($filtering_box_icon_id, array($filtering_image_dim, 'auto', true), false);
		else
			$filtering_box_icon_ar = wp_get_attachment_image_src($filtering_box_icon_id, $filtering_image_dim, false);
		$filtering_box_icon = $filtering_box_icon_ar[0];
		
		if(!$filtering_box_icon){
			if(current_user_can( 'manage_options' ))
				$filtering_box_icon = plugins_url().'/pagelines-sections/anything-boxes/images/no_thumb.png';
			else
				$filtering_box_icon = null;
		}

				
		$filtering_image = ($filtering_box_icon) ? self::_get_box_image( $filtering, $filtering_box_icon, $filtering_link, $this->thumb_size) : '';
	
		$filtering_title_text = ($filtering_link) ? sprintf('<a href="%s">%s</a>', $filtering_link, $filtering->post_title ) : $filtering->post_title; 
	
		$filtering_title = do_shortcode(sprintf('<div class="abboxtitle"><h3>%s</h3></div>', $filtering_title_text));

		
		
		
  		if(ploption('filtering_show_excerpt', $this->oset)){
			if($filtering->post_excerpt != ''){
				$filtering_excerpt = $filtering->post_excerpt;
			}else {
				$filtering_excerpt = custom_trim_excerpt(apply_filters('the_content', $filtering->post_content), $filtering_excerpt_len ); 
			}
		}
		else $filtering_excerpt = '';
		
		
		
		$filtering_content = sprintf('<div class="abboxtext">%s %s </div>', $filtering_excerpt, pledit( $filtering->ID ));
			
		$filtering_info = ($this->thumb_type != 'only_thumbs') ? sprintf('<div class="abboxinfo fix bd">%s%s</div>', $filtering_title, $filtering_content) : '';
		
		$filtering_above_box = '';
		$filtering_above_box = apply_filters('ab_above_box', $filtering_above_box, $filtering->ID);
		$terms = get_the_terms($filtering->ID , $filtering_tax );
				$terms_string = '';
		 		foreach ( $terms as $term ) :     
          			$terms_string = $terms_string.$term->slug.' '; 
             	endforeach;   
	

		if($this->thumb_type == 'only_text') {
			return sprintf(
				'<div id="%s" class="item %s">%s<div class="media box-media %s"><div class="blocks box-media-pad">%s</div></div></div>', 
				'abbox_'.$filtering->ID,
				$terms_string,
				$filtering_above_box, 
				$this->thumb_type, 
				$filtering_info
			);
		}else {	
			return sprintf(
				'<div id="%s" class="item %s">%s<div class="media box-media %s"><div class="blocks box-media-pad">%s%s</div></div></div>', 
				'abbox_'.$filtering->ID,
				$terms_string,
				$filtering_above_box, 
				$this->thumb_type, 
				$filtering_image, 
				$filtering_info
			);
		}
	
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function _get_box_image( $filtering, $filtering_box_icon, $filtering_link = false, $filtering_thumb_size = false){
			global $pagelines_ID;
			
			$frame = ($this->framed) ? 'pl-imageframe' : '';
			
			if($this->thumb_type == 'inline_thumbs'){
				$max_width = ($filtering_thumb_size) ? $filtering_thumb_size : 65;
				$image_style = 'max-width: 100%';
				$wrapper_style = sprintf('width: 22%%; max-width:%dpx', $max_width);
				$wrapper_class = sprintf('abboxgraphic img %s', $frame);
			} else {
				$max_width = ($filtering_thumb_size) ? $filtering_thumb_size.'px' : '100%';
				$image_style = 'max-width: 100%';
				$wrapper_style = sprintf('max-width:%s', $max_width);
				$wrapper_class = sprintf('abboxgraphic %s', $frame);
			}
			
			// Make the image's tag with url
			$image_tag = sprintf('<img src="%s" alt="%s" style="%s" />', $filtering_box_icon, esc_html($filtering->post_title), $image_style);
			
			// If link for box is set, add it
			$image_output = ( $filtering_link ) ? sprintf('<a href="%s" title="%s">%s</a>', $filtering_link, esc_html($filtering->post_title), $image_tag ) : $image_tag;
			
			$wrapper = sprintf('<div class="%s" style="%s">%s</div>', $wrapper_class, $wrapper_style, $image_output );
			
			// Filter output
			return apply_filters('ab_box_image', $wrapper, $filtering);
	}


	
}


?>