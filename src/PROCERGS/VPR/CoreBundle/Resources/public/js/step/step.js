/*global $:false, maxItems:false, resetCheckbox */
$(function() {
    "use strict";

    $(".ballot input").on("click", function(event) {
        var sel = $(".ballot input:checked");
        if (sel.length > maxItems) {
            if (!event.preventDefault) {
                event.preventDefault = function() {
                    event.returnValue = false; //ie
                };
            }
            event.preventDefault();
            $("#alert-limit").modal("show");
            resetCheckbox($(this).get(0));
        }
    });

    $("#btn-vote").on("click", function() {
        $("#btn-vote").button("loading");

        // $("#max-selection").fadeOut();
        // $(".confirm-message").fadeIn();
        $("#info-msg").addClass("confirm").find("p").toggleClass("hidden");

        $("html, body").animate({
            scrollTop: $(".main-title").offset().top
        }, 900);
        $(".ballot").slideUp(1100, confirmation);
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
            scrollTop: $(selector).offset().top
        }, 500);
    });

    function confirmation() {
        $(".confirmation-buttons").show();

        $(".ballot input").not(":checked").closest(".option").addClass("hidden");
        $("input:checked").each(function(i) {
            $(this).closest(".option").prevAll(".step-category").first().addClass("checked");
        });
        $(".step-category").not(".checked").addClass("hidden");

        if (!$(".ballot input:checked").length) {
            $("#vote-empty").removeClass("hidden");
        }

        $(".ballot").slideDown(500, function() {
            $("#btn-vote").hide();
        });
    }

    $("#btn-rectify").on("click", function() {
        $("#info-msg").removeClass("confirm").find("p").toggleClass("hidden");

        $(".ballot").slideUp(500, function() {
            $("#vote-empty").addClass("hidden");
            $(".ballot .option, .step-category").removeClass("hidden");
            $(".step-category").removeClass("checked");
            $("#btn-vote").button("reset").show();
            $(".ballot").slideDown(500);
        });
        $(".confirm-message, .confirmation-buttons").slideUp();
    });

    $(".ballot .desc-toggle").on("click", function() {
        $(this).closest(".option").children(".desc").slideToggle();
        $(this).toggleClass("less");
    });

});