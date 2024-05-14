$(document).ready(function () {
  //This function for sticky header
  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $(".top-head").addClass("Sticky-top-head");
    } else {
      $(".top-head").removeClass("Sticky-top-head");
    }
  });

  $('.mob-btn').on('click', function () {
    $('.lft-menu').addClass('slide-right');
    $('.menu-close').fadeIn();
  });
  $('.menu-close').on('click', function () {
    $('.lft-menu').removeClass('slide-right');
    $(this).hide();
  });

});
