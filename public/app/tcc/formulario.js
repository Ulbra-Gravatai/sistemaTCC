$(function() {

    "use strict";

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './tcc/';
    const listaURL = './tcc/';
	
    function verifyErrors(err) {
        const errors = err || {};

        $.each(['titulo', 'aluno', 'semestre'], function(key, value) {
            const message = errors[value] || false;
            const element = $form.find('#' + value);
            if (message) {
                element.parent().addClass('has-error').find('.help-block').html(message);
            } else {
                element.parent().removeClass('has-error').find('.help-block').html('');
            }
        });
    }

    $form.on('submit', function(event) {
        event.preventDefault();

        const values = {
            titulo: $form.find('#titulo').val(),
          	aluno: $form.find('#aluno').val(),
            semestre: $form.find('#semestre').val(),
        };


        const url = restURL + (itemID ? itemID + '/' : '' );
        const method = itemID ? 'put' : 'post';
        const text = itemID ? 'Alterado': 'Incluído';

        const request = $.ajax({
                url: url,
                type: method,
                dataType: 'json',
                data: values
            });

        // Caiu aqui deu certo
        request.done(function(data) {
            verifyErrors();
            swal({
                title: "OK",
                text: text,
                type: "success",
                showCancelButton: false,
                confirmButtonText: "Voltar para Lista",
                closeOnConfirm: false },
                function() {
                    location.href = listaURL;
                });
        });

        // Caiu aqui, tem erro
        request.fail(function(err) {
            const errors = err.responseJSON;
            verifyErrors(errors);
        });

    });

});
