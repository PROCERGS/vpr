$(function() {
	$(".btn-transfer-poll-option").on("click", function (e) {
        var url = $(this).data('href');
		$.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                // setting a timeout
                addLoader($('#admin-poll-list'));
            },
            success: function(result) {
            	var r = confirm('Tudo certo');                
                window.location.reload();
            },
            error: function(result) {
            	alert('Aconteceu um erro');            	
            },
            complete:function() {
            	removeLoader($('#admin-poll-list'));
            }
        });
    });
	$(".btn-transfer-open-vote").on("click", function (e) {
        var url = $(this).data('href');
		$.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                // setting a timeout
                addLoader($('#admin-poll-list'));
            },
            success: function(result) {
            	var r = confirm('Tudo certo');                
                window.location.reload();
            },
            error: function(result) {
            	alert('Aconteceu um erro');            	
            },
            complete:function() {
            	removeLoader($('#admin-poll-list'));
            }
        });
    });	  
});
