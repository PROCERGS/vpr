$(function() {
    // add bootstrap classes to forms
    $('.form-group select').not('.form-control').addClass('form-control');

    // add mask input money
    $('.money').mask('000.000.000.000.000,00', {reverse: true});

    // Sortable rows
    /*
    $('.sorted_table tbody').sortable({
        containerSelector: 'tr',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        onDrop:function ($item) {
            $elmTable = $item.closest('table');

            $item.removeClass('dragged').removeAttr('style');
            $('body').removeClass("dragging");

            $elmTable.find('tbody tr').each(function(){
                $(this).find('span.order').html( $(this).index() + 1);
            });

            var dataTable = $elmTable.attr('id');
            $('body').find('a[data-table="'+dataTable+'"]').css('display','inline-block').show();
        }
    });
    */

    // Save sorting
    $('.save-sorted').click(function(){
        var tableId = $(this).attr('data-table');
        $elmTable = $('#'+tableId);
        var dataType = $elmTable.attr('data-type');

        var ids = [];
        $.each($elmTable.find('tbody tr'), function() {
            ids.push($(this).data('id'));
        });

        $btnSave = $(this);
        $btnSave.button('loading');

        switch(dataType){
            case 'step':
              saveSortingUrl = saveStepSortingUrl;
              break;
            case 'pollOption':
              saveSortingUrl = savePollOptionSortingUrl;
              break;
            default:
              alert('error');
              return false;
        }

        $.ajax({
            url: saveSortingUrl,
            type: 'POST',
            data: ({
                ids: ids
            }),
            dataType: 'json',
            success: function(result) {
                if(result.success){
                    $btnSave.button('reset');
                    $btnSave.hide();
                }else{
                    $('.alert-save-sorted')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html(result.message)
                        .show();
                }
            }
        });
        return false;
    });

    $('form.poll-option select#poll_select').change(function(){
        var poll_id = $(this).val();
        var $elmStep = $('#procergs_vpr_corebundle_polloption_step');
        var $elmRlAgency = $('#procergs_vpr_corebundle_polloption_rlAgency');

        $.ajax({
            url: loadPollStepsUrl,
            type: 'POST',
            beforeSend: function(){
                $elmStep.find('option').remove();
                $elmStep.append(new Option('Aguarde...', ''));
                $elmRlAgency.find('option').remove();
                $elmRlAgency.append(new Option('Aguarde...', ''));
            },
            data: ({
                poll_id: poll_id
            }),
            dataType: 'json',
            success: function(result) {
                $elmStep.find('option').remove();
                $elmRlAgency.find('option').remove();

                if(result.success){
                    $.each(result.steps, function(i, data) {
                        $elmStep.append(new Option(data.value, data.id));
                    });
                    $elmStep.removeAttr('disabled');
                    $.each(result.rlAgencys, function(i, data) {
                    	$elmRlAgency.append(new Option(data.value, data.id));
                    });
                    $elmRlAgency.removeAttr('disabled');
                }else{
                    $elmStep.attr('disabled','disabled');
                    $elmRlAgency.attr('disabled','disabled');
                }
            }
        });
    });
});