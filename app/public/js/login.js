$(document).ready(function() {
	$("fieldset.optional input").keyup(function(){
		var expand = false;
		$("fieldset.optional input").each(function(){
			if ($(this).val().length > 0)
				expand = true;
		});
		if (expand)
			$("fieldset.optional legend").slideDown();
		else
			$("fieldset.optional legend").slideUp();
		return false;
	});
	
	$("fieldset.optional legend").hide();
});
