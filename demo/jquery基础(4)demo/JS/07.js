/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-07 20:53:38
 * @version $Id$
 */
$(document).ready(function() {
	var $books = $('#books');
	var $controls = $('<div id="books-controls"></div>');
	$controls.insertAfter($books);
	$('<button>Pause</button>').click(function(event) {
		event.preventDefault();
		$books.cycle('pause');
		$.cookie('cyclePaused', 'y');
	}).button({
		icons: {primary: 'ui-icon-pause'}
	}).appendTo($controls);
	$('<button>Resume</button>').click(function(event) {
		event.preventDefault();
		var $paused = $('ul:paused');
		if ($paused.length) {
			$paused.cycle('resume');
		} else {
			$(this).effect('shake', {
				distance: 10
			});
		}
		$.cookie('cyclePaused', null);
	}).button({
		icons: {primary: 'ui-icon-play'}
	}).appendTo($controls);
	
	$books.cycle({
		timeout: 1500,
		fx: 'fade',
		speed: 200,
		pause: true,
		before: function() {
			$('#slider')
				.slider('value', $("#books li").index(this));
		}
	});
	if ($.cookie('y')) {
		$books.cycle('pause');
	}

	$books.hover(function() {
		$books.find('.title').animate({
			backgroundColor: '#eee',
			color: '#000'
		}, 1000);
	}, function() {
		$books.find('.title').animate({
			backgroundColor: '#000',
			color: '#fff'
		}, 1000);
	});

	$('h1').click(function() {
		$(this).toggleClass('highlighted', 'slow', 'easeInExpo');
	});

	$books.find('.title').resizable({
		grid: [10, 10],	
		handles: 's'
	});

	$('<div id="slider"></div>').slider({
		min: 0,
		max: $('#books li').length - 1,
		slide: function(event, ui) {
			$books.cycle(ui.value);
		}
	}).appendTo($controls);
});
