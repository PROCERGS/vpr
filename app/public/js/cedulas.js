$(document).ready(function() {
	
	var municipioCache = {}, lastXhr;
	$(".municipio").autocomplete({
		minLength: 3,
		source: function (request, response) {
			var nm_municipio = request.term;
			if (nm_municipio in municipioCache) {
				response(municipioCache[nm_municipio]);
			}
			
			lastXhr  = $.getJSON(municipiosSearchURL, request, function (data, status, xhr) {
				console.log(data);
				municipioCache[nm_municipio] = data;
				if (xhr === lastXhr) {
					response(data);
				}
			});
		},
		select: function (event, ui) {
			$("#regiao_id").val(ui.item.id);
		}
	}).show();
	$("label[for=nm_municipio]").show();
	
	$("#regiao_id").change(function() {
		$(".municipio").val("");
	});
	
	$("form").submit(function () {
		if($("#regiao_id").val() == -1){
			$('.flash').html("<p>Informe a região ou escreva um município correspondente.</p>");
			$('.flash').addClass('error');
			return false;
		}
	});	
});
