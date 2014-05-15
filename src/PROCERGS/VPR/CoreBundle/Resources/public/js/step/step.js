/*global $:false, maxItens:false, resetCheckbox */
$(function() {
  "use strict";

  $(".ballot input").on("click", function(event) {
    var sel = $(".ballot input:checked");
    if (sel.length > maxItens) {
      event.preventDefault();
      $("#alert-limit").modal("show");
      resetCheckbox($(this).get(0));
    }
  });

  $("#btn-vote").on("click", function() {
    $("#btn-vote").button("loading");
    $(".ballot").addClass("animate");
    $("body").animate({
      scrollTop: $(".main-title").offset().top
    }, 1000, confirmation);
  });

  function confirmation () {
    $("#btn-vote").hide();
    $(".loader").fadeIn("normal", function() {
      $(".ballot input").not(":checked").parent().addClass("hidden");
      $("input:checked").closest(".options").prev(".step-category").addClass("checked");
      $(".step-category").not(".checked").addClass("hidden");

      $(".loader").delay(1000).fadeOut("normal", function() {
        $(".js-toggle").toggle();
        $(".ballot").removeClass("animate");
        $("#confirmation").show();

        if (!$(".ballot input:checked").length) {
          $("#vote-empty").removeClass("hidden");
        }
      });
    });
  }

  $("#btn-correct").on("click", function() {
    $("#vote-empty").addClass("hidden");
    $(".ballot .options .content, .step-category").removeClass("hidden");
    $(".ballot").removeClass("animate");
    $(".js-toggle").toggle();
    $("#btn-vote").button("reset").show();
  });

  $(".ballot .desc-toggle").on("click", function() {
    $(this).closest("li").find(".desc").toggle();
    $(this).toggleClass("less");
  });

});