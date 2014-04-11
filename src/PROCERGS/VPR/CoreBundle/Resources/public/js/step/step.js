$(function() {

  $(".step input").on("click", function(event) {
    var sel = $('.step input:checked');
    if (sel.length > oba.maxItens) {
      event.preventDefault();
      alert(oba.label['limit'] + oba.maxItens);
      resetCheckbox($(this).get(0));
    }
  });

  $("#btn-vote").on("click", function() {
    $(".step").addClass("animate");
    $(".loader").fadeIn("normal", function() {
      // $('#btn-vote').hide();
      $('#btn-vote').text("Confirmar");
      $(".step input").not(":checked").parent().addClass("hidden"); //hide li
      $("input:checked").closest(".options").prev(".step-category").addClass("checked"); //hide step category
      $(".step-category").not(".checked").addClass("hidden");

      setTimeout(function() {
        $(".loader").fadeOut();
        $(".step").removeClass("animate");
        $('#confirmation').show();
      }, 3000);
    });

  });

  $("#btn-cancel").on("click", function() {
    $(".step").removeClass("animate");
    $(".step li, .step-category").removeClass("hidden")
    $("#confirmation").hide();
    $('#btn-vote').show();
  });

});