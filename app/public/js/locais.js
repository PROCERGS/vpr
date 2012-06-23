jQuery(document).ready(function($) {
	
	/**
	 *	onClick() no jquery.ui.autocomplete.js
	 */
	
//	var tec = function ajaxRequest(){
//		$.ajax({
//			type: "POST",
//			url: municipio,
//			data:  "nome_municipio="+ $(".municipio").val(),
//			success: function(data) {
//				$('.descricao').html(data);
//			}
//		});
//	}
	var array_produtos = grupo_urnas.split("|");
	
	$(".municipio").autocomplete({	
		source: array_produtos
	});
//	$(".municipio").change(tec);
});