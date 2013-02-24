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
		wp_enqueue_script( 'equalize', $this->base_url.'/js/equalizecols.js');
		wp_enqueue_script( 'easing', $this->base_url.'/js/jquery.easing.js');
		
		}

	function section_head( $clone_id ) {  

		?>

  <script>
  jQuery(document).ready(function (){

		jQuery(".inner-item").equalizeCols(); // Make row heights equal
		
		// Isotope Center Container

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


	  // Call Isotope
	 
	  	var mycontainer = jQuery('.filtering');

	      // add randomish size classes
	      mycontainer.find('.filtering .item').each(function(){
	        var $this = jQuery(this),
	            number = parseInt( $this.find('.item-info.item-title').text(), 10 );
	        if ( number % 7 % 2 === 1 ) {
	          $this.addClass('width2');
	        }
	        if ( number % 3 === 0 ) {
	          $this.addClass('height2');
	        }
	      });

		
	      mycontainer.isotope({
		      itemSelector: '.item' 
		       
		  });
			
        
	   // filter items when filter link is clicked
	jQuery('#options a').click(function(){
	  var selector = jQuery(this).attr('data-filter');
	  mycontainer.isotope({ filter: selector });
	  return false;  
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
					'title'		=> __('Filtering Setup Options', 'filtering'), 
					'shortexp'	=> __('Basic setup options for handling of filltering.', 'filtering'),
					'exp'			=> __( '', 'filtering'),
					'selectvalues'	=> array(
						'filtering_post_type' => array(
							'default'		=> 'posts',
							'type' 			=> 'select',
							'selectvalues' 			=> $post_type_array,
							'inputlabel'	=> __( 'Select your post type to filter', 'filtering'),
						), 
						'filtering_taxonomy' => array(
							'default'		=> 'categories',
							'type' 			=> 'select',
							'selectvalues' => $taxonomy_array,
							'inputlabel'	=> __( 'Select taxonomy. Make sure the taxonomy goes with the post type, i.e. categories with posts', 'filtering'),
						), 
						'filtering_excludes' => array(
							'default'		=> '',
							'type' 			=> 'text',
							'inputlabel'	=> __( 'Enter Excluded Categories/Terms  ( if multiple, separate using a comma )', 'filtering'),				

						),
						
						'filtering_item_width' => array(
							'type' 			=> 'text_small',
							'default'		=> '250px',
							
							'inputlabel' 		=> __( 'Width of  each item item. Default is 250px. Enter just the width value, px will be added for you.' , 'filtering'),
						),
						'filtering_image_type' => array(
								'type' 		=> 'select',
								'default'	=> 'thumbs',
								'selectvalues'	=> array(
										'thumbs'	=> array('name' => __( 'Show Image on Top', 'filtering') ), 
										'only_thumbs'	=> array('name' => __( "Show Only the Image", 'filtering') ),
										'only_text'	=> array('name' => __( "Text Only, no image", 'filtering') )

									), 
								'inputlabel' => __( 'Display Image (default is Show Image on Top)', 'filtering'),				

						),
					 
						'filtering_items' => array(
							'default'		=> '6',
							'type' 			=> 'text_small',
							'size'			=> 'small',
							'inputlabel' 	=> __( 'Maximum Boxes To Show', 'filtering'),
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
									'default'		=> 0,
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
						'title'		=> __('Box Image Options', 'filtering'), 
						'shortexp'	=> __('Options for formatting box images.', 'filtering'),
						'exp'		=> __('', 'filtering'),
						'selectvalues'	=> array(
							
							
							'filtering_thumb_size' => array(
								'default'		=> '64',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Enter the max image size in pixels (optional)', 'filtering'),
							),
							'filtering_max_image_attachment' => array(
								'default'		=> '600',
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Enter the maximum attachment size in pixels e.g. "300"</br>– OR –</br>as an attachment string e.g. "thumbnail"', 'filtering'),
							),
							'filtering_thumb_frame' => array(
								'default'		=> 0,
								'type' 			=> 'check',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Frame To Images', 'filtering'),
							),
							'filtering_max_height' => array(
								'default'		=> '',
								'type' 			=> 'text_small',
								'size'			=> 'small',
								'inputlabel' 		=> __( 'Add A Max-Height To Images ( to keep them tidy when on top : use a pixel value )', 'filtering'),
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
						'shortexp' 		=> __( 'Add a custom CSS class to this set of boxes.', 'filtering'),
						'selectvalues'	=> array(
							'filtering_class' => array(
								'default'		=> '',
								'type' 			=> 'text',
								'size'			=> 'small',
								'inputlabel' 	=> __( 'Add custom css class to these boxes (Hint: try "custom1" with "thumbs on top" mode)', 'filtering'),
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

	function section_template( $clone_id=null) {

        global $post; global $filtering_ID;
        $oset = array('post_id' => $filtering_ID, 'clone_id' => $clone_id);
        $per_row = ( ploption( 'filtering_col_number', $this->oset) ) ? ploption( 'filtering_col_number', $this->oset) : 3;
       	$filtering_type = ( ploption( 'filtering_post_type', $this->oset ) ) ? ploption( 'filtering_post_type', $this->oset ) : null;
		$filtering_tax = ( ploption( 'filtering_taxonomy', $this->oset ) ) ? ploption( 'filtering_taxonomy', $this->oset ) : null;
		$filtering_class = ( ploption( 'filtering_class', $this->oset ) ) ? ploption( 'filtering_class', $this->oset ) : null;
		

		if($filtering_tax!='categories'){
			printf( '<div class="section-filtering %s">' , $filtering_class);
				$this->draw_taxonomy_filtering();
			echo '</div>';
		}else {
			printf( '<div class="section-filtering %s">' , $filtering_class);
				$this->draw_category_filtering();
			echo '</div>';	
		}
        
        

    }
  
    
   	
   	 // Draw Category Filtering

    function draw_category_filtering($clone_id=null) {

        global $post; 
        global $filtering_ID;

        $oset = array('post_id' => $filtering_ID);
        $filtering_orderby = ( ploption( 'filtering_orderby', $this->oset ) ) ? ploption( 'filtering_orderby', $this->oset ) : 'ID';
		$filtering_order = ( ploption( 'filtering_order', $this->oset ) ) ? ploption( 'filtering_order', $this->oset ) : 'DESC';
		$filtering_excludes = ( ploption( 'filtering_excludes', $this->oset ) ) ? ploption( 'filtering_excludes', $this->oset ) : '';
        $filtering_width = (ploption('filtering_item_width')) ? ploption('filtering_item_width').'px' : '250px';
		$filtering_image = (ploption('filtering_image_type')) ? ploption('filtering_image_type') : 'thumbs';
		
        $excludes = '';
        if($filtering_excludes) {

		 $exclude_cats = explode(", ", $filtering_excludes);
			foreach ($exclude_cats as &$exclude_cat) {
				 $cat_id = get_category_by_slug($exclude_cat); 
  				$exclude_cat =$cat_id->term_id;

			}

			$excludes= implode(", ", $exclude_cats);
		}
		
                          
        $categories = get_categories('exclude='.$excludes.' ');
        
      
		$include = array();

		foreach ( $categories as $category )
		    $include[] = $category->term_id;


        $filtering_excerpt_len = (ploption('filtering_excerpt_length',$oset)) ? (ploption('filtering_excerpt_length',$oset)) : '150';
        
   		$args =  array( 'post_type' => 'post', 'posts_per_page' => -1, 'orderby'=>$filtering_orderby , 'order'=> $filtering_order, 'category__in' => $include );
		$filtering = new WP_Query( $args );
		
      

        
        ?>

        <nav class="filtering-nav-wrap">
           <ul id="options" class="clearfix">
			<li><a href="#show-all" data-filter="*" class="selected">All</a></li>

		<?php 

			
			foreach( $categories as $category ){ ?>

		    <li><a href="#" data-filter=".<?php echo $category->slug?>"><?php echo $category->name?></a></li>

		    <?php } ?>
		

		</ul>
            <div class="clear"></div>
        </nav>

        <section class="filtering clearfix">
        <?php

            while ($filtering->have_posts() ) : $filtering->the_post();

            if(ploption('filtering_show_excerpt', $this->oset)){
			if($post->post_excerpt != ''){
				$filtering_excerpt = $post->post_excerpt;
			}else {
				$filtering_excerpt = custom_trim_excerpt(apply_filters('the_content', $post->post_content), $filtering_excerpt_len ); 
			}
		}
		else $filtering_excerpt = '';
            	$categories = get_the_category( $post->ID );
				$categories_string = '';
		 		foreach ( $categories as $category ) :     
          			$categories_string = $categories_string.$category->slug.' '; 
             	endforeach;   
	
		

                printf( '<div class="item %s " style="width: %s; margin-right: 10px;">' , $categories_string, $filtering_width);
	?>
	<div class="inner-item">

			<?php
		
	if($filtering_image != 'only_text')	{
	
 		if ( has_post_thumbnail() ) {
 			?>
		
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
	
		<?php
} else {


		$image_src = plugins_url().'/pagelines-sections/filtering/images/no_thumb.png';
		$permalink = get_permalink($post->ID);
		$title = get_the_title($post->ID);

		printf('<a href="%s"><img src="%s" alt="%s" /></a>' , $permalink, $image_src,  $title);
 } 

	


		
	
	 }
	if($filtering_image != 'only_thumbs')	{
	?>
		<div class="item-info">
			<div class="item-title">
				<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			</div>

	<?php
		printf('<div class="item-excerpt">%s</div>', $filtering_excerpt );
	?>
	</div>
	<?php }
	?>
	</div>
	
</div>
	<?php
	endwhile;
	
	?>
</section>
<?php }

// Taxonomy items

    function draw_taxonomy_filtering($clone_id=null) {
    	global $post; 
        global $filtering_ID;
    	$filtering_type = ( ploption( 'filtering_post_type', $this->oset ) ) ? ploption( 'filtering_post_type', $this->oset ) : null;
		$filtering_tax = ( ploption( 'filtering_taxonomy', $this->oset ) ) ? ploption( 'filtering_taxonomy', $this->oset ) : null;
		$filtering_orderby = ( ploption( 'filtering_orderby', $this->oset ) ) ? ploption( 'filtering_orderby', $this->oset ) : 'ID';
		$filtering_order = ( ploption( 'filtering_order', $this->oset ) ) ? ploption( 'filtering_order', $this->oset ) : 'DESC';
		$filtering_excludes = ( ploption( 'filtering_excludes', $this->oset ) ) ? ploption( 'filtering_excludes', $this->oset ) : '';
        $filtering_width = (ploption('filtering_item_width')) ? ploption('filtering_item_width').'px' : '250px';
		$filtering_image = (ploption('filtering_image_type')) ? ploption('filtering_image_type') : 'thumbs';
		
        
	 
       
        

        $oset = array('post_id' => $filtering_ID);

        
        $filtering_excerpt_len = (ploption('filtering_excerpt_length',$oset)) ? (ploption('filtering_excerpt_length',$oset)) : '150';
        
        $postCategories = get_the_category($post->ID);
			foreach ( $postCategories as $postCategory ) {
			$myCategories[] = get_term_by('id', $postCategory->cat_ID, 'category');
			}

		$excludes = '';
        if($filtering_excludes) {

         $exclude_terms = explode(", ", $filtering_excludes);
            foreach ($exclude_terms as $exclude_term) {

                $term = get_term_by( 'name',  $exclude_term,  $filtering_tax  );
                 $exclude_term_array[] = $term->term_id;         

            }

            $excludes= implode(", ", $exclude_term_array);
           

        }
         
      $args2 = array('exclude'=>$excludes);	
	$terms = get_terms($filtering_tax, $args2);
      $include = array();

		foreach ( $terms as $term )
		    $include[] = $term->term_id;




 $args = array(
 	'post_type' => $filtering_type,
 	'tax_query' => array(
		array(
			'taxonomy' => $filtering_tax,
			'field' => 'id',
			'terms' => $include
			
		)
		));   
        

 //  	$args = array( 'post_type' => $filtering_type, 'posts_per_page' => -1, 'orderby'=>$filtering_orderby , 'order'=> $filtering_order );
		$filtering = new WP_Query( $args );
	
      
       


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

        <div class="filtering">
        <?php

            while ($filtering->have_posts() ) : $filtering->the_post();
            	if(ploption('filtering_show_excerpt', $this->oset)){
			if($post->post_excerpt != ''){
				$filtering_excerpt = $post->post_excerpt;
			}else {
				$filtering_excerpt = custom_trim_excerpt(apply_filters('the_content', $post->post_content), $filtering_excerpt_len ); 
			}
		}
		else $filtering_excerpt = '';
            	$terms = get_the_terms($post->ID , $filtering_tax );
				$terms_string = '';
		 		foreach ( $terms as $term ) :     
          			$terms_string = $terms_string.$term->slug.' '; 
             	endforeach;   
	
		
             	printf( '<div class="item %s " style="width: %s; margin-right: 10px;">' , $terms_string, $filtering_width);
	
               
	
	
	
	?>
	<div class="inner-item">

			<?php
			
	if($filtering_image != 'only_text')	{
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );

	
 		if ( $image ) {
 			?>
		
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
	
		<?php
} else {


		$image_src = plugins_url().'/pagelines-sections/filtering/images/no_thumb.png';
		$permalink = get_permalink($post->ID);
		$title = get_the_title($post->ID);

		printf('<a href="%s"><img src="%s" alt="%s" /></a>' , $permalink, $image_src,  $title);
 } 

		
	
	 }

	if($filtering_image != 'only_thumbs')	{
	?>
		<div class="item-info">
			<div class="item-title">
				<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			</div>

	<?php
		printf('<div class="item-excerpt">%s</div>', $filtering_excerpt );
	?>
	</div>
	<?php }
	?>
	</div>
	
</div>
	<?php
	endwhile;
	
	?>
</div>
<?php }

}		