jQuery(document).ready(function($) {
	$(".regiao").change(function() {
		if($(this).val() != -1){
			var regiao_id = $(this).val();
			$.get(regiao, $(location).attr('href',regiao+"?regiao_id="+regiao_id))
		}
	});
});


