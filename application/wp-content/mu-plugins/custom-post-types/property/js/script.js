if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";
	
	var $landpagediv = $('#propertygallerydiv');
	$landpagediv.hide();
	$landpagediv.removeClass('hide-if-js');
	var $template_select = $('select[name="page_template"]');
	var $selected_tax_title = $('#selected-tax-title');

	if( 'page-templates/property-gallery.php' == $template_select.val() ){
		$landpagediv.show();
	}

	$template_select.on('change', function(){
		var selected_type = $(this).val();
		if( 'page-templates/property-gallery.php' === selected_type ){
			$landpagediv.fadeIn();
		} else {
			$landpagediv.fadeOut();
		}
	});
	
});