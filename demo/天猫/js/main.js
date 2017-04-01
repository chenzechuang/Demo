
/*common js*/

function getByClass(clsName, parent) {
  var oParent = parent ? document.getElementById(parent) : document;
  var eles = [];
  elements = oParent.getElementsByTagName("*");
  for (var i = 0, l = elements.length; i < l; i++) {
    if (elements[i].className == clsName) {
      eles.push(elements[i]);
    }
  }
  return eles;
}

function addClass(element,value) {
  if (!element.className) {
    element.className = value;
  } else {
    newClassName = element.className;
    newClassName += " ";
    newClassName += value;
    element.className = newClassName;
  }
}

function addLoadEvent(func) {
  var oldonload = window.onload;
  if(typeof window.onload !="function") {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    };
  }
}


/* topNav */

function showNav() {
  var sitemap = getByClass('sitemap', 'quick-menu')[0],
      site_content = sitemap.getElementsByTagName('div')[1],
      oldClass = sitemap.className;
  sitemap.onmouseover = function() {
    addClass(this, 'hover');
    site_content.style.display = 'block';
  };
  sitemap.onmouseout = function() {
    this.className = oldClass;
    site_content.style.display = 'none';
  };
}
addLoadEvent(showNav);


/* 天猫图标 */

function showPic() {
  var content = document.getElementById('content'),
      nav_menu = content.getElementsByTagName('ul')[0],
      oLi = nav_menu.getElementsByTagName('li'),
      oDiv = nav_menu.getElementsByTagName('div');
  for (var i = 0, l = oLi.length; i < l; ++i) {
    oLi[i].index = i;
    oLi[i].onmouseover = function() {
      for (var j = 0, n = oDiv.length; j < n; ++j) {
        oDiv[this.index].style.display = 'block';
      }
    };
    oLi[i].onmouseout = function() {
      for (var j = 0, n = oDiv.length; j < n; ++j) {
        oDiv[j].style.display = 'none';
      }
    };
  }
}
addLoadEvent(showPic);


/*左侧列表*/

function showCategory() {
   var content = document.getElementById('content'),
      nav_menu = content.getElementsByTagName('ul')[1],
      oLi = nav_menu.getElementsByTagName('li'),
      oDiv = getByClass('category-main-content', 'content');

  for (var i = 0, l = oLi.length; i < l; ++i) {
    oLi[i].index = i;
    oLi[i].onmouseover = function() {
      this.className = 'hover';
      for (var j = 0, n = oDiv.length; j < n; ++j) {
        oDiv[j].count = j;
        oDiv[j].onmouseover = function() {
          //鼠标移动到右侧列表，左侧列表依旧高亮
          oLi[this.count].className = 'hover';
          this.style.display = 'block';
        };
        oDiv[this.index].style.display = 'block';
      }
    };
    oLi[i].onmouseout = function() {
      this.className = '';
      for (var j = 0, n = oDiv.length; j < n; ++j) {
        oDiv[j].count = j;
        oDiv[j].onmouseout = function() {
          oLi[this.count].className = '';
          this.style.display = 'none';
        };
        oDiv[this.index].style.display = 'none';
      }
    };  
  }
}

addLoadEvent(showCategory);

/* 轮播图 */
function showCarousel() {
  var oBox = document.getElementById("Carousel"),
      oImg = oBox.getElementsByTagName('img'),
      oBgc = oBox.getElementsByTagName('div')[0],
      oUl = oBox.getElementsByTagName('ul')[0],
      oCount = oUl.getElementsByTagName('li'),
      play = null,
      timer = null,
      index = 0;
  
  for (var i = 0, l = oCount.length; i < l; ++i) {
    oCount[i].index = i;
    oCount[i].onmouseover = function() {
      show(this.index);
    };
  }

  oBox.onmouseover = function() {
    clearInterval(play);
  };

  oBox.onmouseout = function() {
    autoPlay();
  };
  //自动播放
  function autoPlay() {
    play = setInterval(function() {
      index++;
      if (index >= oImg.length) {
        index = 0;
      }
      show(index);
    }, 4000);
  }
  autoPlay();

  //渐变显示
  function show(a) {
    index = a;
    var alpha = 0;
    //更换背景颜色
    switch(index) {
      case 0:
        oBgc.style.backgroundColor = '#E8E8E8';
        break;
      case 1:
        oBgc.style.backgroundColor = '#34B1E7';
        break;
      case 2:
        oBgc.style.backgroundColor = '#53C3F1';
        break;
      case 3:
        oBgc.style.backgroundColor = '#A1BB1C';
        break;
      case 4:
        oBgc.style.backgroundColor = '#E53C12';
        break;
    }
    for (var i = 0, l = oCount.length; i < l; ++i) {
      oCount[i].className = "";
    }
    oCount[index].className = 'selected';
    clearInterval(timer);

    for (var i = 0, l = oImg.length; i < l; ++i) {
      oImg[i].style.opacity = 0;
      oImg[i].style.filter = 'alpha(opacity=0)';    
    }

    timer = setInterval(function() {
      alpha += 2;
      alpha > 100 && (alpha = 100);
      oImg[index].style.opacity = alpha / 100;
      oImg[index].style.filter = 'alpha(opacity = ' + alpha + ')';
      alpha == 100 && clearInterval(timer);
    },20);
  }
}

addLoadEvent(showCarousel);


/* brandMaskShow */
function brandMaskShow() {
  var brand_box = document.getElementById('HotBrand'),
      oLi = brand_box.getElementsByTagName('li'),
      oMask = getByClass('brand-mask', 'HotBrand');
  for (var i = 0, l = oLi.length; i < l; ++i) {
    oLi[i].index = i;
    oLi[i].onmouseover = function() {
      oMask[this.index].style.opacity = 0.8;
      oMask[this.index].style.filter = 'alpha(opacity=80)';
    };
    oLi[i].onmouseout = function() {
      for (var j = 0, l = oMask.length; j < l; ++j) {
        oMask[j].style.opacity = 0;
        oMask[j].style.filter = 'alpha(opacity=0)';
      }
    }
  }
}

addLoadEvent(brandMaskShow);

/* 定位导航 */

function showProducts() {
  var menu = document.getElementById('fixedMenu'),
  products = getByClass('shopProduct', 'content'),
  one_top = products[0].offsetTop,
  items = menu.getElementsByTagName('a'),
  fixedSearch = document.getElementById('fixedSearchBox'),
  conID = '';
  for(var i = 0; i < items.length; i++) {
    items[i].index = i;
    //点击定位
    items[i].onclick = function() {
      var top = products[this.index].offsetTop;
      document.documentElement.scrollTop = document.body.scrollTop = top + 100;
      for (var i = 0, l = items.length; i < l; ++i) {
        items[i].className = '';
      }   
      this.className = 'current';
      return false;
    };  
  }

  //滚动监听高亮
  function scroll() {
    var scroll = document.documentElement.scrollTop || document.body.scrollTop;
    if (scroll > one_top - 200) {
      menu.style.display = 'block';
      fixedSearch.style.display = 'block';
    } else {
      menu.style.display = 'none';
      fixedSearch.style.display = 'none';
    }
    for (var i = 0, l = products.length; i < l; i++) { 
      var top = products[i].offsetTop; 
      if (scroll > top - 100) {  //如果滚动条到顶部的距离大于哪一个div的offsetTop到顶部的距离就把items的ID复制给conID;
        conID = products[i].id;
      } else {
        break;
      }
    }

    if (conID) {
      for(var i = 0; i < items.length; i++) {
        //获取a元素的链接，用#号分隔成数组
        var href = items[i].href.split("#");
        //如果数组的最后一项的值等于conID，那么给这个a赋值类名current
        if (href[1] == conID) {
          items[i].className = "current";
        } else {
          items[i].className = "";
        }
      }
    }
  }

  window.onscroll = function () {
    scroll();
  };
}

addLoadEvent(showProducts);

/* 返回顶部 */
function toTop() {
  var top_btn = document.getElementById('top');
  top_btn.onmouseover = function() {
    this.className = 'current';
  };
  top_btn.onmouseout = function() {
    this.className = '';
  };
  top_btn.onclick = function() {
    document.documentElement.scrollTop = document.body.scrollTop = 0;
  };
}

addLoadEvent(toTop);