jQuery(function($) {
  "use strict";

  /*navigation menu show/hide*/
  $(".phone-navigation .close").click(function(){
    $(this).parent().css("transform", "translate3d(-100%,0,0)");
  });
  $('#nav-icon1').click(function(){
    $(this).toggleClass('open');
    $(".phone-navigation").css("transform", "translate3d(0,0,0)");
  });
$(document).ready(function(){ 
$('.owl_carousel_home').bxSlider({
  minSlides: 1,
  maxSlides: 1,
 
  
  captions: true
});
  });

  /*our history click on cap - tabs*/
  $(".image-cap").click(function(){

    //get data-target attribute
    var tab_class = $(this).parent().data("target");

    //show/hide elements and addClass to clicked link
    $(this).parent().parent().find(".image-cap.back").removeClass("back").children('img').show();
    $(this).parent().parent().find(".image-cap .image-cap").removeClass("back").children('p').remove();
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
 $(document).ready(function() {	
            $('.ma5slider').ma5slider();
		
        });
    
 $(document).ready(function() {
    $().myTabs({});
});
 

var settings;

$.fn.myTabs = function( options ) {

	var defaults = {
		tabs: "ul.tabs li",
		heading: ".tab_heading",
		tab_pager: "nav.tab-pager ul li", 
		tab_content : ".tab_content"	
	};

	settings = $.extend(defaults, options);

	tabHandler( { 'activeTab': '#tab0' });

	$(settings.tabs).on( "click", tabHandler );
	$(settings.heading).on( "click", tabHandler );
	$(settings.tab_pager).on( "click", tabHandler );

	function scrollToAnchor(activeTab){
		var activeTabId = activeTab.replace(/#/, '');
		var anchor = $("a[name='"+ activeTabId +"']");
		//$('html,body').animate({scrollTop: anchor.offset().top},'slow');
	}

	function tabHandler( event ) {

		var activeTab;

		if (event.activeTab && event.activeTab.match(/^#tab/)) {
			activeTab = event.activeTab;
		} else {
			activeTab = $(this).find("a").attr("href");
		}

		var activeIndex = parseInt(activeTab.replace("#tab", ""));

		if (! $.isNumeric(activeIndex)) {
			return false;
		}

		var tabsSize = $(settings.tabs).size();
		var prevIndex = (activeIndex === 0) ? (tabsSize - 1) : (activeIndex - 1);
		var nextIndex = (activeIndex === (tabsSize - 1) )  ? 0 : (activeIndex + 1);

		$(settings.tab_content).hide();

		// tabs
		$(settings.tabs + ":nth-child("+ (activeIndex +1) + ")").addClass('active').siblings().removeClass('active');

		// heading
		$(settings.heading).eq(activeIndex).addClass('active').siblings().removeClass('active');

		// tab_pager
		$(settings.tab_pager).removeClass('active');
		$(settings.tab_pager + ":nth-child("+ (activeIndex +2) + ")").addClass('active');
		$(settings.tab_pager + ":first-child").find("a").attr("href", "#tab"+ prevIndex);
		$(settings.tab_pager + ":last-child").find("a").attr("href", "#tab"+ nextIndex);

		scrollToAnchor(activeTab);
		$(activeTab).toggle();

		return false;

	}

};
 
 
 


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

  if ($.fn.owlCarousel) {
    /**
     * activate carousel
     */
    // $(".carousel__items").owlCarousel({
    //   items: 1
    // });

    $(".body__carousel").owlCarousel({
      items: 1,
      nav: true,
      dots: false
    });
    var owl_c = $("#ourmarket .slider__items").owlCarousel({
      items: 1,
      nav: true,
      dots: false
    });

	  
  }

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
  $(".continue-shopping").click(minicart_hide);

  $('.cart-preview').click(function (e) {
    e.preventDefault()
    minicart_toggle()
  })

  $(document.body).on('wc_fragments_refreshed', function () {
    $('.mini-cart .cart__body').removeClass('cart-loading')
  })

  $('.minicart-overlay').click(minicart_hide)

  /**
   * mini-cart remove item
   */
  $('body').on('click', '.mini-cart .remove', function (e) {
    e.preventDefault()

    $(this).parents('.cart__body').addClass('cart-loading')

    $.get(this.href).always(function () {
      $(document.body).trigger('wc_fragment_refresh') // refresh cart contents
      $(this).parents('.cart__body').removeClass('cart-loading')
    })
  });

  /**
   * mini-cart update item quantity
   */
  $('body').on('change', '.mini-cart [name=quantity]', function (e) {
    e.preventDefault()

    $('.mini-cart .cart__body').addClass('cart-loading')

    var req = {
      action: 'si_cart_update_quantity',
      cart_item_key: $(this).data('cart_item_key'),
      quantity: this.value
    }

    $.post(woocommerce_params.ajax_url, req).always(function () {
      $(document.body).trigger('wc_fragment_refresh') // refresh cart contents
    })
  });

  // Update the quantity in the button's data attribute (needed for WC ajax
  // functionality) when value in select-box changes
  $('.product [name=quantity]').on('change', function () {
    $(this).parent().find('.add_to_cart_button').attr('data-quantity', this.value)
  })

  // SHOP BY BRAND select-box
  $('#shop-by-brand-select').change(function () {
    this.form.submit()
  })

  if ($.fn.owlCarousel) {
    // Shop Products Carousel
    $('.store_items_wrap > div').each(function () {
      $(this).owlCarousel({
        loop: false,
        nav: true,
        dots: false,
        margin: 20,
        responsiveClass: true,
        responsive: {
          0: { items: 1 },
          485: { items: 2 },
          719: { items: 3 },
          959: { items: 4 },
          1199: { items: 5 }
        }
      })
    })
  }

  /**
   * careers learnmore
   */
  $('#careers .careers__learn-more').click(function (e) {
    if ($(this).is('.applynow')) return

    e.preventDefault()
    e.stopPropagation()

    var shortH = $(this).parent().find('.careers__text-block-short').height()

    $(this).parent().find('.careers__text-block-short').hide()

    $(this).parent().find('.careers__text-block-full')
      .css({
        maxHeight: shortH,
        display: 'block'
      })
      .animate({ maxHeight: 9999 }, 1000)
      .parent().addClass('open')

    $(this).hide()
  })

  // the "(more)" link
  $('.careers__item span.more').click(function () {
    $(this).parents('.careers__item').find('.learnmore').click()
  })


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



  /************************
    Forms
  ************************/

  /**
   * Any form AJAX handler
   */
  $(document).on('submit', 'form.ajax-form', function (e) {
      var $form = $(this)

      $('.form-status-message', $form).remove()
      $('[name]', $form).removeClass('form-incorrect-field')

      var req = $form.serialize()

      var callback = function (res) {
        var $message = $('<div class="form-status-message" />')
          .addClass('form-status-' + res.status)
          .html(res.message)
          .appendTo($form)

        var delay = $form.data('status-delay') || 4000
        delay += res.message.length * 10
        setTimeout(function () {
          $message.slideUp()
          $('[name]', $form).removeClass('form-incorrect-field')
        }, delay)

        if (res.incorrectField) {
          $('[name="' + res.incorrectField + '"]:not([type="submit"])', $form)
            .addClass('form-incorrect-field')
            .focus()
        }

        if (res.status === 'ok') {
          $('[name]:not([type="hidden"]):not([type="submit"])', $form)
            .blur()
            .val('')
        }
      }

      $.post(window.ajax_object.ajaxurl, req, callback, 'json')

      return false
    })
    .find('[name]').change(function () {
      $(this).removeClass('form-incorrect-field')
    })

});

function minicart_toggle() {
  var is_minicart_visible = jQuery('.mini-cart').is(':visible')
  jQuery('.mini-cart').toggle(!is_minicart_visible)
  jQuery('.minicart-overlay').toggle(!is_minicart_visible)
}

function minicart_show() {
  jQuery('.mini-cart').show()
  jQuery('.minicart-overlay').show()
}

function minicart_hide() {
  jQuery('.mini-cart').hide()
  jQuery('.minicart-overlay').hide()
}