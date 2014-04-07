$(function() {
    // add bootstrap classes to forms
    $('.form-group select').not('.form-control').addClass('form-control');

    // Sortable rows
    $('.sorted_table tbody').sortable({
        containerSelector: 'tr',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        onDrop:function ($item) {
            $item.removeClass("dragged").removeAttr("style");
            $("body").removeClass("dragging");

            $('tbody tr').each(function(){
                $(this).find('span.order').html( $(this).index() + 1);
            });

            $('.alert-save-sorted').hide();
            $('.save-sorted').show();
        }
    });

    // Save sorting
    $('.save-sorted').click(function(){
        var steps = [];
        $.each($('tbody tr'), function() {
            steps.push($(this).data('id'));
        });

        $(this).button('loading');

        $.ajax({
            url: saveStepSortingUrl,
            type: 'POST',
            data: ({
                steps: steps
            }),
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $('.alert-save-sorted').removeClass('alert-danger').addClass('alert-success')
                    $('.save-sorted').hide();
                }else{
                    $('.alert-save-sorted').removeClass('alert-success').addClass('alert-danger')
                }
                $('.alert-save-sorted').html(result.message).show();
            }
        });        
    });

});