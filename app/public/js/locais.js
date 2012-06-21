jQuery(document).ready(function($) {
	
	var array_produtos = grupo_urnas.split("|");
	
	$(".municipio").autocomplete({	
		source: array_produtos
	});
	
	$(".municipio").change(function(){		
		$.ajax({
			type: "POST",
			url: municipio,
			data:  "nome_municipio="+ $(this).val(),
			success: function(data) {
				$('.descricao').html(data);
			}
		});
	});
});