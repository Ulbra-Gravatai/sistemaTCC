$(function() {

    var $lista = $('#lista-js');
    var url = './campus/';
    var urlListar = './campus/listar';

    function ajax(id) {
        var request = $.ajax({
                url: url + id + '/',
                type: 'delete',
                dataType: 'json',
            });
        // Caiu aqui deu certo
        request.done(function(data) {
            location.href = urlListar;
            return;
        });

        // Caiu aqui, tem erro
        request.fail(function(err) {
            console.log(err);
            return false;
        });
    }

    $lista.on('click', '.excluir', function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        if (!id) {
            return false;
        }
        swal({
            title: "Deseja mesmo excluir?",
            text: "Você irá remover esse registro!",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sim, excluir agora!",
            closeOnConfirm: false },
            function(){
                swal("Ok!", "Registro excluido!", "success");
                ajax(id);
            });
    });

});
