$(document).ready(function() {
	if (typeof current_id_regiao === 'undefined' || current_id_regiao == null) current_id_regiao = '00';
	
	current_id_regiao = current_id_regiao.length<2?"0"+current_id_regiao:current_id_regiao;
	$(".container.internal .identification").css('background-image', "url('/images/fundo_identificacao_" + current_id_regiao + ".png')");
	
	$(".group dl dd .toggleDetails").click(function(){
		$(this).siblings(".details").slideToggle();
		return false;
	});
	$(".group dl dd .details").hide();
	
	$("a input[type=button]").each(function() {
		$(this).click(function() { 
			location.href=$(this).closest("a").attr("href");
		});
	}); 
});
