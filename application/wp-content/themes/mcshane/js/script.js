( function( $ ) {

/* ----- MOBILE SAFARI & IE9+ VIEWPORT UNIT FIX ----- */
$(document).ready(function() {
	viewportUnitsBuggyfill.init();
});

/* ----- BACKGROUND COVER INITIALIZE ----- */
$(document).ready(function() {
	$('.background-cover').backgroundCover();
});

/* ----- FEATHERLIGHT INITIALIZE ----- */
$(document).ready(function() {
	$('[data-featherlight]').featherlight();
});

/* ----- LIGHTSLIDER INITIALIZE ----- */
$(document).ready(function() {
	$('.hero-slider').lightSlider({
		controls: false,
		mode: 'fade',
		item: 1,
		pause: 4000,
		speed: 1000,
		auto: true,
		pager: false,
		loop: true,
		adaptiveHeight: true,
		useCSS: true,
		enableTouch: false,
		freeMove: false
	});
	$('.feature-slider > ul').lightSlider({
		controls: true,
		gallery: true,
		autoWidth: true,
		mode: 'fade',
		item: 1,
        thumbItem: 6,
		thumbWidth: 160,
		speed: 1000,
        currentPagerPosition: 'left',
        slideMargin: 0,
		loop: true,
		adaptiveHeight: true,
		useCSS: true,
		thumbMargin: 0,
		galleryMargin: 4
	});
	$('.bg-slider > ul').lightSlider({
		controls: false,
		mode: 'fade',
		item: 1,
		pause: 7000,
		speed: 5000,
		auto: true,
		pager: false,
		loop: true,
		adaptiveHeight: true,
		useCSS: true,
		enableTouch: false,
		freeMove: false,
		enableTouch: false,
	});
	var propertySlider = $('.property-gallery > ul.slider').lightSlider({
		item: 3,
		slideMove: 1,
		controls: false,
		speed: 1000,
		pager: false,
        slideMargin: 0,
		freeMove: false
	});
	var propertySlides = $('#property-slides');
	$('.property-gallery ul.slider > li').click(function() {
		var pos = $(this).index(),
		imageSrc = $(this).attr('src');
		propertySlider.goToSlide(pos);
		$('li', propertySlides).stop().fadeOut()
		$('li:nth-child(' + (pos + 1) + ')', propertySlides).stop().fadeIn();
	});
	$('.experience > ul').lightSlider({
		controls: true,
		autoWidth: false,
		pager: false,
		mode: 'slide',
		item: 3,
		speed: 1000,
        slideMargin: 0,
		loop: false,
		adaptiveHeight: true,
		useCSS: true
	});
});

/* ----- MAIN NAV FUNCTIONS ----- */
$(document).ready(function() {
	function initMenu() {
		$('ul.sub-menu').wrap('<div class="drop-down"><div class="container"></div></div>'); // WRAP WORDPRESS-GENERATED SUB-MENUS
	}
	$.when(initMenu()).done(function() {
		function calculateLeft() {
			if (window.matchMedia('(min-width: 961px)').matches) {
				$('ul.sub-menu').each(function() {
					var theSubMenu = $(this);
					headerContainer = $('header > .container'),
					headerContainerPostiion = headerContainer.offset(),
					parentListItem = theSubMenu.closest('header nav ul#main > li'),
					parentListItemPosition = parentListItem.offset(),
					calloutLink = $('.callout', theSubMenu),
					calloutPosition = parentListItemPosition.left - headerContainerPostiion.left;
					
					theSubMenu.css('left', parentListItemPosition.left + 'px');
					calloutLink.css('left', -calloutPosition + 'px');
				});
			}
			else {
				$('ul.sub-menu').each(function() {
					var theSubMenu = $(this);
					calloutLink = $('.callout', theSubMenu);
					
					theSubMenu.css('left', '200px');
					calloutLink.css('left', '-200px');
				});
			}
		}
		calculateLeft();
		$(window).resize(function() {
			calculateLeft();
		});
	});
});

/* ----- MOBILE MENU FUNCTIONS ----- */
$(document).ready(function() {
	var mobileButton = $('#mobile-menu'),
	mobileMenu = $('header nav ul#main'),
	menuEntry = $('header nav ul#main > li');
	
	mobileButton.click(function() {
		mobileMenu.toggleClass('active');
	});
	
	menuEntry.click(function() {
		$(this).toggleClass('active');
		$('> a', this).trigger('mouseover');
	});
});

/* ----- LEFT NAV FUNCTIONS ----- */
$(document).ready(function() {
	
	$('.left nav ul li.active .children').each( function(){
		if( $(this).is(':visible') ){
			$(this).siblings('.toggle').html('-');
		}
	});
	
	$('.left nav .toggle').click(function() {
		var theToggle = $(this),
		theSubMenu = theToggle.siblings('ul');
		if (theSubMenu.is(':visible')) {
			theToggle.html('+');
		}
		else {
			theToggle.html('-');
		}
		theSubMenu.slideToggle();
	});
});

/* ----- BODY ACCORDION FUNCTIONS ----- */
$(document).ready(function() {
	var accordionLink = $('.accordion-toggle'),
	accordionContent = $('.accordion-content');
	accordionLink.click(function(e) {
		e.preventDefault();
		if (accordionLink.hasClass('active')) {
			accordionLink.html('Read More');
		}
		else {
			accordionLink.html('Read Less');
		}
		accordionLink.toggleClass('active');
		accordionContent.slideToggle();
	});
});

/* ----- FOOTER ACCORDION FUNCTIONS ----- */
$(document).ready(function() {
	var footerToggle = $('.footer-toggle'),
	hiddenFooter = $('.hidden-footer');
	footerToggle.click(function(e) {
		e.preventDefault();
		if (footerToggle.hasClass('active')) {
			footerToggle.html('Read More');
		}
		else {
			footerToggle.html('Read Less');
		}
		footerToggle.toggleClass('active');
		hiddenFooter.slideToggle();
	});
});

/* ----- GOOGLE MAPS API ----- */
$(document).ready(function() {
	if(typeof lat !== 'undefined') {
		var map,
		location = new google.maps.LatLng(lat, long);
		
		var MY_MAPTYPE_ID = 'mcshane';
		
		function initialize() {
		
		  var featureOpts = [
		  {
			"elementType": "geometry",
			"stylers": [
			  { "saturation": -100 }
			]
		  },{
			"featureType": "road.highway",
			"elementType": "geometry",
			"stylers": [
			  { "color": "#b03a0a" }
			]
		  },{
			"elementType": "labels",
			"stylers": [
			  { "saturation": -100 }
			]
		  }
		];
		
		  var mapOptions = {
			zoom: 14,
			center: location,
			disableDefaultUI: true,
			zoomControl: true,
			scrollwheel: false,
			mapTypeControlOptions: {
			  mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
			},
			mapTypeId: MY_MAPTYPE_ID
		  };
		
		  map = new google.maps.Map(document.getElementById('map-canvas'),
			  mapOptions);
		
		  var styledMapOptions = {
			name: 'McShane'
		  };
		  
		  var marker = new google.maps.Marker({
			  position: location,
			  map: map,
			  icon: mc_scripts.theme_images_url + 'map-marker.svg'
		  });
		
		  var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
		
		  map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
	}
});


/**
 * darrinb edits
 *
 */
$(document).ready(function() {


});
 

} )( jQuery );
