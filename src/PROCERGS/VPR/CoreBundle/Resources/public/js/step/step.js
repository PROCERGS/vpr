/*global $:false */
$(function() {
  "use strict";

  $(".ballot input").on("click", function(event) {
    var sel = $(".ballot input:checked");
    if (sel.length > oba.maxItens) {
      event.preventDefault();
      alert(oba.label["limit"] + oba.maxItens);
      resetCheckbox($(this).get(0));
    }
  });

  $("#btn-vote").on("click", function() {
    $(".ballot").addClass("animate");

    $(".loader").fadeIn("normal", function() {
      $(".js-toggle").toggle();
      $(".ballot input").not(":checked").parent().addClass("hidden");
      $("input:checked").closest(".options").prev(".step-category").addClass("checked");
      $(".step-category").not(".checked").addClass("hidden");

      $(".loader").delay(1000).fadeOut("normal", function() {
        $(".ballot").removeClass("animate");
        $("#confirmation").show();

        if (!$(".ballot input:checked").length) {
          $("#vote-empty").removeClass("hidden");
        }
      });
    });
  });

  $("#btn-correct").on("click", function() {
    $("#vote-empty").addClass("hidden");
    $(".ballot li, .step-category").removeClass("hidden");
    $(".ballot").removeClass("animate");
    $(".js-toggle").toggle();
  });
});