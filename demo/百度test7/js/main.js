/* 表单三级联动 */
$(document).ready(function() {
  var data = {'place':[
    {
      'province':'请选择省份',
      'param1':[{
        'city':'请选择城市',
        'param2':[{
          'area':'请选择区域'
        }]
      }]
    },

    {
      'province':'广东省',
      'param1':[{
        'city':'广州市',
        'param2':[
          {'area':'天河区'},
          {'area':'越秀区'},
          {'area':'白云区'}
        ]
      },
      {
        'city':'深圳市',
        'param2':[
          {'area':'罗湖区'},
          {'area':'宝安区'}
        ]
      },
      {
        'city':'珠海市',
        'param2':[
          {'area':'香洲区'},
          {'area':'金湾区'}
        ]
      }]
    },

    {
      'province':'江苏省',
      'param1':[{
        'city':'南京市',
        'param2':[
          {'area':'玄武区'},
          {'area':'江宁区'},
          {'area':'浦口区'}
        ]
      },
      {
        'city':'无锡市',
        'param2':[
          {'area':'江阴区'},
          {'area':'宜兴区'}
        ]
      }]
    },

    {
      'province':'福建省',
      'param1':[{
        'city':'厦门市',
        'param2':[
          {'area':'思明区'},
          {'area':'海沧区'}
        ]
      },
      {
        'city':'福州市',
        'param2':[
          {'area':'台江区'},
          {'area':'晋安区'}
        ]
      }]
    }
  ]};
  
  var $province = $('#province');
  var $city = $('#city');
  var $area = $('#area');

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