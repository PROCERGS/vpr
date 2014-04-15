$(function() {

  $(".ballot input").on("click", function(event) {
    var sel = $('.ballot input:checked');
    if (sel.length > oba.maxItens) {
      event.preventDefault();
      alert(oba.label['limit'] + oba.maxItens);
      resetCheckbox($(this).get(0));
    }
  });

  $("#btn-vote").on("click", function() {
    $(".ballot").addClass("animate");

    $(".loader").fadeIn("normal", function() {
      $('#btn-vote').hide();
      $(".ballot input").not(":checked").parent().addClass("hidden");
      $("input:checked").closest(".options").prev(".step-category").addClass("checked");
      $(".step-category").not(".checked").addClass("hidden");

      $(".loader").delay(1000).fadeOut("normal", function() {
        $(".ballot").removeClass("animate");
        $('#confirmation').show();
      });
    });
  });

  $("#btn-cancel").on("click", function() {
    $(".ballot").removeClass("animate");
    $(".ballot li, .step-category").removeClass("hidden")
    $("#confirmation").hide();
    $('#btn-vote').show();
  });

});