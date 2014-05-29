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
        } else {
          $(this).parent(".content").toggleClass("checked");

          var count = $(".vote-count").data("val");
          if($(this).parent(".content").hasClass("checked")) {
            count++;
          } else {
            count--;
          }
          $(".vote-count").data("val", count).text(count);

          if (sel.length == maxItems) {
            $("#info-msg").addClass("complete");
          } else {
            $("#info-msg").removeClass("complete");
          }
        }
    });



    $("#btn-vote").on("click", function() {
      var selection = $(".ballot input:checked").siblings("label").clone();
      if (selection.length > 0 )
        $("#confirmation-content").html(selection);
      else
        $("#confirmation-content").html("Nenhuma opção selecionada");

      $("#confirmation-modal").modal("show");
      $("#info-msg").hide();
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


    $(".ballot .desc-toggle").on("click", function() {
        $(this).closest(".option").children(".desc").slideToggle();
        $(this).toggleClass("less");
    });

});