;(function(){
  'use strict';

  $('.btn-hamburguer').click(function() {
      $(this).toggleClass('active');
      return false;
  });

  $('.menu-show').on('click', function(e) {
      e.preventDefault();
      $('.navbar-toggle-js').toggleClass('navbar-visible');
      $('.content-move-js').toggleClass('side-show');
  });

  $('.navbar-container').on('click',function(e){
    e.stopPropagation();
  });

  $('.navbar-toggle-js').on('click', function(e){
    $('.btn-hamburguer').removeClass('active');
    $('.navbar-toggle-js').removeClass('navbar-visible');
  });
})();
