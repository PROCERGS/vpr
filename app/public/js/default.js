$(document).ready(function() {
	if (typeof current_id_regiao === 'undefined' || current_id_regiao == null) current_id_regiao = '00';
	
	current_id_regiao = current_id_regiao.length<2?"0"+current_id_regiao:current_id_regiao;
	$(".container.internal .identification").css('background-image', "url('/images/fundo_identificacao_" + current_id_regiao + ".png')");
});
