if (typeof jQuery === "undefined") {
    throw new Error("script.js requires jQuery");
}

jQuery(function ($) {

    "use strict";

	// append gallery item to gallery list
	function addItemToGalleryList($list_item_id, $list_item_name, $gallery_list){
		var $list_item = '<tr data-postid="' + $list_item_id + '" data-postname="' + $list_item_name + '">';
				$list_item += '<td><input class="hidden" type="hidden" value="' + $list_item_id +'" name="gallery_items[' + $list_item_id + '][ID]" />';
				$list_item += '<input class="small-text" type="text" value="" name="gallery_items[' + $list_item_id + '][order]" /></td>';
				$list_item += '<td><input class="text readonly" type="text" value="' + $list_item_name + '" name="gallery_items[' + $list_item_id + '][name]" readonly="readonly"/></td>';
				$list_item += '<td><input class="checkbox" type="checkbox" value="1" name="gallery_items[' + $list_item_id + '][featured]" /></td>';
				$list_item += '<td><a class="del">[x]</a></td>';
			$list_item += '</tr>';
		$gallery_list.append($list_item);
	};

	function updateTableStripes(table){
		$("tbody tr", table).removeClass("alternate");
		$("tbody tr:even", table).addClass("alternate");
	};

	var $max_pages,
		$cur_pg_val,
		$nav_pg_val,
		$settings_div = $('#gallery-settings-column'),
		$nav_parent = $('#gallery-settings-nav'),
		$controls_parent = $('#gallery-settings-controls'),
		$nav_spinner = $('.spinner', $nav_parent),
		$submit_spinner = $('.spinner', $controls_parent),
		$gallery_list = $('#gallery-management-list'),
		$posttype_select = $('#gallery-settings-posttype'),
		$selected_posttype,
		$gallery_checklist = $('#gallery-settings-checklist');

	$max_pages = parseInt( $('input[name="gallery_settings_max_pages"]', $settings_div).val(), 10 ); // Always use radix
	$cur_pg_val = parseInt( $('input[name="gallery_settings_curr_page"]', $settings_div).val(), 10 ); // Always use radix

	if( $cur_pg_val === 1){
		$('.prev', $settings_div).addClass('disabled');
	}
	if( $cur_pg_val === $max_pages){
		$('.next', $settings_div).addClass('disabled');
	}

	// when selecting a new post type
	$posttype_select.on('change', function(){
						
		var $selected_posttype = $(this).val();
		
		console.log($selected_posttype);
				
		// ajax all the things
		var request = $.ajax({
			type : "POST",
			url : cgJax.ajaxurl,
			data : {plugin: "cpt-gallery", util_action: "get_new_posts", action:"setup_gallery_new_posts", post_type:$selected_posttype},
			dataType : 'json'
		}).done(function( response ) {
		
			// -1 means an error, 1 means success
			$gallery_checklist.fadeOut(function(){
				$gallery_checklist.empty().append(response.notice);
				$(this).fadeIn();
			});											
			
			$nav_spinner.hide();
			
		}).fail(function(response){			
		}).always(function(){});

	});
	
	
	// navigation
	$('.meta-nav', $settings_div).on('click', function(){
		$nav_spinner.show();
		$cur_pg_val = parseInt( $('input[name="gallery_settings_curr_page"]', $settings_div).val(), 10 ); // Always use radix

		$nav_pg_val = ( $(this).is('.next') ) ? ($cur_pg_val + 1) : ($cur_pg_val - 1) ;

		if( $nav_pg_val <= 1){
			$nav_pg_val = 1;
		}

		if( $nav_pg_val === 1){
			$('.prev', $settings_div).addClass('disabled');
		} else {
			$('.prev', $settings_div).removeClass('disabled');
		}

		if($nav_pg_val >= $max_pages ){
			$nav_pg_val = $max_pages;
			$('.next', $settings_div).addClass('disabled');
		} else {
			$('.next', $settings_div).removeClass('disabled');
		}

		$('input[name="gallery_settings_curr_page"]', $settings_div).val($nav_pg_val);
		
		$selected_posttype = $posttype_select.val();
		
		// ajax all the things
		var request = $.ajax({
			type : "POST",
			url : cgJax.ajaxurl,
			data : {plugin: "cpt-gallery", util_action: "get_paged_posts", action:"setup_gallery_paged_posts", post_type:$selected_posttype, paged:$nav_pg_val},
			dataType : 'json'
		}).done(function( response ) {
		
			// -1 means an error, 1 means success
			$gallery_checklist.fadeOut(function(){
				$gallery_checklist.empty().append(response.notice);
				$(this).fadeIn();
			});											
			
			$nav_spinner.hide();
			
		}).fail(function(response){			
		}).always(function(){});

		
	});


	// adding gallery items
	$('#submit-gallery-posttype').on('click', function(){

		var $menu_items = {},
			$checkboxes = $('#gallery-settings').find('#gallery-settings-checklist li input:checked');

		// If no items are checked, bail.
		if ( !$checkboxes.length ) {
			return false;
		}

		$submit_spinner.show();

		$checkboxes.each(function(){
			var $list_item = $(this),
				$list_item_name = $list_item.data('postname'),
				$list_item_id = $list_item.val();
				$menu_items[$list_item_id] = $list_item_name;
				addItemToGalleryList($list_item_id, $list_item_name, $gallery_list);
		});

		$submit_spinner.hide();
		$checkboxes.removeAttr('checked');
		updateTableStripes($gallery_list);

	});
	
	// removing gallery items
	$($gallery_list).on('click', '.del', function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		updateTableStripes($gallery_list);
	});
	
	
	// navigating the gallery list
	



});