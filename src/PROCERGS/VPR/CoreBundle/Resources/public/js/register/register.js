$(function() {

  /*
   * Register check username
   */
  $(usernameId).on('blur', function () {
    if (!this.value.length) {
      return;
    }
    this.value = completaZerosEsquerda(this.value, 12);
    if (validarTitulo(this.value)) {

    } else {
      alert(messages[0]);
    }

  });


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
    if ($(window).width() > 768 && formContainer.is(":hidden") ) {
      formContainer.show();
    }
  });


  /*
   * Show alert about LC
   */
   $("#about-lc-link a").on("click", function(e) {
    e.preventDefault();
    $("#about-lc").toggleClass("hidden");
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
