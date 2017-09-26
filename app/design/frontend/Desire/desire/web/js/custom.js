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
		$("#social-login-popup .input-text").focus(function(){
			$(this).parent().addClass("move-to-top");
		}).blur(function(){
			if(!$(this).val()){
				$(this).parent().removeClass("move-to-top");
			}
		})
	});  
	
	$('#testimonial-slider').owlCarousel({
		loop:true,
		margin:0,
		nav:true,
		dots:false,
		items:1
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
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.visibility = "visible";
    evt.currentTarget.className += " active";
} 