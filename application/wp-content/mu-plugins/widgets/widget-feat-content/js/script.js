if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";
	
	// identify the select element for post types in the widget box
	
	$('.lp-settings').each(function(index, value){		
		var $this = $(this);		
		if( $('.entries-type', $this).hasClass('type-selected') ){
			$this.closest('.postbox').show();
		} else {
			$this.closest('.postbox').hide();
		}
	});
	



	$('.widgets-php').on('change', 'select.wfc-entries-type', function(){
		
		var $this = $(this);
		var $parent = $this.closest('.wfc-content-settings');
		var $taxplaceholder = $('.taxplaceholder', $parent);
		var $tax_select = $('select.wfc-entries-tax', $parent);
				
		var selected_type = $(this).val();
				
		// ajax all the things
		var request = $.ajax({
			type : "POST",
			url : fcwJax.ajaxurl,
			data : {plugin: "widget-feat-content", util_action: "get_type_taxonomies", action:"setup_type_taxonomies", post_type:selected_type},
			dataType : 'json'
		}).done(function( response ) {
			// -1 means an error, 1 means success	
			if('-1' === response.code) {
				$taxplaceholder.fadeOut(function(){
					$('option:not(:first)', $tax_select).remove();
				});
			}
			
			if('1' === response.code) {
				$taxplaceholder.fadeOut(function(){
					$tax_select.empty().append(response.notice);
					$(this).fadeIn();
				});											
			}
			
		}).fail(function(response){			
		}).always(function(){});

	});
	

	

});