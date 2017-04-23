/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2017-03-07 17:39:25
 * @version $Id$
 */
$(document).ready(function() {
  $(".mainNav li").hover(function() {
    $(this).has('div') //有二级菜单的导航
      .children().first().css({ //第一个子元素应用样式
        color: '#E33437',
        backgroundColor: '#fff'
      }).end().end()  //返回$(this)    
        .find('.jnNav').stop().show();
  }, function() {
    $(this)
      .children().first().css({ //第一个子元素应用样式
        color: '#fff',
        backgroundColor: 'transparent'
      }).end().end()  //返回$(this)    
        .find('.jnNav').stop().hide();
  });

  $('#jnProitem ul.imgList li img').bind('click', function(event) {
    var imgSrc = $(this).attr('src');
    var i = imgSrc.lastIndexOf('.');
    var unit = imgSrc.substring(i);
    imgSrc = imgSrc.substring(0, i);
    var imgSrc_big = imgSrc + '_big' + unit;
    $('#thickImg').attr('href', imgSrc_big);
  });

  $div_li = $('.tab_menu ul li');
  $div_li.click(function(event) {
    $(this).addClass('selected')
      .siblings().removeClass('selected');
    var index = $div_li.index(this);
    $('.tab_box > div').eq(index).show()
      .siblings().hide();
  }).hover(function() {
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });

  $('.color_change ul li img').click(function(event) {
    $(this).addClass('hover')
      .parent().siblings().find('img').removeClass('hover');
    var imgSrc = $(this).attr('src');
    var i = imgSrc.lastIndexOf('.');
    var unit = imgSrc.substring(i);
    var imgSrc = imgSrc.substring(0, i);
    var imgSrc_small = imgSrc + '_one_small' + unit;
    var imgSrc_big = imgSrc + '_one_big' + unit;
    $('#bigImg').attr('src', imgSrc_small);
    $('#thickImg').attr('href', imgSrc_big);
    var alt = $(this).attr('alt');
    $('.color_change strong').text(alt);
    var newImgSrc = imgSrc.replace('images/pro_img/', '');
    $('#jnProitem .imgList li').hide();
    $('#jnProitem .imgList').find('.imgList_' + newImgSrc).show()
      .eq(0).find('a').click();
  });

  $('.pro_size li').click(function(event) {
    $(this).addClass('cur')
      .siblings().removeClass('cur');
    $('.pro_size strong').text($(this).text());
  });

  var $span = $('.pro_price strong');
  var price = $span.text();
  $('#num_sort').change(function(event) {
    var num = $(this).val();
    $span.text(num * price);
  }).change();

  $('.rating li a').click(function(event) {
    var title = $(this).attr('title');
    alert('您给商品的评分是: ' + title);
    var cl = $(this).parent().attr('class');
    $(this).parent().parent().removeClass().addClass('rating '+cl+ 'star');
    $(this).blur(); //去掉超链接的虚线框
  });

  var $product = $(".jnProDetail");
  $('#cart a').click(function(event) {
    var pro_name = $product.find('h4:first').text();
    var pro_size = $product.find('.pro_size strong').text();
    var pro_color = $product.find('.color_change strong').text();
    var pro_num = $product.find('#num_sort').val();
    var pro_price = $product.find('.pro_price strong').text();
    var dialog = "感谢您的购买。<div style='font-size:12px;font-weight:400;'>您购买的"+
        "产品是："+pro_name+"；</br>"+
        "尺寸是："+pro_size+"；</br>"+
        "颜色是："+pro_color+"；</br>"+
        "数量是："+pro_num+"；</br>"+
        "总价是："+pro_price +"元。</div>";
    $("#jnDialogContent").html(dialog);
    $('#basic-dialog-ok').modal();
    return false;
  });
});


function showImg(index) {
  var $rollobj = $('.jnImageroll');
  var $rolllist = $rollobj.find('div a');
  var newhref = $rolllist.eq(index).attr('href');
  $('#JS_imgWrap').attr('href', newhref)
    .find('img').eq(index).stop(true, true).fadeIn().siblings().fadeOut();
  $rolllist.removeClass('chos').css('opacity', '0.8')
    .eq(index).addClass('chos').css('opacity', '1');
}

$(document).ready(function() {
  var $imgrolls = $('.jnImageroll div a');
  var len = $imgrolls.length;
  var index = 0;
  var adTimer = null;
  $imgrolls.mouseover(function() {
    index = $imgrolls.index(this);
    showImg(index);
  }).eq(0).mouseover();

  $('.jnImageroll').hover(function() {
    if (adTimer) {
      clearInterval(adTimer);
    }
  }, function() {
    adTimer = setInterval(function() {
      showImg(index);
        index++;
      if (index == len) {
        index = 0;
      }
    }, 3000);
  }).trigger('mouseleave');
});

$(document).ready(function() {
  $('#inputSearch').focus(function() {
    $(this).addClass('focus');
    if ($(this).val() == this.defaultValue) {
      $(this).val('');
    }
  }).blur(function() {
    $(this).removeClass('focus');
    if ($(this).val() == '') {
      $(this).val(this.defaultValue);
    }
  }).keyup(function(event) {
    if (event.which == 13) {
      alert('回车提交列表');
    }
  });
});


$(document).ready(function() {
  var $li = $('#skin li');
  $li.click(function(event) {
    switchSkin(this.id);
  });

  var cookie_skin = $.cookie("MyCssSkin");
  if (cookie_skin) {
    switchSkin(cookie_skin);
  }
});

function switchSkin(skinName) {
  $('#'+skinName).addClass('selected')
    .siblings().removeClass('selected');
    
  $('#cssfile').attr('href', 'css/skin/'+skinName+'.css');
  $.cookie("MyCssSkin", skinName, {path:'/', expires: 10});   
}


$(document).ready(function() {
  var x = 10;
  var y = 20;
  $('a.tooltip').mouseover(function(event) {
    this.myTitle = this.title;
    this.title = "";
    var tooltip = $('<div id="tooltip">' + this.myTitle + '</div>');
    $('body').append(tooltip);
    $('#tooltip')
      .css({
        "top": (event.pageY + y) + 'px',
        "left": (event.pageX + x) + 'px'
      }).show('fast');
  }).mouseout(function(event) {
    this.title = this.myTitle;
    $('#tooltip').remove();
  }).mousemove(function(event) {
    $('#tooltip')
      .css({
        "top": (event.pageY + y) + 'px',
        "left": (event.pageX + x) + 'px'
      });
  });
});


$(document).ready(function() {
  var $aTab = $('.jnBrandTab li a');
  $aTab.click(function(event) {
    $(this).parent().addClass('chos')
      .siblings().removeClass('chos');
    var index = $aTab.index(this);
    showBrandList(index);
    return false;
  }).eq(0).click();

  $('.jnBrandList li').each(function(index) {
    var $img = $(this).find('img');
    var img_w = $img.width();
    var img_h = $img.height();
    var spanHtml = '<span style="position:absolute;top:0;left:5px;width:'+img_w+'px;height:'+img_h+'px;" class="imageMask"></span>';
    $(spanHtml).appendTo(this);
  });


  $('.jnBrandList').on('mouseenter mouseout', '.imageMask', function(event) {
    $(this).toggleClass('imageOver');
  });
});

function showBrandList(index) {
  var $rollobj = $('.jnBrandList');
  var rollWidth = $rollobj.find('li').outerWidth();
  rollWidth = rollWidth * 4;
  $rollobj.stop(true).animate({left: -rollWidth*index}, 1000);
}



