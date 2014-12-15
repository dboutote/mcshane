if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";
		
	var $landpagediv = $('#landingpagediv');
	var $landingpagetwodiv = $('#landingpagetwodiv');
	$('#pageparentdiv').after($landpagediv);
	$landpagediv.after($landingpagetwodiv);
	$landpagediv.removeClass('hide-if-js');
	var $template_select = $('select[name="page_template"]');
	
	
	$('.lp-settings').each(function(index, value){		
		var $this = $(this);		
		if( $('.entries-type', $this).hasClass('type-selected') ){
			$this.closest('.postbox').show();
		} else {
			$this.closest('.postbox').hide();
		}
	});
	
	if( 'page-landing-page.php' == $template_select.val() ){
		$landpagediv.show();		
	}
	
	
	$('.addlplist').each(function(){
		var $link = $(this);		
		var $target_div = $($link.attr('href'));
		var $parent = $link.closest('.lp-settings');
		var $type_select = $('select.entries-type', $parent);		
		
		if( $target_div.is(":visible") ){
			$link.hide();
		} else {
			$link.show();
		}
		
		$link.on('click', function(e){	
			if( !$target_div.is(":visible") && '' !== $type_select.val() ){
				$target_div.fadeIn();
			}		
			e.preventDefault();
		});		
		
		
	});	
	
	$template_select.on('change', function(){
		var selected_type = $(this).val();
		if( 'page-landing-page.php' === selected_type ){
			$landpagediv.fadeIn();
		} else {
			$landpagediv.fadeOut();
		}
	});


	$('select.entries-type').on('change', function(){
	
		var $this = $(this);
		var $parent = $this.closest('.lp-settings');
		var $taxplaceholder = $('.taxplaceholder', $parent);
		var $tax_select = $('select.entries-tax', $parent);
		
		var selected_type = $(this).val();
				
		// ajax all the things
		var request = $.ajax({
			type : "POST",
			url : lpJax.ajaxurl,
			data : {plugin: "landing-page", util_action: "get_type_taxonomies", action:"setup_type_taxonomies", post_type:selected_type},
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
		}).always(function(){			
		});

	});
	

	

});