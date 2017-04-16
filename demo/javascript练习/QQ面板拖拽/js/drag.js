function getByClass(clsName, parent) {
  var oParent = parent ? document.getElementById(parent) : document;
  var eles = [];
  elements = oParent.getElementsByTagName('*');
  for (var i = 0, l = elements.length; i < l; i++) {
    if (elements[i].className == clsName) {
      eles.push(elements[i]);
    }
  }
  return eles;
}

window.onload = drag;

function drag() {
  var oTitle = getByClass('login_logo_webqq', 'loginPanel')[0];

  oTitle.onmousedown = fndown;

  var oClose = document.getElementById('ui_boxyClose');
  oClose.onclick = function() {
    document.getElementById('loginPanel').style.display = "none";
  };

  var loginState = document.getElementById('loginState'),
      stateList = document.getElementById('loginStatePanel'),
      list = stateList.getElementsByTagName('li'),
      loginStateShoe = document.getElementById('loginStateShow');
      stateText = document.getElementById('login2qq_state_txt'); 

  loginState.onclick = function(e) {
    e = e || window.event;
    if(e.stopPropagation) {
      e.stopPropagation();
    } else {
      e.cancelBubble = true;
    }
    stateList.style.display = "block";
  };

  for (var i = 0, l = list.length; i < l; i++) {
    list[i].onmousemove = function() {
      this.style.backgroundColor = '#567';
    };
    list[i].onmouseout = function() {
      this.style.backgroundColor = '#fff';
    };
    list[i].onclick = function(e) {
      e = e || window.event;
      if(e.stopPropagation) {
        e.stopPropagation();
      } else {
        e.cancelBubble = true;
      }
      var lid = this.id;
      stateList.style.display = 'none';
      stateText.innerHTML = getByClass('stateSelect_text', lid)[0].innerHTML;
      loginStateShow.className = '';
      loginStateShow.className = 'login-state-show ' + lid;
    };
  }
  document.onclick = function() {
    stateList.style.display = "none";
  }
}

function fndown(event) {
  event = event || window.event;
  var oDrag = document.getElementById('loginPanel'),
      disX = event.clientX - oDrag.offsetLeft;
      disY = event.clientY - oDrag.offsetTop;

  document.onmousemove = function(event) {
    event = event || window.event;
    fnmove(event, disX, disY);
  };

  document.onmouseup = function() {
    document.onmousemove = null;
    document.onmousedown = null;
  };
}

function fnmove(e, posX, posY) {
  var oDrag = document.getElementById('loginPanel'),
      l = e.clientX - posX,
      t = e.clientY - posY,
      winW = document.documentElement.clientWidth || document.body.clientWidth,
      winH = document.documentElement.clientHeight || document.body.clientHeigth,
      maxW = winW - oDrag.offsetWidth - 10,
      maxH = winH - oDrag.offsetHeight;
  if(l < 0) {
    l = 0;
  } else if(l > maxW){
    l = maxW;
  }
  if(t < 0) {
    t = 10;
  } else if(t > maxH){
    t = maxH;
  }
  oDrag.style.left = l + 'px';
  oDrag.style.top = t + 'px';
}