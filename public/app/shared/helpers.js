;(function(context) {

    'use strict';

    context['showSaved'] = function showSaved(text, callback) {
        swal({
            title: "Salvo",
            text: text,
            type: "success",
            showCancelButton: false,
            confirmButtonText: "Fechar",
            closeOnConfirm: false },
            callback);
    };

    context['showConfirmDelete'] = function showConfirmDelete(callback) {
        swal({
            title: "Deseja mesmo excluir?",
            text: "Esse processo irá excluir permanentemente o registro!",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sim, excluir agora!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false }, callback);
    };


    context['showDone'] = function showDone(callback) {
        swal({
            title: "Processo Concluído!",
            text: "O processo foi concluído com sucesso!",
            type: "success",
            showCancelButton: false,
            confirmButtonText: "Fechar",
            closeOnConfirm: false }, callback);
    }

    context['showError'] = function showError(text, callback) {
        swal({
            title: "Ocorreu um Erro!",
            text: text,
            type: "error",
            showCancelButton: false,
            confirmButtonText: "Fechar",
            closeOnConfirm: true });
    };

})(this);