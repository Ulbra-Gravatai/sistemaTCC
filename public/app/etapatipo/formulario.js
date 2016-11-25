$(function() {

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './etapatipo/';
    const listaURL = './etapatipo/';
    const fields = ['nome'];
    let isDone = false;

    function verifyErrors(err) {
        const errors = err || {};
        $.each(fields, function(key, value) {
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

        if (isDone) {
            return false;
        }

        const values = {
            nome: $form.find('#nome').val()
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
            isDone = true;
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
