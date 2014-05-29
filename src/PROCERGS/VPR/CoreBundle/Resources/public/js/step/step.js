/*global $:false, maxItems:false, resetCheckbox, voteEmptyMessage */
$(function() {
    "use strict";

    var infoMessage = $("#info-msg");
    var confirmationModal = $("#confirmation-modal");
    var confirmationContent = $("#confirmation-content");
    var itensCount = $("#info-msg .itens-count");

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
        } else {
          $(this).parent(".content").toggleClass("checked");

          itensCount.text(sel.length);

          if (sel.length == maxItems) {
            infoMessage.addClass("complete");
          } else {
            infoMessage.removeClass("complete");
          }
        }
    });

    confirmationModal.on("hidden.bs.modal", function () {
      infoMessage.show();
    });

    $("#btn-vote").on("click", function() {
      var selection = $(".ballot input:checked").siblings("label").clone().attr("for", "");

      if (selection.length > 0 )
        confirmationContent.html(selection);
      else
        confirmationContent.html(voteEmptyMessage);

      confirmationModal.modal("show");
      infoMessage.hide();
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

    $(".ballot .desc-toggle").on("click", function() {
        $(this).closest(".option").children(".desc").slideToggle();
        $(this).toggleClass("less");
    });

});