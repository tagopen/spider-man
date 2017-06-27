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
      var form         = $form.find('[type=submit]').attr("name"),
          cinema       = $form.find("[name=cinema] option:selected").text(),
          online       = $form.find("[name=online] option:selected").text(),
          promocode    = $form.find("[name=promocode]").val(),
          ticket1      = $form.find("[name=ticket1]").val(),
          ticket2      = $form.find("[name=ticket2]").val(),
          email        = $form.find("[name=email]").val(),
          date         = $form.find("[name=date]").val(),
          radio0       = '',
          radio1       = '';
          
      $('[name^=\"radio0\"]:checked').each(function() {
        if ($(this).prop("checked")) {
          radio0 = $(this).siblings().text();
        }
      });


          console.log(radio0);

      $.ajax({
        url: "././lib/mail/mail.php",
        type: "POST",
        data: {
          form: form,
          cinema: cinema,
          online: online,
          promocode: promocode,
          ticket1: ticket1,
          ticket2: ticket2,
          email: email,
          date: date,
        },
        cache: false,
        success: function() {
          // Success message
          $form.find('.success').html("<div class='alert alert-success'>");
          $form.find('.success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
            .append("</button>");
          $form.find('.success > .alert-success')
            .append("<strong>Ваше сообщение успешно отправлено. В ближайшее время наши менеджеры свяжутся с вами! </strong>");
          $form.find('.success > .alert-success')
            .append('</div>');

          // remove prevent submit behaviour and disable preloading
          $form.find("[type=submit]").prop("disabled", false).button('reset');  

          //clear all fields
          $form.trigger("reset");
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
          $form.trigger("reset");
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