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

  $(function() {
    $('#video__play').on('click', function(e) {
      var dataYoutube = $(this).closest('.video').data('youtube');
      $(this).replaceWith('<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/' + dataYoutube + '?autoplay=1" frameborder="0" allowfullscreen></iframe></div>');
      e.preventDefault();
    });
  });

  // jQuery for page scrolling feature - requires jQuery Easing plugin
  $(function() {
    $('.scroll-to').on('click', function(e) {
        var $anchor = $(this);
        $('html, body').stop().animate({
             scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1500);
      e.preventDefault();
    });

    $('.action__btn--orange').on('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
             scrollTop: ($($anchor.data('href')).offset().top)
        }, 1500);
      });
  });

  // Masked Input
  $(function($){
    $(".form__control--mask").each(function() {
      var $this = $(this),
          maskPlaceholder = $this.attr('placeholder'),
          mask = $this.data('mask');
      if (mask != '') {
        $this.mask(mask, {placeholder: maskPlaceholder});
      }
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
    $('.test__items').matchHeight({
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
  $(function () {

    $('.form__group--date').datetimepicker({
      locale: 'ru',
      format: 'DD/MM/YYYY' + ' Г.',
      icons: {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-camera',
        clear: 'fa fa-trash-o',
        close: 'fa fa-times'
      }
    });
  });

  // Multiselect
  $(function () {
    $('.multiselect').multiselect({
      includeSelectAllOption: true,
      buttonClass:      'multiselect form__control form__control--input-group',
      nonSelectedText:  'Не выбрано',
      allSelectedText:  'Всё выбрано',
      selectAllText:    'Выбрать все!',
      numberDisplayed:  0,
      buttonWidth:      '100%',
      nSelectedText:    ' выбрано'
    });
  });

  // Buy ticket
  $(function() {
    $('.action__btn--orange').prop("disabled", true);
  });

  $(function() {
    var 
    $selectCinema = $('.select--cinema').multiselect({
      disableIfEmpty: true,
      disabledText: 'Выберите кинотеатр',
      nonSelectedText: 'Выберите кинотеатр',
      enableFiltering: true,
      filterPlaceholder: 'Поиск...',
      maxHeight: 400,
      numberDisplayed: 1,
      buttonClass: 'action__btn btn--default',
      templates: {
        filter: '<div class="input-group"><input class="form-control multiselect-search" type="text"></div>',
      },
      onChange: function(option, checked) {
        var link = $('.select--cinema option:selected').data('link');
        $('.action__btn--orange').prop("disabled", false);
        $('.action__form').attr('action', link);
      }
    }),
    $select = $('.select--city').multiselect({
      disableIfEmpty: true,
      disabledText: 'Выберите город',
      nonSelectedText: 'Выберите город',
      enableFiltering: true,
      filterPlaceholder: 'Поиск...',
      maxHeight: 400,
      numberDisplayed: 1,
      buttonClass: 'action__btn btn--default',
      templates: {
        filter: '<div class="input-group"><input class="form-control multiselect-search" type="text"></div>',
      },
      onChange: function(option, checked) {
        $.ajax({
          type: "POST",
          url: "../system/model.php",
          data: {
            formname: 'place',
            city: $('.select--city').val()
          },
          async: false,
          success: function (response) {
            var list = JSON.parse(response).result.place_cinema;
            if (list) {
                $selectCinema.multiselect('enable');
                $('.select--cinema').empty().append('<option hidden selected value="" disabled multiselect-collapsible-hidden multiselect-filter-hidden>Выберите кинотеатр</option>');
                $.each(list, function () {
                  $('.select--cinema').append($("<option></option>").val(this['cinema_name']).attr('data-link', this['link']).html(this['cinema_name']));
                });
            }
            else {
              $('.select--cinema').empty().append('<option selected="selected" value="0">Нет данных<option>');
            }
            $selectCinema.multiselect('rebuild'); //refresh the select here
          },
          error: function (data) {
            console.log(JSON.parse(data));
          }
        });
        $('.action__btn--orange').prop("disabled", true).parent().attr('action', '');
      }
    });
  
    //apply the plugin
    $select.multiselect('disable'); //disable it initially
    $selectCinema.multiselect('disable'); //disable it initially

    $.ajax({
      type: "POST",
      url: "../system/model.php",
      data: {
        formname: 'place'
      },
      async: false,
      success: function (response) {
        var list = JSON.parse(response).result.place_city;

        if (list) {
            $select.multiselect('enable');
            $('.select--city').empty().append('<option hidden selected value="" disabled multiselect-collapsible-hidden multiselect-filter-hidden>Выберите город</option>');
            $.each(list, function () {
              $('.select--city').append($("<option></option>").val(this['city']).html(this['city']));
            });
        }
        else {
          $('.select--city').empty().append('<option selected="selected" value="0">Нет данных<option>');
        }
        $select.multiselect('rebuild'); //refresh the select here
      },
      error: function (data) {
        console.log(JSON.parse(data));
      }
    });


    $('.select--reg-cinema').multiselect({
      disableIfEmpty: true,
      disabledText: 'Выберите кинотеатр ...',
      nonSelectedText: 'Выберите кинотеатр ...',
      enableFiltering: false,
      filterPlaceholder: 'Поиск...',
      maxHeight: 400,
      numberDisplayed: 1,
      inheritClass: true,
      buttonContainer: '<div class="select-group" />',
      templates: {
        filter: '<div class="input-group"><input class="form-control multiselect-search" type="text"></div>',
      },
      onChange: function(option, checked) {
        promoNotRequired();
      }
    });

    $('.select--online').multiselect({
      disableIfEmpty: true,
      disabledText: 'Способ оплаты ...',
      nonSelectedText: 'Способ оплаты ...',
      enableFiltering: false,
      filterPlaceholder: 'Поиск...',
      maxHeight: 400,
      numberDisplayed: 1,
      inheritClass: true,
      buttonContainer: '<div class="select-group" />',
      templates: {
        filter: '<div class="input-group"><input class="form-control multiselect-search" type="text"></div>',
      },
      onChange: function(option, checked) {
        promoNotRequired();
      }
    });


    function promoNotRequired () {
      if ($('.select--online').val() == 0) {
        $('.promo__item--code').add('.btn--promo').prop('disabled', false);
        if ($('.select--reg-cinema').val() == 2) {
          $('.promo__item--code').removeAttr('required');
        } else {
          $('.promo__item--code').attr('required', 'required');
        }
      } else if ($('.select--online').val() == 1) {
        if ($('.select--reg-cinema').val() == 2) {
          $('.promo__item--code').add('.btn--promo').removeAttr('required').prop('disabled', true);
        } else {
          $('.promo__item--code').add('.btn--promo').attr('required', 'required').prop('disabled', false);
        }
      } else {
        $('.promo__item--code').add('.btn--promo').prop('disabled', false)
      }
    }

    $('.btn--promo').on('click', function(e) {
      e.preventDefault();
      var $form = $('.form--promo'),
          $success = $form.find('.help-block'),
          $btnform = $form.find('.btn--promo'),
          iconSuccess = $btnform.data('loaded-text');
      $btnform.prop("disabled", true).button('loading');
      $.ajax({
        type: "POST",
        url: "../system/model.php",
        data: {
          formname: 'promo',
          cinema: $('[name=cinema] option:selected').text(),
          status: $('[name=online]').val(),
          promo: $('[name=promocode]').val()
        },
        async: true,
        success: function (response) {
          var list = JSON.parse(response).result;
          if (list.promo_error) {
            $btnform.prop("disabled", false).button('reset');
            $success.html(list.promo_error);
          } else if (list.promo_status) {
            $success.html('');
            $btnform.closest('.form__submit--promo').html(iconSuccess);
            $form.find('.promo__item--code').addClass('promo__item--activated').prop("disabled", true);
            $('.select--online').attr('disabled', 'disabled');
            $('.select--reg-cinema').attr('disabled', 'disabled');

          }
        },
        error: function (data) {
          $btnform.prop("disabled", false).button('reset');
          console.log('error', JSON.parse(data));
        }
      });

      e.preventDefault();
    });
  });

})(jQuery); // End of use strict