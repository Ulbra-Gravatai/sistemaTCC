;(function(){
  'use strict';
  console.log('load');
  var $lista = $('#lista-js');

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

  $lista.on('click', '.btn-excluir-js', function(event){
    event.preventDefault();

    var id = $(this).data('id');
    if(!id) return false;
    console.log(id);
    swal(swalExcluir,
    function(isConfirm){
      if (isConfirm) {
        var request = $.ajax({
              url: './semestre/'+ id + '/',
              type: 'DELETE',
              dataType: 'json'
        });

        request.done(function(data){
          swal({
           title: "Deletado!",
           text: "O semestre foi deletado com sucesso!",
           type: "success",
           confirmButtonText: "OK"
          },
          function(isConfirm){
           if (isConfirm) {
             location.reload();
           }
          });
        });

        request.fail(function(data){
          swal("Erro!", "Erro ao deletar o semestre, tente novamente", "error");
        });
      } else {
        swal("Cancelado!", "O semestre não foi deletada!", "error");
      }
    });
  });
})();
