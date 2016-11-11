;(function(){
  'use strict';

  const $lista = $('.lista-etapa-js');
  const $formSemestre = $('#form-js');
  const $formEtapa = $('#etapa-js');
  const $btnSalvarEtapa = $('#btn-salvar-etapa-js');
  const urlSemestre = './semestre/';
  const urlEtapa = './etapa-semestre/';

  function init(){
    const id = $('#id-semestre').val();
    if(id) {
      $('#container-etapas-js').show();
      $('#btn-cadastrar-semestre').html('Cadastrar');
    }
    $('.datepicker').datepicker({
      // format: 'yyyy-mm-dd',
      format: 'dd/mm/yyyy',
      autoclose: true
    });
  }
  init();

  const swalExcluir = {
    title: "Você tem certeza?",
    text: "Após a exclusão não será possível recuperá-lo!",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: "Deletar!",
    cancelButtonText: "Cancelar!",
    closeOnConfirm: false,
    closeOnCancel: false
  };

  const swalEditar = {
    title: "Editar essa etapa?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#8CD4F5",
    confirmButtonText: "Editar!",
    cancelButtonText: "Cancelar!",
    closeOnConfirm: false,
    closeOnCancel: false
  };

  const swalSalvar = {
    title: "Cadastrar essa etapa?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#8CD4F5",
    confirmButtonText: "Cadastrar!",
    cancelButtonText: "Cancelar!",
    closeOnConfirm: false,
    closeOnCancel: false
  };

  function verifyErrors(elem, arr, err) {
      const errors = err || {};
      $.each(arr, function(key, value) {
          const message = errors[value] || false;
          const element = elem.find('#' + value);
          if (message) {
              element.parent().addClass('has-error').find('.help-block').html(message);
          } else {
              element.parent().removeClass('has-error').find('.help-block').html('');
          }
      });
  }

  $formSemestre.on('submit', function(e){
    e.preventDefault();

    var dataStart = $formSemestre.find('#dataInicio').val();
    var dataEnd = $formSemestre.find('#dataFim').val();

    const body = {
      nome: $formSemestre.find('#nome').val(),
      dataInicio: moment(dataStart, 'DD/MM/YYYY').format('YYYY-MM-DD'),
      dataFim: moment(dataEnd, 'DD/MM/YYYY').format('YYYY-MM-DD'),
      // dataInicio: dataStart,
      // dataFim: dataEnd,
      campus: $formSemestre.find('#campus').val()
    };

    const id = $('#id-semestre').val();

    function pushEtapa(elem){
      let arr = [];
      $.each(elem, function(index, e){
        arr.push(e.dataset.id);
      });
      return arr;
    }

    const url = urlSemestre + (id ? id + '/' : '');
    const method = id ? 'PUT' : 'POST';
    const request = $.ajax({
        url: url,
        type: method,
        dataType: 'json',
        // contentType: 'application/json',
        data: body
    });

    request.done(function(data){
      if(data.id) {
          swal({
           title: "Cadastrado!",
           text: "Continuar cadastrando o semestre!",
           type: "success",
           confirmButtonText: "OK"
          },
          function(isConfirm){
           if (isConfirm) {
             location.href = urlSemestre + 'editar/' + data.id;
           }
          });
      }
    });

    request.fail(function(err){
      const errors = err.responseJSON;
      const verify = ['nome','campus', 'dataInicio', 'dataFim'];
      verifyErrors($formSemestre,verify , errors);
      swal("Erro!", "Erro ao cadastrar o semestre, tente novamente", "error");
    });
  });


  $('.table').on('click', '.btn-cadastrar-etapa', function(event){
    event.preventDefault();
    var $inputEtapa = $('#etapa-js .clean');
    $inputEtapa.val('');
    $inputEtapa.parent().removeClass('has-error').find('.help-block').html('');
    $('#btn-open-etapa').click();

    const tccValue = ($(this).parent().parent().parent().data('tcc') || null);
    (tccValue) ? $('#add-tcc').val(tccValue) : $('#add-tcc').val('');

    const etapaId = $(this).parent().parent().data('id');
    if (etapaId) getEtapa(etapaId);
  });


  $lista.on('click', '.btn-excluir-etapa', function(e){ //Excluir etapa
    e.preventDefault();

    const etapaId = $(this).parent().parent().data('id');
    if(!etapaId) return false;

    swal(swalExcluir,
    function(isConfirm){
      if (isConfirm) {
        var request = $.ajax({
              url: urlEtapa + etapaId + '/',
              type: 'DELETE',
              dataType: 'json'
        });

        request.done(function(data){
          swal({
           title: "Deletado!",
           text: "A etapa foi deletada com sucesso!",
           type: "success",
           confirmButtonText: "OK"
          },
          function(isConfirm){
           if (isConfirm) {
             location.reload();
           }
          });
        });

        request.fail(function(err){
          swal("Erro!", "Erro ao deletar a etapa, tente novamente", "error");
        });
      } else {
        swal("Cancelado!", "A etapa não foi deletada!", "error");
      }
    });
  });

  function getEtapa(id) { // GET - busca os dados da etapa e popula no modal
    var request = $.ajax({
        url: urlEtapa + id + '/',
        type: 'GET',
        dataType: 'json',
    });

    request.done(function(data){
      $('#id-etapa').val(data.id);
      $('#etapa-nome').val(data.nome);
      $('#etapa-tipo').val(data.tipo);
      $('#etapa-peso').val(data.peso);
      $('#etapa-dataFim').val(moment(data.dataFim, "YYYY-MM-DD").format('DD/MM/YYYY'));
      $('#etapa-dataInicio').val(moment(data.dataInicio, "YYYY-MM-DD").format('DD/MM/YYYY'));
      $('#add-tcc').val(data.tcc);
    });

    request.fail(function(data){
      $('#etapa-js').find('.form-group').hide();
      $('#etapa-js').html('Houve um problema');
    });
  }

  $btnSalvarEtapa.on('click', function(e){ // função cadastrar etapa
    e.preventDefault();
    const etapaId = $('#id-etapa').val();
    const url = urlEtapa + (etapaId ? etapaId + '/' : '');
    const method = etapaId ? 'PUT' : 'POST';


    var dataStart = moment($('#etapa-dataInicio').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
    console.log(dataStart);
    var dataEnd = moment($('#etapa-dataFim').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
    console.log(dataEnd);

    const body = {
      nome: $('#etapa-nome').val(),
      tipo: $('#etapa-tipo').val(),
      semestre: $('#id-semestre').val(),
      peso: $('#etapa-peso').val(),
      dataInicio: dataStart,
      dataFim: dataEnd,
      ordem: 2,
      tcc: $('#add-tcc').val()
    };

    if(etapaId) {
      body.id = etapaId;
    }

    swal(swalSalvar,
    function(isConfirm){
      if (isConfirm) {
        var request = $.ajax({
              url: url,
              type: method,
              dataType: 'json',
              data: body
        });

        request.done(function(data){
          swal({
           title: "Cadastrada!",
           text: "A etapa foi cadastrada com sucesso!",
           type: "success",
           confirmButtonText: "OK"
          },
          function(isConfirm){
           if (isConfirm) {
             location.reload();
           }
          });
        });

        request.fail(function(err){
          const errors = err.responseJSON;
          const verify = ['etapa-nome','etapa-tipo','etapa-peso', 'etapa-dataInicio', 'etapa-dataFim'];
          verifyErrors($formEtapa, verify, errors);
          swal("Erro!", "Erro ao alterar a etapa, tente novamente", "error");
        });
      } else {
        swal("Cancelado", "A etapa não foi alterada!", "error");
      }
    });
  });
})();
