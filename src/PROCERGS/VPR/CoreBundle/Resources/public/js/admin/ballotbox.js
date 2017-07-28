$(function() {
    $("#btn-load-ballotbox").click(function() {
        $("form input[data-val], form select[data-val]").each(function() {
            var val = $(this).data("val");
            $(this).val(val);
        });

        var openingTime = $(".opening-time");
        var closingTime = $(".closing-time");

        if (openingTime.data("day") || closingTime.data("day")) {
            $("form .opening-time select[id$='date_day']").val(openingTime.data("day"));
            $("form .opening-time select[id$='date_month']").val(openingTime.data("month"));
            $("form .opening-time select[id$='date_year']").val(openingTime.data("year"));
            $("form .opening-time select[id$='time_hour']").val(parseInt(openingTime.data("hour")));
            $("form .opening-time select[id$='time_minute']").val(parseInt(openingTime.data("minute")));

            $("form .closing-time select[id$='date_day']").val(closingTime.data("day"));
            $("form .closing-time select[id$='date_month']").val(closingTime.data("month"));
            $("form .closing-time select[id$='date_year']").val(closingTime.data("year"));
            $("form .closing-time select[id$='time_hour']").val(parseInt(closingTime.data("hour")));
            $("form .closing-time select[id$='time_minute']").val(parseInt(closingTime.data("minute")));

            $("#chkDtDiffPoll").prop('checked', true);
            $('.ballotbox-offline-time-itens').show();
        }


    })

    var isOnline = $('#procergs_vpr_corebundle_ballotbox_isOnline');
    var isSms = $('#procergs_vpr_corebundle_ballotbox_isSms');

    $('#procergs_vpr_corebundle_ballotbox_isOnline, #procergs_vpr_corebundle_ballotbox_isSms').change(function(){
        if (isOnline.prop('checked') || isSms.prop('checked')) {
        	$('.ballotbox-offline-itens').hide();
        	$('#procergs_vpr_corebundle_ballotbox_city').val('');
        	$('#procergs_vpr_corebundle_ballotbox_city').prop('selectedIndex', '');
        } else {
        	$('.ballotbox-offline-itens').show();
        }
    });
    $('#chkDtDiffPoll').change(function(){
    	var self = $(this);
    	if (!self.prop('checked')) {
        	$('.ballotbox-offline-time-itens').hide();
        	$('.ballotbox-offline-time-itens select').val('');
        	$('.ballotbox-offline-time-itens select').prop('selectedIndex', '');
        } else {
        	if ($('.ballotbox-offline-time-itens select').val() == '') {
        		if (!$('#procergs_vpr_corebundle_ballotbox_poll').val()) {
        			alert('Selecione um votação primeiro');
        			self.prop('checked', false);
        			return;
        		}
        		$.ajax({
                    url: admin_select_poll,
                    type: 'POST',
                    data: {
                        poll_id: $('#procergs_vpr_corebundle_ballotbox_poll').val()
                    },
                    dataType: 'json',
                    success: function(result) {
                        if(result.poll){
                        	var openingTime = result.poll.openingTime.split(/[^0-9]/);
                        	var closingTime = result.poll.closingTime.split(/[^0-9]/);

                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_day').val(openingTime[2]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_month').val(openingTime[1]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_date_year').val(openingTime[0]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_hour').val(openingTime[3]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_openingTime_time_minute').val(openingTime[4]*1);

                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_day').val(closingTime[2]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_month').val(closingTime[1]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_date_year').val(closingTime[0]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_hour').val(closingTime[3]*1);
                        	$('#procergs_vpr_corebundle_ballotbox_closingTime_time_minute').val(closingTime[4]*1);
                        	$('.ballotbox-offline-time-itens').show();
                        }
                    },
                    error: function(result) {
                    	alert('Aconteceu um erro');
                    	self.prop('checked', false);
                    }
                });
        	}
        }
    });

    $('#procergs_vpr_corebundle_ballotbox_isOnline').trigger('change');
    $('#chkDtDiffPoll').trigger('change');
    $('form').submit(function(){            
    	$("body").append("<div class='ajax-bg'><div class='ajax-loader'></div><div class='ajax-txt'>Aguarde...</div></div>");
    });

});
