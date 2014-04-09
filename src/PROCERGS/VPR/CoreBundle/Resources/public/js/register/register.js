$(function() {

  /*
   * Mobile behavior to register form
   */
  var formContainer = $(".register-form-container");

  $(".register-label").on("click", function() {
    if ($(window).width() <= 768) {
      formContainer.toggleClass("slide");
    }
  });

  $(window).resize(function() {
    if ($(window).width() > 768 && registerForm.is(":hidden") ) {
      registerForm.show();
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
