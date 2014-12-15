if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";

	var $landpagediv = $('#landingpagebgrotatordiv');
	$landpagediv.hide();
	$landpagediv.removeClass('hide-if-js');
	var $template_select = $('select[name="page_template"]');

	if( 'page-templates/landing-page.php' == $template_select.val() ){
		$landpagediv.show();
	}

	$template_select.on('change', function(){
		var selected_type = $(this).val();
		if( 'page-templates/landing-page.php' === selected_type ){
			$landpagediv.fadeIn();
		} else {
			$landpagediv.fadeOut();
		}
	});

});