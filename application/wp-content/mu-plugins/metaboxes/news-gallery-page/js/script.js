if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";

	var $landpagediv = $('#newsgallerydiv');
	$landpagediv.hide();
	$landpagediv.removeClass('hide-if-js');
	var $template_select = $('select[name="page_template"]');
	var $selected_tax_title = $('#selected-tax-title');

	if( 'page-templates/news-gallery.php' == $template_select.val() ){
		$landpagediv.show();
	}

	$template_select.on('change', function(){
		var selected_type = $(this).val();
		if( 'page-templates/news-gallery.php' === selected_type ){
			$landpagediv.fadeIn();
		} else {
			$landpagediv.fadeOut();
		}
	});
	
	$('select.tax-type').on('change', function(){
	
		var $this = $(this);
		var $parent = $this.closest('.ng-settings');
		var $taxplaceholder = $('.taxplaceholder', $parent);
		var $tax_select = $('select.tax-terms', $parent);
		
		var selected_type = $(this).val();
		
		console.log(selected_type);
				
		// ajax all the things
		var request = $.ajax({
			type : "POST",
			url : ngJax.ajaxurl,
			data : {plugin: "news-gallery", util_action: "get_news_terms", action:"setup_news_terms", tax_type:selected_type},
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
					$selected_tax_title.text(response.tax_name + ' ');
					$tax_select.empty().append(response.notice);
					$(this).fadeIn();
				});											
			}
			
		}).fail(function(response){			
		}).always(function(){			
		});

	});	

});