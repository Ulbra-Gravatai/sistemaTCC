$(function() {

    "use strict";

    const $form = $('#form-js');
    const itemID = $form.find('#id').val();
    const restURL = './tcc/';
    const listaURL = './tcc/';
    
    function verifyErrors(err) {
        const errors = err || {};

        $.each(['titulo', 'aluno', 'semestre', 'disciplina', 'professor'], function(key, value) {
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
        console.log('selecionado e: ', values);

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
                showCancelButton: true,
				cancelButtonText: 'Inserir Banca',
                confirmButtonText: "Voltar para Lista",
                closeOnConfirm: false,
				closeOnCancel: false},
                function(isConfirm) {
                    if(isConfirm){
						location.href = listaURL;
					}else{
						location.href = restURL + 'editar/' + data.tcc.id + '/';
					}
                });
        });

        // Caiu aqui, tem erro
        request.fail(function(err) {
            const errors = err.responseJSON;
            verifyErrors(errors);
        });

    });
	
	/************************************************
	 * Ação para botão adicionar Banca ou Orientador
	 ************************************************/
	$('.add-banca-js').on('click', function(event){
		event.preventDefault();
		if(!itemID){
			swal({
                title: "Erro",
                text: 'Você precisa salvar o TCC para criar a banca.',
                type: "error",
                showCancelButton: false,
                confirmButtonText: "Ok",
                closeOnConfirm: false });
			return false;
		}
		const tccPID = '';
		const url = './tccprofessor/' + (tccPID ? tccPID + '/' : '' );
		const method = tccPID ? 'put' : 'post';
		var values = {
			tipo: $(this).data('tipo'),
			tcc: itemID,
			professor: $('#professor').val()
		};
		values.professor = values.professor.substring(0, values.professor.toString().indexOf('-')).trim()
		const request = $.ajax({
                url: url,
                type: method,
                dataType: 'json',
                data: values
            });
		request.done(function(data) {
			verifyErrors();
			var item = $('.modelo-item-banca').clone().removeClass('modelo-item-banca');
			item.attr('id','tccprofessor-' + data.banca.id);
			item.prepend(data.professor.pessoa.nome);
			item.find('.excluir-professor-js').attr('data-id',data.banca.id);
			if(data.banca.tipo == data.tipo.orientador){
				item.find('.label').addClass('label-warning').text('Orientador');
			}else{
				item.find('.label').addClass('label-info').text('Banca');
			}
			$('.remove-on-add-banca-js').remove();
			$('#banca-list').append(item);
			$('#professor').val('');
		});
		request.fail(function(err) {
            const errors = err.responseJSON;
            verifyErrors(errors);
        });
	});
	
	/**********************************************
	 * Ação para botão excluir Banca ou Orientador
	 **********************************************/
	var swalExcluir = {
		title: "Você tem certeza?",
		text: "Após a exclusão não será possível recupera os dados.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Deletar!",
		cancelButtonText: "Cancelar!",
		closeOnConfirm: false,
		closeOnCancel: false
	};
	var $lista = $('#banca-list');
	$lista.on('click', '.excluir-banca-js', function (e) {
		e.preventDefault();
		var tccPID = $(this).data('id');
		
		if (!tccPID){
			return false;
		}
		
		swal(swalExcluir, function (isConfirm) {
			if (isConfirm) {
				var request = $.ajax({
					url: './tccprofessor/' + tccPID + '/',
					type: 'DELETE',
					dataType: 'json'
				});
				request.done(function (data) {
					swal("Deletado!", "O Professor foi excluído da banca com sucesso!", "success");
					$('#tccprofessor-' + tccPID).remove();
				});
				request.fail(function (data) {
				swal("Erro!", "Erro ao excluir professor da banca, tente novamente", "error");
				});
			} else {
			swal("Cancelado!", "O Professor não foi excluído da banca!", "error");
			}
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
    }).bind('typeahead:selected', function(a, b){
        var x = alunos.filter( function(arg) {
            return arg.nome == b;
        });
        $('#alunoSelecionado').val(x[0].id);
    });

	$('.typeahead.professor-js').typeahead({
      hint: true,
      highlight: true,
      minLength: 1
    },
    {
      name: 'professores',
      source: substringMatcher(professores)
    }).bind('typeahead:selected', function(a, b){
        var x = professores.filter( function(arg) {
            return arg.nome == b;
        });
        $('#professor').val(x[0].id);
    });
});

var substringMatcher = function(alunos) {
  return function findMatches(q, cb) {
    var matches, substringRegex;

    matches = [];
    substrRegex = new RegExp(q, 'i');

    $.each(alunos, function(i, aluno) {
      if (substrRegex.test(aluno)) {
        matches.push(aluno);
      }
    });
    cb(matches);
  };
};