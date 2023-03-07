/* Add Custom Code Jquery
 ========================================================*/
$(document).ready(function(){
	// Fix hover on IOS
	$('body').bind('touchstart', function() {}); 
	/*
	// video
	$(document).ready(function() {
	    $('.home23-video').magnificPopup({
	      type: 'iframe',
	      iframe: {
	      patterns: {
	         youtube: {
	          index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
	          id: 'v=', // String that splits URL in a two parts, second part should be %id%
	          src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe. 
	          },
	        }
	      }
	    });
	});
*/
	jQuery(function(){
		$(window).load(function() {
			var windowswidth = $(window).width();
			var containerwidth = $('.container').width();
			var widthcss = (windowswidth-containerwidth)/2-55;
			var hei = $('.slide13').outerHeight();
			var rtl = jQuery( 'body' ).hasClass( 'rtl' );
			if( !rtl ) {
				jQuery(".custom-scoll").css("left",widthcss);
			}else{
				jQuery(".custom-scoll").css("right",widthcss);
			}
			var navScroll = $("#box-link1");
			
			if (navScroll.length > 0) {
				//$(".custom-scoll").fadeOut();
				jQuery(".custom-scoll").css("top",hei);
				jQuery(".custom-scoll").css("position","absolute");

				$(window).scroll(function() {
					if( $(window).scrollTop() > navScroll.offset().top - 30  ) {
						//$(".custom-scoll").fadeIn();
						
						jQuery(".custom-scoll").css("top",0);
						jQuery(".custom-scoll").css("position","fixed");
					} 
					else {
						//$(".custom-scoll").fadeOut();
						jQuery(".custom-scoll").css("top",navScroll.offset().top);
						jQuery(".custom-scoll").css("position","absolute");
					}
			
				});

	        }
	    })
	});

	/*
	jQuery(function(){
		$('#nav-scroll').onePageNav({
			currentClass: 'active',
			changeHash: false,
			scrollSpeed: 750,
			scrollThreshold: 0.5,
			filter: '',
			easing: 'swing',
			
		});
	});
	*/

	// Messenger Top Link
	$('.list-msg').owlCarousel2({
		pagination: false,
		center: false,
		nav: false,
		dots: false,
		loop: true,
		slideBy: 1,
		autoplay: true,
		margin: 30,
		autoplayTimeout: 4500,
		autoplayHoverPause: true,
		autoplaySpeed: 1200,
		startPosition: 0, 
		responsive:{
			0:{
				items:1
			},
			480:{
				items:1
			},
			768:{
				items:1
			},
			1200:{
				items:1
			}
		}
	});






	// Close pop up countdown
	 $( "#so_popup_countdown .customer a" ).click(function() {
	  $('body').toggleClass('hidden-popup-countdown');
	 });
	// =========================================


	// click header search header 
	jQuery(document).ready(function($){
		$( "header .search-header-w .icon-search" ).click(function() {
		$('header #sosearchpro .search').slideToggle(200);
		$(this).toggleClass('active');
		});
	});

	// add class Box categories
	jQuery(document).ready(function($){

		if($("#accordion-category .panel .panel-collapse").hasClass("in")){
			$('#accordion-category .panel .accordion-toggle').addClass("show");			
		} 
		else{
			$('#accordion-category .panel .accordion-toggle').removeClass("show");
		}

	});

	// slider categories
	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:10,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:4
	            },
	            1200:{
	                items:6
	            },
	        },
	    })
	});

	jQuery(document).ready(function($) {
	    var slidercate = $(".custom-slidercates25.so-categories .cat-wrap");
	    slidercate.owlCarousel2({    
	    margin:30,
	    nav:true,
	    loop:true,
	    dots: false,
	    navText: ['',''],
	    responsive:{
	            0:{
	                items:1
	            },
	            480:{
	                items:2
	            },
	            768:{
	                items:4
	            },
	            992:{
	                items:5
	            },
	            1200:{
	                items:6
	            },
	            1650:{
	                items:8
	            },
	        },
	    })
	});


	// custom to show footer center
	$(".button-toggle").click(function () {
		if($(this).children('.showmore').hasClass('active')) $(this).children().removeClass('active');
		else $(this).children().addClass('active');
		
		
		
		if($(this).prev().hasClass('showdown')) $(this).prev().removeClass('showdown').addClass('showup');
		else $(this).prev().removeClass('showup').addClass('showdown');
	}); 


	$(".clearable").each(function() {
  
	  var $inp = $(this).find("input:text"),
	      $cle = $(this).find(".clearable__clear");

	  $inp.on("input", function(){
	    $cle.toggle(!!this.value);
	  });
	  
	  $cle.on("touchstart click", function(e) {
	    e.preventDefault();
	    $inp.val("").trigger("input");
	  });
	  
	});

});
// Resonsive Sidebar aside
$(".open-sidebar").click(function(e){
	e.preventDefault();
	$(".sidebar-overlay").toggleClass("show");
	$(".sidebar-offcanvas").toggleClass("active");
});

$(".sidebar-overlay").click(function(e){
	e.preventDefault();
	$(".sidebar-overlay").toggleClass("show");
	$(".sidebar-offcanvas").toggleClass("active");
});
$('#close-sidebar').click(function() {
	$('.sidebar-overlay').removeClass('show');
	$('.sidebar-offcanvas').removeClass('active');

});