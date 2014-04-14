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
      $(".ballot input").not(":checked").parent().addClass("hidden"); //hide li
      $("input:checked").closest(".options").prev(".step-category").addClass("checked"); //hide step category
      $(".step-category").not(".checked").addClass("hidden");

      setTimeout(function() {
        $(".loader").fadeOut();
        $(".ballot").removeClass("animate");
        $('#confirmation').show();
      }, 2000);
    });

  });

  $("#btn-cancel").on("click", function() {
    $(".ballot").removeClass("animate");
    $(".ballot li, .step-category").removeClass("hidden")
    $("#confirmation").hide();
    $('#btn-vote').show();
  });

});