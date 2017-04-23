/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-08-29 18:52:18
 * @version $Id$
 */
$(document).ready(function() {
	var $speech = $('div.speech');
	var defaultSize = $speech.css('fontSize');
	var num = parseFloat(defaultSize);
	$('#switcher button').click(function() {
		switch (this.id) {
			case 'switcher-large':
				num *= 1.4;
				break;
			case 'switcher-small':
				num /= 1.4;
				break;
			default:
				num = parseFloat(defaultSize);
		}
		$speech.animate({fontSize: num + 'px'}, 'slow');
	});

	var $firstPara = $('p').eq(1)
	$firstPara.hide();
	$('a.more').click(function(event) {
		event.preventDefault();
		$firstPara.animate({
			opacity: 'toggle',
			height: 'toggle' 
			}, 'slow');
		var $link = $(this);
		if ($link.text() == 'read more') {
			$link.text('read less');
		} else {
			$link.text('read more');
		}
	});

	$('div.label').click(function() {
		var paraWidth = $('div.speech p').outerWidth();
		var $switcher = $(this).parent();
		var switcherWidth = $switcher.outerWidth();
		$switcher
		.css({position: 'relative'})
		.fadeTo('fast', 0.5)
		.animate({
			left: paraWidth - switcherWidth
		}, {
			duration:'slow',
			queue: false
		})
		.fadeTo('slow', 1.0)
		.slideUp('slow', function() {
			$switcher.css({backgroundColor: '#f00'});
		})
		.slideDown('slow');
	});

	$('p').eq(2)
		.css('border','1px solid #333')
		.click(function() {
			$clickedItem = $(this);
			$(this).next().slideDown('slow', function() {
				$clickedItem.slideUp('slow');
			});
		});
	$('p').eq(3).css('backgroundColor', '#ccc').hide();

  $('body').hide().fadeIn(3000);//此处的时间可以根据需要自行设置。

  $('p').mouseover(function() {
    $(this).css('backgroundColor', 'yellow')
    .mouseout(function(){
      $(this).css('backgroundColor', 'white')
    });
  });//改进版方法做了扩展，鼠标悬停时，相应段落应用黄色背景色，当鼠标移开后变成白色。

  $('h2').click(function() {
  	$(this)
  	.fadeTo('fast', 0.2)
  	.animate({
  		marginLeft: '+=20px'
  	},{
  		duration: 'slow',
  		queue: false
  	})
  	.queue(function(next) {
  		$('div.speech').fadeTo('slow', 0.5);
  	});
  });

  var key_left = 37;
  var key_up = 38;
  var key_right = 39;
  var key_down = 40;
  $switcher = $('#switcher');
  $switcher.css('position', 'relative');
  $(document).keyup(function(event) {
  	switch(event.which) {
  		case key_left:
  			$switcher
  			.animate({
  				left: '-=20px'
  			},{
  				duration: 'fast'
  				})
  				break;
  		case key_up:
  			$switcher
  			.animate({
  				top: '-=20px'
  			},{
  				duration: 'fast'
  			})
  			break;
  		case key_right:
  			$switcher
  			.animate({
  				left: '+=20px'
  			},{
  				duration: 'fast'
  			})
  			break;
  		case key_down:
  			$switcher
  			.animate({
  				top: '+=20px'
  			},{
  				duration: 'fast'
  			})
  	}	
  });
});
