$(function() {

  $(".step input").on("click", function(event) {
    var sel = $('.step input:checked');
    if (sel.length > oba.maxItens) {
      event.preventDefault();
      alert(oba.label['limit'] + oba.maxItens);
    }
  });

  $("#btn-vote").on("click", function() {
    $(".step").slideUp("slow", function() {
      $('input:checked').closest('.category').prev('dt').addClass('lero');
      $('dd dt').not('.lero').hide();
      $('dd dt').not('.lero').next().hide();
      $("li input").not(':checked').closest('li').hide();
      $(".loader").fadeIn();
      $('#btn-vote').hide();
    });

    setTimeout(function() {
      $(".loader").fadeOut();
      $(".step").slideDown("slow", function() {
        $('#confirmation').show();
      });
    }, 3000);

  });

  $("#btn-cancel").on("click", function() {
    $('dt,dd,li').show().removeClass('lero');
    $('#confirmation').hide();
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