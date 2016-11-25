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

  $form.find('#arquivo').on('change', function (event) {
    event.preventDefault();
	var item = $('<li class="list-group-item"></li>').html(
	'<div class="progress">'
		+ '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">'+
			+ '<span class="sr-only">Enviando Arquivo</span>'
		+ '</div>'
	+ '</div>');
    //var values = {
    //  etapa: $form.find('#etapa-id').val(),
    //  arquivo: $form.find('#arquivo').val()
    //};
    var url = restURL;
    var method = 'post';
    var request = $.ajax({
      url: url,
      type: method,
      dataType: 'json',
      data: new FormData($form[0]),
	  contentType: false,       // The content type used when sending data to the server.
		cache: false,             // To unable request pages to be cached
		processData:false,
		beforeSend: function() {
			$('#upload-list').append(item);
		}
    });
    // Caiu aqui deu certo
    request.done(function (data) {
      verifyErrors();
	  
	  item.attr('id','arquivo-' + data.arquivo.id);
	  item.html(item.prev().html());
	  item.find('a').attr('href','../' + data.arquivo.caminho + data.arquivo.nome).text(data.arquivo.nome);
	  item.find('.excluir-arquivo-js').attr('data-id',data.arquivo.id);
      swal({
        title: "OK",
        text: "Enviado!",
        type: "success",
        showCancelButton: false,
        confirmButtonText: "OK",
        closeOnConfirm: true},
			function () {
			//  location.href = listaURL;
				
			});
    });
    // Caiu aqui, tem erro
    request.fail(function (err) {
      var errors = err.responseJSON;
      verifyErrors(errors);
    });
  });
  
  /**********************************
   * Função para excluir arquivos
   **********************************/
   var swalExcluir = {
		title: "Você tem certeza?",
		text: "Após a exclusão não será possível recupera os dados.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Deletar!",
		cancelButtonText: "Cancelar!",
		closeOnConfirm: false,
		closeOnCancel: false
	};
	var $lista = $('.lista-arquivos-js');
	$lista.on('click', '.excluir-arquivo-js', function (e) {
	  e.preventDefault();

	  var etapaId = $(this).data('id');

	  if (!etapaId)
		return false;

	  swal(swalExcluir, function (isConfirm) {
		if (isConfirm) {
		  var request = $.ajax({
			url: 'enviaretapa/' + etapaId + '/',
			type: 'DELETE',
			dataType: 'json'
		  });
		  request.done(function (data) {
			swal("Deletado!", "O Arquivo foi excluído com sucesso!", "success");
			$('#arquivo-' + etapaId).remove();
		  });
		  request.fail(function (data) {
			swal("Erro!", "Erro ao excluir arquivo, tente novamente", "error");
		  });
		} else {
		  swal("Cancelado!", "O Arquivo não foi excluído!", "error");
		}
	  });
	});
});
