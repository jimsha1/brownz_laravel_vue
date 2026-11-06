var windowWidth = $(window).width();
$( window ).resize(function() {
  windowWidth = $(window).width();
});
$(document).ready(function(){
  if (windowWidth < 768) {
    $('.animateme').each(function(){
      $(this).removeClass('animateme');
    });
  };

  //tooltipy
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });

  scrollme.init();
  var booking_page = $("#booking_page").val();
  if(booking_page !=1)
    $('select').selectric();
  //hlavní swiper
  var swiper1 = new Swiper('.swiper-1', {
    
      navigation: {
      nextEl: '#next-1',
      prevEl: '#prev-1',
    },
    autoplay: {
      delay: 5000,
    },
    effect: 'fade',
    speed: 1000,
    
    
  });
  //naše tipy swiper
  var swiper2 = new Swiper('.swiper-2', {
    
      navigation: {
      nextEl: '#next-2',
      prevEl: '#prev-2',
    },
    effect: 'fade',
    speed: 1000
  });

  //scroll události
  var lastScrollTop = 0;
  $(window).scroll(function(event){
      var st = $(this).scrollTop();
      if (st > lastScrollTop){

      } else {

      }
      lastScrollTop = st;
      if (st%10 == 0) {
        $('.animate-element').each(function(){
          if (st > $(this).offset().top - $(window).outerHeight()/1.5) {
            $(this).addClass($(this).attr('data-add-class'));
          } else {
            if (!(windowWidth < 768 || $(this).hasClass('stay-shown'))) {
              $(this).removeClass($(this).attr('data-add-class'));
            }
          }
        });
      }
  });

  $(window).load(function(e){
    var st = $(this).scrollTop();
    $('.animate-element').each(function(){
      if (st > $(this).offset().top - $(window).outerHeight()/1.5) {
        $(this).addClass($(this).attr('data-add-class'));
      } else {
        if (!(windowWidth < 768 || $(this).hasClass('stay-shown'))) {
          $(this).removeClass($(this).attr('data-add-class'));
        }
      }
    });
  });

  //stejná výška boxů
  $('.same-height').responsiveEqualHeightGrid();
  $('.same-height-2').responsiveEqualHeightGrid();
  var lineHeight = $('.tickets .info-item').outerHeight() + "px";
  $('.tickets .info-item').css('line-height',lineHeight);

  //scroll to anchor
  $('a.scroll-to').on('click', function(e){
      var link = $(this).attr('href'),
          speed = 1000,
          effect = "easeInOutSine";

      $("html, body").stop().animate(
          { scrollTop: $(link).offset().top },
          speed, effect );

      e.preventDefault();
  });

  //checkbox switch slide
  $('.checkbox-type span:first-child').on('click', function() {
    $('.checkbox-type input').prop('checked', false);
    switchClass();
  });
  $('.checkbox-type span:last-child').on('click', function() {
    $('.checkbox-type input').prop('checked', true);
    switchClass();
  });
  $('.checkbox-type').on('click', function(){
    switchClass();
  });

  function switchClass() {
    $('.checkbox-type span').removeClass('active');
    $('.voucher-type-result .result').addClass('hidden-result');
    if ($('.checkbox-type input').is(':checked')) {
      $('.checkbox-type span:first-child').addClass('active');
      $('.voucher-type-result #result2').removeClass('hidden-result');
    } else {
      $('.checkbox-type span:last-child').addClass('active');
      $('.voucher-type-result #result1').removeClass('hidden-result');
    }
  }

  //checkbox - show element
  $('.click-check').on('click', function(){
    if($(this).find('input').is(':checked')) {
      $($(this).attr('data-show')).stop().slideDown();
    } else {
      $($(this).attr('data-show')).stop().slideUp();
    }
    $($(this).attr('data-hide')).stop().slideUp();
  });


  if (windowWidth < 768) {
    $('#calendar').fullCalendar('changeView', 'agendaDay');
  }

  //datetimepicker init
  $('.datetimepicker').datetimepicker({
    stepping: 30,
    showClose: true
  });

  //mobile menu
  $('.has-submenu').on('click', function(){
    if($(this).hasClass('opened')) {
      $(this).removeClass('opened');
      $(this).find('.submenu').slideUp(300);
    } else {
      $(this).addClass('opened');
      $(this).find('.submenu').slideDown(300);
    }
  })
  $('.hamburger').on('click', function(){
    $('.menu-wrap').addClass('opened');
  });
  $('.menu-wrap .close-menu').on('click', function(){
    $('.menu-wrap').removeClass('opened');
  });

  //šipka na tabulce na mobilu
  $('.table-wrap').on('scroll', function(){
    $(this).addClass('scrolled');
  });


  $('#next-2').on('click', function(){
    // swiper-slide swiper-slide-active
    if($('.swiper-2 .swiper-slide-active').css('opacity') == 0) {
      $('.swiper-2 .swiper-slide-active').css('opacity','1');
      $('.swiper-2 .swiper-slide-prev').css('opacity','0');
    }
  });

});


