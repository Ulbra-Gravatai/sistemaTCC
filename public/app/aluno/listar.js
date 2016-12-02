$(function() {

    const $lista = $('#lista-js');
    const url = './aluno/';
    const urlListar = './aluno/listar';

    function ajax(id) {
        const request = $.ajax({
                url: url + id + '/',
                type: 'delete',
                dataType: 'json',
            });
        request.done(function(data) {
            showDone(function() {
                location.href = urlListar;
            });
            return;
        });

        request.fail(function(err) {
            var res = err.responseJSON;
            showError(res.error);
            if ('message' in res) {
                console.log('DETALHES DO ERRO:', res.message);
            }
            return false;
        });
    }

    $lista.on('click', '.excluir', function(event) {
        event.preventDefault();
        const id = $(this).data('id');
        if (!id) {
            return false;
        }
        showConfirmDelete(function() {
            ajax(id);
        });
    });

});
