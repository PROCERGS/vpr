$(function() {

  $(".step input").on("click", function(event) {
    var self = $(this);
    var sel = $('.step input:checked');
    if (sel.length > oba.maxItens) {
      event.preventDefault();
      alert(oba.label['limit'] + oba.maxItens);
      self.siblings("label").click();
    }


  });

  $("#btn-vote").on("click", function() {

    $(".step").addClass("slide");
    $(".loader").fadeIn();

    setTimeout(function() {
      $('#btn-vote').hide();
      $(".step input").not(":checked").parent().addClass("hidden"); //hide li
      $("input:checked").closest(".options").prev(".step-category").addClass("checked"); //hide step category
      $(".step-category").not(".checked").addClass("hidden");
    }, 2000);

    setTimeout(function() {
      $(".loader").fadeOut();
      $(".step").removeClass("slide");
      $('#confirmation').show();
    }, 3000);

  });

  $("#btn-cancel").on("click", function() {
    $(".step").removeClass("slide");
    $(".step li, step-category").removeClass("hidden")
    $("#confirmation").hide();
    $('#btn-vote').show();
  });

});


// var oba = {};
// oba.label = {'limit' : '{{ 'step.limit.selection' |trans }}'};
// oba.maxItens = {{ step.getMaxSelection }};
// $(document).on('click', '.step input', function (event) {
//   var sel = $('.step input:checked');
//   if (sel.length > oba.maxItens) {
//     event.preventDefault();
//     alert(oba.label['limit'] + oba.maxItens);
//     }
// });
// $(document).on('click', '#btn-vote', function(event) {
//   //debugger;
//   $('input:checked').parents('.category').prev('dt').addClass('lero');
//   $('dd dt').not('.lero').slideUp('medium');
//   $('dd dt').not('.lero').next().slideUp('medium');
//   $("li input").not(':checked').parents('li').slideUp('medium');
//   $('#btn-vote').hide();
//   $('#confirmation').show();
// });
// $(document).on('click', '#btn-cancel', function(event) {
//   $('dt,dd,li').slideDown('medium').removeClass('lero');
//   $('#confirmation').hide();
//   $('#btn-vote').show();
// });