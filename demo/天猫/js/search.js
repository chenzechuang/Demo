/* search page */


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

/* selectForm more */

function hideValue(element, n) {
  var $more_value = element.find('ul li:gt('+ n +')');
  $more_value.hide();
}

function toggleValue(element, button, n) {
  var $more_value = element.find('ul li:gt('+ n +')');
  var button_text = '更多';
  $more_value.toggle();
  if (button.text() == button_text) {
    button.text('收起');
    button.append('<i></i>');
    button.find('i').css({
      backgroundPosition: '-2px -72px'
    })
  } else {
    button.text('更多');
    button.append('<i></i>');
    button.find('i').css({
      backgroundPosition: '-2px -66px'
    })
  }
  return false;
}

$(document).ready(function() {
  var banner_form = $('.bannerForm'),
      category_form = $('.categoryForm'),
      banner_toggle = banner_form.find('.valueMore a'),
      category_toggle = category_form.find('.valueMore a');
  hideValue(banner_form, 15);
  hideValue(category_form, 9);
      
  banner_toggle.click(function() {
    toggleValue(banner_form, $(this), 15);
  });

  category_toggle.click(function() {
    toggleValue(category_form, $(this), 9);
  });
});

$(document).ready(function() {
  var sort_button = $('.filterForm a');
  var sortDirection = true;
  sort_button.click(function() {
    sort_button.removeClass('sorted-asc sorted-desc');
    $(this).addClass(sortDirection == true ? 'sorted-asc' : 'sorted-desc');
    sortDirection = !sortDirection;
    return false;
  });
});