(function($) {
  "use strict"; // Start of use strict

  // Old browser notification
  $(function() {
    $.reject({
      reject: {
        msie: 9
      },
      imagePath: 'img/icons/jReject/',
      display: [ 'chrome','firefox','safari','opera' ],
      closeCookie: true,
      cookieSettings: {
        expires: 60*60*24*365
      },
      header: 'Ваш браузер устарел!',
      paragraph1: 'Вы пользуетесь устаревшим браузером, который не поддерживает современные веб-стандарты и представляет угрозу вашей безопасности.',
      paragraph2: 'Пожалуйста, установите современный браузер:',
      closeMessage: 'Закрывая это уведомление вы соглашаетесь с тем, что сайт в вашем браузере может отображаться некорректно.',
      closeLink: 'Закрыть это уведомление',
    });
  });

  $(function () {
    $('#video__play').on('click', function(e) {
      let dataYoutube = $(this).parents('.video').data('youtube');
      $(this).replaceWith('<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/' + dataYoutube + '?autoplay=1" frameborder="0" allowfullscreen></iframe></div>');
      e.preventDefault();
    });
  });

  // jQuery for page scrolling feature - requires jQuery Easing plugin
  /*$(function() {
      $('a.page-scroll').bind('click', function(event) {
          var $anchor = $(this);
          $('html, body').stop().animate({
              scrollTop: $($anchor.attr('href')).offset().top
          }, 1500, 'easeInOutExpo');
          event.preventDefault();
      });
  });*/


  // Fixed navbar on Scroll
  /*if(!$('.navbar-toggle').is(':visible')) {
    $('.navbar').affix({
      offset: {
        top: $('header').innerHeight()
      }
    }); 
  }*/

  // Highlight the top nav as scrolling occurs
  /*$('body').scrollspy({
      target: '.navbar-fixed-top'
  })*/

  // Navbar class active
  /*$(document).ready( function () {
    $(".nav li").click( function () {
      $(".nav li").removeClass("active");
      $(this).addClass("active");
    });
  });*/

  // Dropdowns on hover on desktop
  /*var navbarToggle = '.navbar-toggle'; // name of navbar toggle, BS3 = '.navbar-toggle', BS4 = '.navbar-toggler'  
  $('.dropdown, .dropup').each(function() {
    var dropdown = $(this),
      dropdownToggle = $('[data-toggle="dropdown"]', dropdown),
      dropdownHoverAll = dropdownToggle.data('dropdown-hover-all') || false;
    
    // Mouseover
    dropdown.hover(function(){
      var notMobileMenu = $(navbarToggle).size() > 0 && $(navbarToggle).css('display') === 'none' && $(document).width() >= 992 ;
      if ((dropdownHoverAll === true || (dropdownHoverAll === false && notMobileMenu))) { 
        dropdownToggle.trigger('click');
      }
    });
  });*/


  // Close dropdowns on "esc"
  /*$('.dropdown-menu').bind('keydown',function(event) {
    // ESC = Keycode 27
    if (event.keyCode == 27) {
      $(this).parrent().find('.dropdown-toggle').dropdown('toggle');
    }
  });*/

  // Closes the Responsive Menu on Menu Item Click
/*  $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function() {
    $('.navbar-collapse ul li a').click(function(){ 
      $('.navbar-toggle:visible').click();
    });
  });*/
  // Masked Input
  $(function($){
    $(".form__control--mask").each(function() {
      var $this = $(this),
          maskPlaceholder = $this.attr('placeholder'),
          mask = $this.data('mask');
        $this.mask(mask, {placeholder: maskPlaceholder});
    });
  });

  // Equal height
  $(function() {
    $('.slider__img').matchHeight({
      byRow: true,
      property: 'height',
      target: null,
      remove: false,
    });
    $('.slider__descr').matchHeight({
      byRow: true,
      property: 'height',
      target: null,
      remove: false,
    });
    $('.company__item').matchHeight({
      byRow: true,
      property: 'height',
      target: null,
      remove: false,
    });
  });
  
  // Slick slider
  if ($('.slider').length) { 
    $('.slider').slick({
      dots: true,
      arrows: false,
      infinite: true,
      slidesToShow: 1,
      speed: 500,
      mobileFirst: true,
      swipeToSlide: '15',
      responsive: [
        {
          breakpoint: 768,
          settings: "unslick",
        }
      ]
    });
  }

  // DatePicker
  /*$(function () {
    $('.form__group--date').datetimepicker({
      locale: 'ru',
      format: 'LT'
    });
  });*/

  // Multiselect
  $(function () {
    $('.multiselect').multiselect({
      includeSelectAllOption: true,
      buttonClass:      'multiselect form__control form__control--input-group',
      nonSelectedText:  'Не выбрано',
      allSelectedText:  'Всё выбрано',
      selectAllText:    'Выбрать все!',
      numberDisplayed:  1,
      buttonWidth:      '100%',
      nSelectedText:    ' выбрано'
    });
  });

  // Buy ticket
  $(function() {
    $('.action__btn--orange').prop("disabled", true);
  });
  
 
})(jQuery); // End of use strict