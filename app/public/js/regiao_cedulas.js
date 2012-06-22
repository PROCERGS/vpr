jQuery(document).ready(function($) {
	
	$(".regiao").change(function() {
		
		var regiao_id = $(this).val();
		
		$.get(regiao, $(location).attr('href',regiao+"?regiao_id="+regiao_id))});

});


