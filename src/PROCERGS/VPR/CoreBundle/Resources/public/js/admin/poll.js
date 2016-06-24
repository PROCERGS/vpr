$(function() {
    $('#chkSyncPPP').change(function(){
    	var self = $(this);
		if (!$('#procergs_vpr_corebundle_poll_closingTime_date_year').val()) {			
			alert('Complete primeiro a Data fechamento');
			self.prop('checked', false);
			return;
		} else {
			$('#procergs_vpr_corebundle_poll_transferYear').val($('#procergs_vpr_corebundle_poll_closingTime_date_year').val());
		}
    });
    $('#procergs_vpr_corebundle_poll_closingTime_date_year').change(function(){
    	var self = $(this);
    	if ($('#chkSyncPPP').prop('checked')) {
    		$('#procergs_vpr_corebundle_poll_transferYear').val($('#procergs_vpr_corebundle_poll_closingTime_date_year').val());
    	}
    });
    $('#chkSyncPPP').trigger('change');
});
