
jQuery(document).ready(function(){
	
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
	      mycontainer.find('.item').each(function(){
	        var $this = jQuery(this),
	            number = parseInt( $this.find('.item-excerpt').text(), 10 );
	        if ( number % 7 % 2 === 1 ) {
	          $this.addClass('width2');
	        }
	        if ( number % 3 === 0 ) {
	          $this.addClass('height2');
	        }
	      });

	     
	       

	
	
		
 	

});