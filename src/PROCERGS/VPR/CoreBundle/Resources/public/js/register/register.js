/*global $:false */
$(function() {
  "use strict";

  var btnTabs = $(".btn-tab");
  var tabClose = $(".main-content .tab-close");
  var tabContentTop = $(".tab-content").offset().top;

  btnTabs.on("shown.bs.tab", function () {
    btnTabs.removeClass("active");
    $(this).addClass("active");

    tabClose.show();

    $("html, body").animate({
      scrollTop: tabContentTop
    }, 900);
  });

  tabClose.on("click", function() {
    var self = $(this);
    $("html, body").animate({
      scrollTop: 0
    }, 900, function() {
      self.hide();
    });
  });


  // Show 'how to vote' text
  $("#how-to-vote").on("click", function() {
    $("#how-to-vote-text").slideToggle();
  });


  // Show alert about LC
  $("#about-lc-link a").on("click", function(e) {
    e.preventDefault();
    $("#about-lc").toggleClass("hidden");
  });

    $('#fos_user_registration_form_mobile').mask("(00) 0000-00009").focusout(function () {
        var phone, element;
        element = $(this); element.unmask();
        phone = element.val().replace(/\D/g, '');
        if (phone.length > 10) {
            element.mask("(00) 90000-0000");
        } else {
            element.mask("(00) 0000-00009");
        }
    });

});
