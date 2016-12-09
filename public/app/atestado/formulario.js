$(function() {

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './atestado/';
    const listaURL = './atestado/';

    $('.datepicker').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      language: 'pt-BR',
      orientation: 'bottom'
    });


    function verifyErrors(err) {
        const errors = err || {};
        $.each(['tcc_id', 'professor_id', 'data'], function(key, value) {
            const message = errors[value] || false;
            const element = $form.find('#' + value);
            if (message) {
                element.parent().addClass('has-error').find('.help-block').html(message);
            } else {
                element.parent().removeClass('has-error').find('.help-block').html('');
            }
        });
    }

    function formatDate(d) {
        if (!d) {
            return '';
        }
        return d.split('/').reverse().join('-');
    }

    $form.on('submit', function(event) {
        event.preventDefault();

        const values = {
            tcc_id: $form.find('#tcc_id option:selected').val(),
            professor_id: $form.find('#professor_id option:selected').val(),
            data: formatDate($form.find('#data').val())
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
