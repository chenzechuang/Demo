/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-08-20 20:38:24
 * @version $Id$
 */
$(document).ready(function() {
	////在样式转换器按钮上启用悬停效果
	$('#switcher').hover(function() {	
		$(this).addClass('hover');
	}, function() {
		$(this).removeClass('hover');
	});

	//让样式转换器能够扩展和折叠
	var toggleSwitcher = function(event) {
		if (!$(event.target).is('button')) {
			$('#switcher button').toggleClass('hidden');
		}
	};
	$('#switcher').on('click', toggleSwitcher);

	//模拟一次单击，以便开始时处理折叠状态
	$('#switcher').click();

	//setBodyClass()用于修改页面样式
	//样式转换器的状态也会被更新
	var setBodyClass = function(className) {
		$('body').removeClass().addClass(className);

		$('#switcher button').removeClass('selected');
		$('#switcher-' + className).addClass('selected');

		$('#switcher').off('click', toggleSwitcher);

		if (className == 'default') {
			$('#switcher').on('click', toggleSwitcher);
		}
	};

	//开始的时候先选中switcher-default按钮
	$('#switcher-default').addClass('selected');

	//映射键码和对应的按钮
	var triggers = {
		D: 'default',
		N: 'narrow',
		L: 'large'
	};

	//当按钮被单击时调用setBodyClass()
	$('#switcher').click(function(event) {
		if ($(event.target).is('button')) {
			var bodyClass = event.target.id.split('-')[1];
			setBodyClass(bodyClass);
		}
	});

	//当按下相应按键时调用setBodyClass()
	$(document).keyup(function(event) {
		var key = String.fromCharCode(event.which);
		if (key in triggers) {
			setBodyClass(triggers[key]);
		}
	});

	$('div.author').click(function(event) {
		$(this).toggleClass('selected');
	});

	$('.chapter-title').dblclick(function(event) {
		$(this).nextAll().toggleClass('hidden');
	});

  $(document).mousemove(function(event){
    console.log(event.pageX);
    console.log(event.pageY);
  });

  var mousedownY  
  $(document).mousedown(function(e){  
    mousedownY = e.pageY  
  });  
  $(document).mouseup(function(e){  
    console.log("mousedownY:"+ mousedownY+","+ e.pageY);  
    if (e.pageY == mousedownY){  
        $('p').addClass('hidden');  
    }  
    else if (e.pageY > mousedownY){  
    $('p').removeClass('hidden')  
    }  
  });     
});

