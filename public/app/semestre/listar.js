$(function() {
    'use strict';
    const $lista = $('#lista-js');

    $lista.on('click', '.btn-excluir-js', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        if (!id) {
            return false;
        }
        showConfirmDelete(function() {
            var request = $.ajax({
                url: './semestre/'+ id + '/',
                type: 'DELETE',
                dataType: 'json'
            });
            request.done(function(data){
                showDone(function() {
                    location.reload();
                });
            });
            request.fail(function(data){
              swal("Erro!", "Erro ao deletar o semestre, tente novamente", "error");
            });
        });
    });
});
