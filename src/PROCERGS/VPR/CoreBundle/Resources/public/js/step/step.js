/*global $:false, maxItens:false, resetCheckbox */
$(function() {
  "use strict";

  $(".ballot input").on("click", function(event) {
    var sel = $(".ballot input:checked");
    if (sel.length > maxItens) {
      event.preventDefault();
      $("#alert-limit").modal("show");
      resetCheckbox($(this).get(0));
    } else if ($("#confirmation").is(":visible")) {
    	event.preventDefault();
    }
  });

  var ballotHeight = $(".ballot").outerHeight(true) + 32;
  $("#btn-vote").on("click", function() {
    $("#btn-vote").button("loading");
    $("body").animate({
      scrollTop: $(".main-title").offset().top
    }, 1000, confirmation);
  });

  function confirmation () {
    $(".ballot").delay(500).addClass("animate");
    $(".js-confirm").delay(200).fadeIn();

    setTimeout(function() {
      $(".ballot input").not(":checked").parent().addClass("hidden");
      $("input:checked").closest(".options").prev(".step-category").addClass("checked");
      $(".step-category").not(".checked").addClass("hidden");
      $(".ballot").height(ballotHeight).delay(3000).removeClass("animate");
      $(".js-toggle").delay(3000).toggle();

      if (!$(".ballot input:checked").length) {
        $("#vote-empty").removeClass("hidden");
      }
    }, 1500);
  }

  $("#btn-correct").on("click", function() {
    $(".js-confirm").hide();
    $("#vote-empty").addClass("hidden");
    $(".ballot .options .content, .step-category").removeClass("hidden");
    $(".ballot .options .content, .step-category").removeClass("checked");
    $(".ballot").removeClass("animate");
    $(".js-toggle").toggle();
    $("#btn-vote").button("reset").show();
  });

  $(".ballot .desc-toggle").on("click", function() {
    $(this).closest("li").find(".desc").toggle();
    $(this).toggleClass("less");
  });

});