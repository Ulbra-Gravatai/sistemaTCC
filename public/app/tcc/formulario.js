$(function() {

    "use strict";

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './tcc/';
    const listaURL = './tcc/';
    
    function verifyErrors(err) {
        const errors = err || {};

        $.each(['titulo', 'aluno', 'semestre', 'disciplina'], function(key, value) {
            const message = errors[value] || false;
            if (message) {
                $('.group-' + value).addClass('has-error').find('.help-block').html(message);
            } else {
                $('.group-' + value).removeClass('has-error').find('.help-block').html('');
            }
        });
    }

    $form.on('submit', function(event) {
        event.preventDefault();

        const values = {
            titulo: $form.find('#titulo').val(),
            aluno: $form.find('#aluno').val(),
            semestre: $form.find('#semestre').val(),
			disciplina: $form.find('#disciplina').val(),
        };
        values.aluno = values.aluno.substring(0, values.aluno.toString().indexOf('-')).trim();

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

    $('.typeahead.pessoas-js').typeahead({
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'alunos',
      source: substringMatcher(alunos)
    });

});

var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substringRegex;

    matches = [];

    substrRegex = new RegExp(q, 'i');

    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        matches.push(str);
      }
    });

    cb(matches);
  };
};