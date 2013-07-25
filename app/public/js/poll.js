$(document).ready(function() {
	$("form dl input[type=checkbox]").change(function () {
        var qtdMaxEscolha = $(this).closest('dl').attr('contextmenu');
		var selectedCount = $(this).closest('dl').find("input[type=checkbox]:checked").length;
		if (selectedCount >= qtdMaxEscolha)
			$(this).closest('dl').find('input[type=checkbox]').not(":checked").attr('disabled', 'disabled');
		if (selectedCount < qtdMaxEscolha)
            $(this).closest('dl').find('input[type=checkbox]').removeAttr('disabled');
	});
});