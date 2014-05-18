/*global $:false */
$(function() {
    "use strict";
    /*
     * Mobile behavior to register form
     */
    var formContainer = $(".register-form-container");

    $(".register-label").on("click", function() {
        if ($(window).width() <= 768) {
            formContainer.toggleClass("animate");
            $(".register-tooltip").tooltip("hide");
        }
    });

    $(window).resize(function() {
        if ($(window).width() > 768 && formContainer.is(":hidden")) {
            formContainer.show();
        }

        $(".register-tooltip").tooltip("hide");

    });


    /*
     * Show alert about LC
     */
    $("#about-lc-link a").on("click", function(e) {
        e.preventDefault();
        $("#about-lc").toggleClass("hidden");
    });


    /*
     * Add tooltip at register form
     */
    $(".register-tooltip").on({
        focusin: function() {
            $(".tre-search-link").fadeIn().css("display", "block");
        },
        focusout: function() {
            $(".tre-search-link").fadeOut();
        }
    });

});
