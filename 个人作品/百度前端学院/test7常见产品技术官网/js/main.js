/* 表单三级联动 */
$(document).ready(function() {
  var $province = $('#province');
  var $city = $('#city');
  var $area = $('#area');
  $.ajax({
    url: '../place.json',
    type: 'GET',
    dataType: 'json',
  })
  .done(function(data) {
    function select_1() {//构造第一个select
      var html = '';
      var len = data.place.length;
      for(var i = 0; i < len; i++){
        html += '<option class="select-item" value="' + data.place[i].province + '">' + data.place[i].province + '</option>';
      }
      $province.html(html);
      select_2();
    }

    function select_2(n) {//构造第二个select
      var n = n || 0;
      var html = '';
      var len = data.place[n].param1.length;
      for(var i = 0; i < len; i++){
        html += '<option class="select-item" value="' + data.place[n].param1[i].city + '" >' + data.place[n].param1[i].city + '</option>';
      }
      $city.html(html);
      select_3(n);
    }

    function select_3(n, q){//构造第三个select
      var n = n || 0;
      var q = q || 0;
      var html = '';
      var len = data.place[n].param1[q].param2.length;
      for(var i = 0; i < len; i++){
        html += '<option class="select-item" value="' + data.place[n].param1[q].param2[i].area + '" >' + data.place[n].param1[q].param2[i].area + '</option>';
      }
      $area.html(html);
    }

    select_1();//初始化

    $province.change(function() {//绑定第一个select
      var n = $province.find(':selected').index();
      select_2(n);
    });

    $city.change(function() {//绑定第二个select
      var n = $province.find(':selected').index();
      var q = $city.find(':selected').index();
      select_3(n, q);
    });
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
    $oUl.eq(index).stop().addClass('active')
      .siblings().removeClass('active');
  }
});

$(document).ready(function() {
  $(window).scroll(function(event) {
    var w_scroll = $(window).scrollTop();
    var e_top = $('.list').offset().top;
    var w_height = $(window).height();
    if (w_scroll + w_height >= e_top + 100) {
      $('.list_content').find('p').css({
        top: 0
      })
    }
  });
});

$(document).ready(function() {
  $('.activity-img img').hover(function() {
    $(this).addClass('big').removeClass('normal');
  }, function() {
    $(this).addClass('normal').removeClass('big');
  });
});