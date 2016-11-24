$(function () {

  var $form = $('#form-js');
  //var itemID = $form.find('#id').val();
  var restURL = './enviaretapa/';
  var listaURL = './enviaretapa/';
  function verifyErrors(err) {
    var errors = err || {};
    $.each(['arquivo'], function (key, value) {
      var message = errors[value] || false;
      var element = $form.find('#' + value);
      if (message) {
        element.parent().addClass('has-error').find('.help-block').html(message);
      } else {
        element.parent().removeClass('has-error').find('.help-block').html('');
      }
    });
  }

  $form.on('submit', function (event) {
    event.preventDefault();
    var values = {
      etapa: $form.find('#etapa-id').val(),
      arquivo: $form.find('#arquivo').val()
    };
    var url = restURL;
    var method = 'post';
    var request = $.ajax({
      url: url,
      type: method,
      dataType: 'json',
      data: values
    });
    // Caiu aqui deu certo
    request.done(function (data) {
      verifyErrors();
      swal({
        title: "OK",
        text: "Alterado!",
        type: "success",
        showCancelButton: false,
        confirmButtonText: "Voltar para Lista",
        closeOnConfirm: false},
              function () {
                location.href = listaURL;
              });
    });
    // Caiu aqui, tem erro
    request.fail(function (err) {
      var errors = err.responseJSON;
      verifyErrors(errors);
    });
  });
  
  $("#arquivo").change(function() {
	$(this).prev().html($(this).val());
  });
});
