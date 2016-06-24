$(function() {

    function changeColor(type, hex) {
        switch (type) {
            case 'title':
            $(".category-preview .bg").css("background-color", '#' + hex);
            break;
            case 'icon':
            $(".category-preview .icon").css("background-color", '#' + hex);
            break;
            case 'option':
            $(".category-preview .option .content").css("background-color", '#' + hex);
            break;
        }
    }

    $('.colorSelector').ColorPicker({
        onChange: function (hsb, hex, rgb) {
            var el = this.data('colorpicker').el;
            $(el).find('.bg').css('backgroundColor', '#' + hex);
            $(el).parent().prev('.col-input').find(".colorHex").val("#" + hex);

            var type = $(el).data("type");
            changeColor(type, hex);
        }
    });

    $(".cat-name").on("input", function() {
        var name = $(this).val();
        $(".category-preview .name").text(name);
    });

    $(".colorHex").on("input", function() {
        var type = $(this).data("type");
        var hex = $(this).val();
        console.log(hex);
        changeColor(type, hex);
    });

    $(".icon-select").on("change", function() {
        console.log($('.colorSelector').ColorPicker());

        var val = $(this).val();
        $(".category-preview").removeClass (function (index, css) {
            return (css.match (/(^|\s)pre-icon-\S+/g) || []).join(' ');
        }).addClass("pre-icon-" + val);
    })

});