jQuery(document).ready(function($) {
	
	var tec = function ajaxRequest(){
		$.ajax({
			type: "POST",
			url: municipio,
			data:  "nome_municipio="+ $(this).val(),
			success: function(data) {
				$('.descricao').html(data);
			}
		});
	}
	
	var array_produtos = grupo_urnas.split("|");
	
	$(".municipio").autocomplete({	
		source: array_produtos
	
	});
	$(".municipio").change(tec);
	$(".municipio").click(tec);
});