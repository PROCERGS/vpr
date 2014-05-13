$(function() {
    // add bootstrap classes to forms
    $('.form-group select').not('.form-control').addClass('form-control');

    // add mask input money
    $('.money').mask('000.000.000.000.000,00', {reverse: true});

    // Sortable rows
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

    // Save sorting
    $('.save-sorted').click(function(){
        var steps = [];
        $.each($('tbody tr'), function() {
            steps.push($(this).data('id'));
        });

        $btnSave = $(this);
        $btnSave.button('loading');

        $.ajax({
            url: saveStepSortingUrl,
            type: 'POST',
            data: ({
                steps: steps
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
    });

    $('form.poll-option select#poll_select').change(function(){
        var poll_id = $(this).val();
        var $elmStep = $('#procergs_vpr_corebundle_polloption_step');

        $.ajax({
            url: loadPollStepsUrl,
            type: 'POST',
            beforeSend: function(){
                $elmStep.find('option').remove();
                $elmStep.append(new Option('Aguarde...', ''));
            },
            data: ({
                poll_id: poll_id
            }),
            dataType: 'json',
            success: function(result) {
                $elmStep.find('option').remove();

                if(result.success){
                    $.each(result.steps, function(i, data) {
                        $elmStep.append(new Option(data.value, data.id));
                    });
                    $elmStep.removeAttr('disabled');
                }else{
                    $elmStep.attr('disabled','disabled');
                }
            }
        });
    });
});