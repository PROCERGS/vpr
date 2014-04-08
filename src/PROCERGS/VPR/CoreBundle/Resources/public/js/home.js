$(function() {

  /*
   * Mobile behavior to register form
   */
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


  /*
   * Show alert about LC
   */
   $("#about-lc-link a").on("click", function(e) {
    e.preventDefault();
    $("#about-lc").toggle();
   });

  /*
   * Add tooltip at register form
   */
   $(".register-tooltip").tooltip({
    "html" : true,
    "placement" : "top",
    "title" : "<a href=''>Encontre seu título clicando aqui</a>",
    "trigger" : "focus"
   });

});
