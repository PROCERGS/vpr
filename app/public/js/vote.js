$(document).ready(function() {
	if (previousStep > 0)
		$("button.back").on('click', function(event) {
			$("form.vote").attr('action', previousStepURL).submit();
		});
	else
		$("button.back").attr("disabled", "disabled");
});