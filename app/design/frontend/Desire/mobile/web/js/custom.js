require(['jquery','owl_carousel'],function($){
	
	(function($){
		$(function(){	
				var scroll = $(document).scrollTop();
				var headerHeight = $('.page-header').outerHeight();
				//console.log(headerHeight);
				
				$(window).scroll(function() {
					// scrolled is new position just obtained
					var scrolled = $(document).scrollTop();
									
					// optionally emulate non-fixed positioning behaviour
				
					if (scrolled > headerHeight){
						$('.page-header').addClass('off-canvas');
						$('#search-content').addClass('off-canvas');
					} else {
						$('.page-header').removeClass('off-canvas');
						$('#search-content').removeClass('off-canvas');
					}

					if (scrolled > scroll){
						 // scrolling down
						 $('.page-header').removeClass('fixed');
						 $('#search-content').removeClass('fixed');
					  } else {
						  //scrolling up
						  $('.page-header').addClass('fixed');
						  $('#search-content').addClass('fixed');
					}
					scroll = $(document).scrollTop();	
				 });
		
		
		});
	})(jQuery);  
	
	
	$(document).ready(function($) {
		var i =0;
		if(i == 0){
			//var signuplink = ' | <a href="#social-login-popup" class="social-login" data-effect="mfp-zoom-in">Sign Up</a>';
			var signuptext = $('.header.links li:last-child');
			var firstItem = $('.header.links li:first-child');
			if(!$(firstItem).hasClass('customer-welcome')){
				$('.header.links li:first-child a').text('Log In');
				//$('.header.links li a.header-signup-link').appendTo(firstItem);
			}
			i++;
		}
		$(".page.messages").delay(5000).fadeOut();
		$("#mtImageContainer a.MagicZoom figure img").attr("style","width:auto !important");
		$('#mtImageContainer a.MagicZoom figure img').on('load', function () {
			$("#mtImageContainer a.MagicZoom figure img").attr("style","width:auto !important");
		});
	});  
	
	var winwidth = jQuery(window).width() < 768;
	if (winwidth){
		$('#homepage-category-carousel').owlCarousel({
			stagePadding: 30,
			margin:15,
			nav:false,
			dots:true,
			items:1
		});
		$('#mobile-collectors-carousel').owlCarousel({
			stagePadding: 30,
			margin:0,
			nav:false,
			dots:true,
			items:1
		});
	}
	
	$('#testimonial-slider').owlCarousel({
		loop:true,
		margin:4,
		nav:false,
		dots:true,
		items:1,
		autoHeight:true
	});
	/*
	$('#product-options-wrapper > .fieldset').owlCarousel({
		stagePadding: 30,
		loop:false,
		margin:0,
		nav:false,
		dots:false,
		items:4,
		autoWidth:true
	});*/
	$('#popular .products-grid').owlCarousel({
		loop:true,
		margin:25,
		nav:false,
		dots:false,
		items:1,
		stagePadding: 30
	});
	/********** Search Button JS ***********/

	$("#search-icon").click(function(){
		$("#search-content").toggleClass("active");
	});
	
	$(".block.block-search").click(function(e) {
		e.stopPropagation();

	});
	
	$(document).click(function() {
		// hide search box
		$("#search-content").removeClass("active");
	});
	
	/***** Filter Attributes Js***/
	/*jQuery(document).on('click','.color .swatch-attribute-selected-option', function (argument) {
		jQuery('.color .swatch-attribute-options').slideToggle();
	});
	jQuery(document).on('click','.color .swatch-attribute-options .swatch-option',function(argument) {
		jQuery('.color .swatch-attribute-selected-option').addClass('selected').attr('style',jQuery(this).attr('style'));
		jQuery('.color .swatch-attribute-options').slideUp();
	});
	*/
	
	/********** Sort Option box *********/
	
	$(".sorter-label-box").click(function(){
		$(this).parent().toggleClass("active");
		$(".sorter-option-box").fadeToggle("slow");
	});
	
});

window.smoothScroll = function(target) {
    var scrollContainer = target;
    do { //find scroll container
        scrollContainer = scrollContainer.parentNode;
        if (!scrollContainer) return;
        scrollContainer.scrollTop += 1;
    } while (scrollContainer.scrollTop == 0);

    var targetY = 0;
    do { //find the top of target relatively to the container
        if (target == scrollContainer) break;
        targetY += target.offsetTop;
    } while (target = target.offsetParent);

    scroll = function(c, a, b, i) {
        i++; if (i > 30) return;
        c.scrollTop = a + (b - a) / 30 * i;
        setTimeout(function(){ scroll(c, a, b, i); }, 20);
    }
    // start scrolling
    scroll(scrollContainer, scrollContainer.scrollTop, targetY, 0);
}
function openTab(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.visibility = "hidden";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
		tabcontent[i].className = tabcontent[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.visibility = "visible";
	document.getElementById(cityName).className += " active";
    evt.currentTarget.className += " active";
}