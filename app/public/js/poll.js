$(document).ready(function() {
	$("form dl input[type=checkbox]").change(function () {
        disabledFields();
	});
    
    disabledFields();
});

function disabledFields(){
    $('#poll').find('dl').each(function() {
        var qtdMaxEscolha = $(this).attr('contextmenu');
        var selectedCount = $(this).find("input[type=checkbox]:checked").length;
        if (selectedCount >= qtdMaxEscolha)
            $(this).find('input[type=checkbox]').not(":checked").attr('disabled', 'disabled');
        if (selectedCount < qtdMaxEscolha)
            $(this).find('input[type=checkbox]').removeAttr('disabled');
    });
}