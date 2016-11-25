$(function() {

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './professor/';
    const listaURL = './professor/';

    function verifyErrors(err) {
        const errors = err || {};
        $.each(['nome', 'email', 'telefone', 'interesses'], function(key, value) {
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
        const interesses = [];
        $.each($("input[type=checkbox]:checked"), function() {
            interesses.push($(this).val());
        });

        const values = {
            nome: $form.find('#nome').val(),
            telefone: $form.find('#telefone').val(),
            email: $form.find('#email').val(),
            sexo: $form.find('input[name=sexo]:checked').val(),
            interesses: interesses
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
        request.done(function(data) {
            verifyErrors();
            showSaved(text, function() {
                location.href = listaURL;
            });
        });
        request.fail(function(err) {
            const errors = err.responseJSON;
            verifyErrors(errors);
        });

    });

});
