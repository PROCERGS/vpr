/*global $:false, maxItems:false, resetCheckbox, voteEmptyMessage */
$(function() {
    "use strict";

    var infoMessage = $("#info-msg");
    var confirmationModal = $("#confirmation-modal");
    var confirmationContent = $("#confirmation-content");
    var itensCount = $("#info-msg .itens-count");

    function updateInfo(sel) {
        if (sel.length == maxItems) {
            infoMessage.addClass("complete");
            infoMessage.removeClass("limit");
        } else if (sel.length > maxItems) {
            infoMessage.removeClass("complete");
            infoMessage.addClass("limit");
        } else {
            infoMessage.removeClass("complete limit");
        }
        itensCount.text(sel.length);
    }

    $("#ballot-main input[type='checkbox']").on("click", function(event) {
        var sel = $("#ballot-main input:checked");

        if (sel.length > maxItems && $(this).is(":checked")) {
            if (!event.preventDefault) {
                event.preventDefault = function() {
                    event.returnValue = false; //ie
                };
            }
            event.preventDefault();
            $("#alert-limit").modal("show");
            resetCheckbox($(this).get(0));
        } else {

            if ($(this).is(":checked"))
                $(this).parent(".content").addClass("checked");
            else
                $(this).parent(".content").removeClass("checked");

            updateInfo(sel);
        }
    });

    confirmationModal.on("hidden.bs.modal", function() {
        infoMessage.show();
    });

    $("form.step-form").on("submit", function() {
        if ($('form.step-form').is('.confirmed')) {
            return true;
        }
        var sel = $("#ballot-main input:checked").closest(".option").clone();
        sel.each(function() {
            $(this).find("input[type='checkbox']").remove();
            $(this).find("label").attr("for", "");
            $(this).find(".content").removeClass("checked");
        });

        if (sel.length > 0 && sel.length <= maxItems) {
            confirmationContent.html(sel);
            infoMessage.hide();
            confirmationModal.modal("show");
        }
        if (sel.length <= 0) {
            confirmationContent.html(voteEmptyMessage);
            infoMessage.hide();
            confirmationModal.modal("show");
        }
        if (sel.length > maxItems) {
            $("#alert-limit").modal("show");
        }

        return false;
    });

    $("#btn-confirm").on('click', function() {
        $('form.step-form').addClass('confirmed').submit();
    });

    $("#dropdown-categories .scrollTo").on("click", function(event) {
        if (!event.preventDefault) {
            event.preventDefault = function() {
                event.returnValue = false; //ie
            };
        }
        event.preventDefault();
        var id = $(this).attr("href").replace("#", "");
        var selector = "a[name=" + id + "]";
        $("html, body").animate({
            scrollTop: $(selector).offset().top - 100
        }, 500);
    });

    $("#ballot-main .desc-toggle").on("click", function() {
        $(this).closest(".option").children(".desc").slideToggle();
        $(this).toggleClass("less");
    });

    //init
    updateInfo($("#ballot-main input:checked"));

});