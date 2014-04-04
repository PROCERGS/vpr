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

            $('.save-sorted').show();
        }
    });
    
    $('.save-sorted').click(function(){
        var actions = [];
        $.each($('tbody tr'), function() {
            actions.push($(this).data('id'));
        });
        
        //console.log(actions);
        
        $('.save-sorted').hide();
    });

});