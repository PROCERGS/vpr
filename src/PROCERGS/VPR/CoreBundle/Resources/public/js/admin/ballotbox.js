$(function() {
    $('#procergs_vpr_corebundle_ballotbox_isOnline').change(function(){
        if ($(this).prop('checked')) {
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
    
});