$(document).ready(function() {
  $('.city:gt(0)').hide();
  var $province = $('#province');
  $province.change(function(event) {
    $('#province option').each(function(i, o) {
      if ($(this).prop('selected')) {
        $('.city').hide();
        $('.city').eq(i).show();
      }
    })
  });
});

$(document).ready(function() {
  $('.newworld-logo').hover(function() {
    $(this).css({
      opacity: '0.9'
    }).find('.logo-info').addClass('hover');
  }, function() {
    $(this).css({
      opacity: '1'
    });
  });
});

$(document).ready(function() {
  var $oUl = $('.newworld-text div ul');
  var len = $oUl.length;
  var $btn = $('.btn-group a');
  var timer = null;
  var index = 0;
  $btn.mouseover(function() {
    index = $btn.index(this);
    show(index);
  });

  $oUl.hover(function () {
    if (timer) {
      clearInterval(timer);
    }
  }, function() {
    autoPlay();
  }).trigger('mouseleave');

  function autoPlay() {
    timer = setInterval(function() {
      show(index);
      index++;
      if (index == len) {
        index = 0;
      }
    }, 3000);
  }
  autoPlay();

  function show(index) {
    $btn.eq(index).addClass('selected')
      .siblings().removeClass('selected');
    $oUl.eq(index).addClass('active')
      .siblings().removeClass('active');
  }
});