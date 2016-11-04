$(function() {
	/**
	 * Máscaras de campo
	 * Arquivo para máscaras comuns em todo o sistema
	 * Adicionar link para os scripts:
	 * 	<script src="./app/shared/jquery-mask-plugin.js"></script>
	 *	<script src="./app/shared/mascaras-padrao.js"></script>
	 * Em todas as views que possuem campos com formatação padrão.
	 *
	 * - Máscaras atuais
	 *   - Telefone: '(00) 00000-0000' e '(00) 0000-00009'
	 */
	//Mascara para campo telefone
	var maskTelefone = function (val) {
		return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	options = {onKeyPress: function(val, e, field, options) {
			field.mask(maskTelefone.apply({}, arguments), options);
		}
	};
	
	$('.mask-phone-js').mask(maskTelefone, options);

});
