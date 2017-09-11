"use strict";
$(function(){

	$(document).ready(function(){

		/*calc total price in cart*/
		$(".cart__total-price p.total span").html(total_price());

		/*navigation menu show/hide*/
		$(".phone-navigation .close").click(function(){
			$(this).parent().css("transform", "translate3d(-100%,0,0)");
		});
		$('#nav-icon1').click(function(){
			$(this).toggleClass('open');
			$(".phone-navigation").css("transform", "translate3d(0,0,0)");
		});


		/*our history click on cap - tabs*/
		$(".image-cap").click(function(){

			//get data-target attribute
			var tab_class = $(this).parent().data("target");

			//show/hide elements and addClass to clicked link
			$(this).parent().parent().find(".image-cap.back").removeClass("back").children('img').show();
			$(this).parent().parent().find(".image-cap").removeClass("back").children('p').remove();
			$(this).parent().parent().find(".history__caption").show();
			$(this).addClass("back");

			$(".content__item").hide();

			//show info to the corresponding link(cap)
			$(".content__item."+ tab_class).show();

			if($(this).hasClass("back")){
				$(this).children('img').hide();
				$(this).append($(this).next().html());
				$(this).next().hide();
			}else{
				$(this).children('img').show();
				$(this).children('p').remove();
			}
		});


		/**
		 * news and media filter
		 */
		$("#newsandmedia .filter a").click(function(){

			$("#newsandmedia .filter a").removeClass("active");
			$(this).addClass("active");

			var target = $(this).data("target");

			if(target == "all"){
				$("#newsandmedia .news__item").show();
			}else{
				$("#newsandmedia .news .news__item").hide();
				$("#newsandmedia .news .news__item." + target).show();
			}
		});


		/**
		 * activate popup windows
		 */
		$(".popup").magnificPopup({
			type: 'inline',
			preloader: false
		});
		$(document).on('click', '.close-popup', function (e) {
			e.preventDefault();
			$.magnificPopup.close();
		});

		/**
		 * activate carousel
		 */
		$(".carousel__items").owlCarousel({
			items				: 1,
			itemsDesktop 			: [1920,1],
			itemsDesktopSmall	: [1199,1],
			itemsTablet				: [789,1],
			itemsMobile 			: [450,1]
		});

		$(".body__carousel").owlCarousel({
			items				: 1,
			itemsDesktop 			: [1920,1],
			itemsDesktopSmall	: [1199,1],
			itemsTablet				: [789,1],
			itemsMobile 			: [450,1],
			navigation	:	true,
			navigationText			: ["",""]
		});
		var owl_c = $("#ourmarket .slider__items").owlCarousel({
			items				: 1,
			itemsDesktop 			: [1920,1],
			itemsDesktopSmall	: [1199,1],
			itemsTablet				: [789,1],
			itemsMobile 			: [450,1],
			navigation	:	true
		});

		/**
		 * custom navigation in carousel
		 */
		$('#ourmarket .right-btn').click(function(e) {
			e.preventDefault();
			// owl.trigger('next.owl.carousel');
			owl_c.trigger('owl.next');
		});
		$('#ourmarket .left-btn').click(function(e) {
			e.preventDefault();
			// owl.trigger('prev.owl.carousel');
			owl_c.trigger('owl.prev');
		});


		/**
		 * show/hide card overlay
		 */
		$(".continue-shopping").click(function(){
			$(".cart").hide();
		});
		$(".cart-preview").click(function(){
			$(".cart").show();
		});


		/**
		 * cart delete item
		 */
		$(document).on("click", ".cart .delete", function(e){
			e.preventDefault();
			$(this).parent().parent().parent().parent().remove();

			$(".cart__total-price p.total span").html(total_price());

			if(!$("div").is(".cart__item")){
				$(".cart__total").hide();
				$("a.check-out").hide();
				$(".cart-head").hide();
				$(".cart__empty").show();
			}
		});

		/**
		 * function calculate total price
		 * in cart
		 */
		function total_price(){
			var total = 0;
			$(".cart__item .price span").each(function(){
				total += Number($(this).html());
			});
			return total;
		}


		/**
		 * careers learnmore
		 */
		$('#careers .careers__text-block').readmore({
			maxHeight: 125,
			speed: 350,
			moreLink: '<a href="#" class="learnmore skew">LEARN MORE</a>',
			lessLink: '<a href="#" class="applynow skew">APPLY NOW</a>'
		});
		$(document).on("click",".learnmore",function(){
			$(this).parent().find("span").hide(300);
		});
		$(document).on("click",".applynow",function(){
			$(this).parent().find("span").show(300);
		});


		/**
		 * brands hover filter
		 */
		$("#brands .types a").hover(function(){
			//on hover
			var target_class = $(this).data("type");
			$(".brands a."+target_class+" img").css("filter","grayscale(0%)");
		},
		function(){
			//leave hover
			$(".brands a img").css("filter","grayscale(100%)");
		});




//==========EoF==============
	});
});