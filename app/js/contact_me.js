$(function() {

  $(".contactForm input, .contactForm textarea, .contactForm select").jqBootstrapValidation({
    preventSubmit: true,
    submitError: function($form, event, errors) {
      // additional error messages or events
    },
    submitSuccess: function($form, event) {
      event.preventDefault(); // prevent default submit behaviour
      $form.find("[type=submit]").prop("disabled", true).button('loading'); //prevent submit behaviour and display preloading
      
      // get values from FORM
      var formname           = 'registration',
          form               = $form.find('[type=submit]').attr("name"),
          cinema             = $form.find('[name=cinema] option:selected').text(),
          purchase           = $form.find('[name=online] option:selected').text(),
          promo              = $form.find('[name=promocode]').val(),
          ticket_1           = $form.find('[name=ticket1]').val(),
          ticket_2           = $form.find('[name=ticket2]').val(),
          email              = $form.find('[name=email]').val(),
          birthday           = $form.find('[name=date]').val(),
          gender             = $form.find('[name=gender]').val(),
          cinema_per_month   = new Array(),
          cinema_3d          = new Array(),
          genre              = $form.find("[name^=\"films3d\"]").val(),
          card_loyalty       = new Array();

      $("[name^=\"radio0\"]:checked").each(function() {
        if ($(this).prop("checked")) {
          var radioText = $(this).siblings().text();

          cinema_per_month.push($.trim(radioText) + " ");
        }
      });

      $("[name^=\"radio1\"]:checked").each(function() {
        if ($(this).prop("checked")) {
          var radioText = $(this).siblings().text();

          cinema_3d.push($.trim(radioText) + " ");
        }
      });

      $("[name^=\"checkbox\"]:checked").each(function() {
        if ($(this).prop("checked")) {
          var radioText = $(this).siblings().text();
          card_loyalty.push($.trim(radioText) + " ");
        }
      });

      $.ajax({
        url: "././lib/model.php",
        type: "POST",
        data: {
          form: $.trim(form),
          formname: $.trim(formname),
          cinema: $.trim(cinema),
          purchase: $.trim(purchase),
          promo: $.trim(promo),
          ticket_1: $.trim(ticket_1),
          ticket_2: $.trim(ticket_2),
          email: $.trim(email),
          birthday: $.trim(birthday),
          gender: $.trim(gender),
          cinema_per_month: cinema_per_month,
          cinema_3d: cinema_3d,
          genre: genre,
          card_loyalty: card_loyalty
        },
        cache: false,
        success: function(response) {
          if (response) {
            var list = JSON.parse(response).result;
            if (list.promo_error) {

              $form.find('.success').html("<div class='alert alert-danger'>");
              $form.find('.success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                .append("</button>");
              $form.find('.success > .alert-danger').append("<strong>" + list.promo_error);
              $form.find('.success > .alert-danger').append('</div>');
            } else {
              // Success message
              $form.find('.success').html("<div class='alert alert-success'>");
              $form.find('.success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                .append("</button>");
              $form.find('.success > .alert-success')
                .append("<strong>Cообщение успешно отправлено.</strong>");
              $form.find('.success > .alert-success')
                .append('</div>');
            }
          }

          // remove prevent submit behaviour and disable preloading
          $form.find("[type=submit]").prop("disabled", false).button('reset');  

          //clear all fields
          //$form.trigger("reset");
        },
        error: function() {
          // Fail message
          $form.find('.success').html("<div class='alert alert-danger'>");
          $form.find('.success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
            .append("</button>");
          $form.find('.success > .alert-danger').append("<strong>Приносим свои извинения, но наш почтовый сервер времено не работает. Попробуйте, отправить сообщение еще раз и сообщите нам о проблеме!");
          $form.find('.success > .alert-danger').append('</div>');

          // remove prevent submit behaviour and disable preloading
          $form.find("[type=submit]").prop("disabled", false).button('reset'); 

          //clear all fields
          //$form.trigger("reset");
        },
      })
    },
    filter: function() {
      return $(this).is(":visible");
    },
  });

  $("a[data-toggle=\"tab\"]").click(function(e) {
    e.preventDefault();
    $(this).tab("show");
  });
});

/*When clicking on Full hide fail/success boxes */
$('#name').focus(function() {
  $('.success').html('');
});