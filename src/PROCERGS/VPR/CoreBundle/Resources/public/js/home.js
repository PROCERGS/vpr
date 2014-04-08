$(function() {

  var voteDocForm = $(".vote-doc").next("form");

  $(".vote-doc").on("click", function() {
    if ($(window).width() <= 768) {
      voteDocForm.slideToggle();
    }
  });

  $( window ).resize(function() {

    if ($(window).width() > 768 && voteDocForm.is(":hidden") ) {
      voteDocForm.show();
    }

  });

});
