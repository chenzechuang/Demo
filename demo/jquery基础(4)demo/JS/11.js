/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-21 23:52:12
 * @version $Id$
 */
$(document).ready(function() {
	$('div.member').on('mouseenter mouseleave', function(event) {
		var size = event.type == 'mouseenter' ? 85 : 75;
		var padding = event.type == 'mouseenter' ? 0 : 5;
		$(this).find('img').stop(true, true).animate({
			width: size,
			height: size,
			paddingTop: padding,
			paddingLeft: padding
		}); 
	});

	$('#fx-toggle').show().on('click', function() {
    $.fx.off = !$.fx.off;
  });

	$.fx.speeds._default = 250;
  $.fx.speeds.zippy = 1000;

  function showDetails () {
  	var $member = $(this);
  	if ($member.hasClass('active')) {
      if ($.fx.off) {
        $.fx.off = !$.fx.off;
      }
      if($movable) { 
  		  $movable.slideUp('slow', function() {
          $member.removeClass('active').children('div').fadeOut('fast')
        });   
  	   return;
      }
    }

    $movable.fadeOut();
  	$('div.member.active')
  		.removeClass('active')
  		.children('div').fadeOut();
    $member.addClass('active');
  	
    $(this).find('div').css({
  		display: 'block',
  		left: '-300px',
  		top: 0
  	}).each(function(index) {
  		$(this).animate({
  			left: 0,
  			top: 25 * index
  		}, {
  			duration: 'slow',
  			specialEasing: {
  				top: 'easeInQuart'
  			}
  		});
  	}).promise().done(showBio);
  }
  $('div.member').click(showDetails);

  var $movable = $('<div id="movable"></div>')
  	.appendTo('body');
  var bioBaseStyles = {
  	display: 'none',
  	height: '5px',
  	width: '25px'
  },
  bioEffects = {
  	duration: 'zippy',
  	easing: 'easeOutQuart',
  	specialEasing: {
  		opacity: 'linear'
  	}
  };

  function showBio() {
  	var $member = $(this).parent(),
  			$bio = $member.find('p.bio'),
  			startStyles = $.extend(bioBaseStyles, $member.offset()),
  			endStyles = {
  				width: $bio.width(),
  				top: $member.offset().top + 10,
  				left: $member.width() + $member.offset().left - 5,
  				opacity: 'show'
  			};
  	$movable
  		.html($bio.clone())
  		.css(startStyles)
  		.animate(endStyles, bioEffects)	
  		.animate({height: $bio.height()}, {easing: 'easeOutQuart'});
  }

});
