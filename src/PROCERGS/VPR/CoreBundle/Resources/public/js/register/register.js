/*global $:false */
$(function() {
    "use strict";

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      // $("footer").css("visibility", "hidden");
      $(".tab-close").show();

      $("html, body").animate({
            scrollTop: $(".tab-content").offset().top
        }, 900);
    });

    $(".tab-close").on("click", function() {
      var self = $(this);
      $("html, body").animate({
            scrollTop: 0
        }, 900, function() {
          self.hide();
        });
    });


    /*
     * Show 'how to vote' text
     */
    $("#how-to-vote").on("click", function() {
      $("#how-to-vote-text").slideToggle();
    });


    /*
     * Mobile behavior to register form
     */
    // var formContainer = $(".register-form-container");

    // $(".register-label").on("click", function() {
    //     if ($(window).width() <= 768) {
    //         formContainer.toggleClass("animate");
    //         $(".register-tooltip").tooltip("hide");
    //     }
    // });

    // $(window).resize(function() {
    //     if ($(window).width() > 768 && formContainer.is(":hidden")) {
    //         formContainer.show();
    //     }

    //     $(".register-tooltip").tooltip("hide");

    // });


    /*
     * Show alert about LC
     */
    $("#about-lc-link a").on("click", function(e) {
        e.preventDefault();
        $("#about-lc").toggleClass("hidden");
    });

});
