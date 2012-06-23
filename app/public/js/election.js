$(document).ready(function() {
	
	if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/iPad/i)) {
		$(document).ready(function () {
			$('label[for]').click(function () {
				var el = $(this).attr('for');
				if ($('#' + el + '[type=radio], #' + el + '[type=checkbox]').attr('selected', !$('#' + el).attr('selected'))) {
					return;
				} else {
					$('#' + el)[0].focus();
				}
			});
		});
	}
	
	$("button.finish").on('click', function(event) {
		$("form.vote").attr('action', reviewURL).submit();
	});
	
	if (previousStep > 0)
		$("button.back").on('click', function(event) {
			$("form.vote").attr('action', previousStepURL).submit();
		});
	else
		$("button.back").hide();
		//$("button.back").attr("disabled", "disabled");
});