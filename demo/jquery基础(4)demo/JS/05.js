/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-08-30 22:16:27
 * @version $Id$
 */
$(document).ready(function() {
	$('div.chapter a[href*="wikipedia"]').attr({
		rel: 'external',
		title: function() {
			return 'Learn more about ' + $(this).text() + ' at Wikipedia.';
		},
		id: function(index, oldValue) {
			return 'wikilink-' + index;
		}
	});
	

	var $p = $('div.chapter p').eq(2).nextAll();
	$('<a href="#top">back to top</a>').insertAfter($p);
	$('a[href$="#top"]').click(function() {
		$('<p>You were here</p>').insertAfter(this);
	});
	$('<a id="top"></a>').prependTo('body');


	var $notes = $('<ol id="notes"></ol>').insertBefore('#footer');
	$('span.footnote').each(function(index) {
		$(this)
		.before([
			'<a href="#footnote-',
			index + 1,
			'" id="context-',
			index + 1,
			'" class="context">',
			'<sup>',
			index + 1, 
			'</sup></a>'
		].join(''))
		.appendTo($notes)
		.append([
			'&nbsp;(<a href="#context-',
			index + 1,
			'">context</a>)'
		].join(''))
		.wrap('<li id="footnote-' + (index + 1) + '"></li>');
	});


	$('span.pull-quote').each(function(index) {
		var $parentParagraph = $(this).parent('p');
		$parentParagraph.css('position', 'relative');
		var $cloneCopy = $(this).clone();
		$cloneCopy
			.addClass('pulled')
			.find('span.drop')
				.html('&hellip;')
			.end()
			.text($cloneCopy.text())
			.prependTo($parentParagraph);
	});


	var flag = 0;
	$('#f-author').click(function() {
		if (flag == '0') {
			$(this).wrap('<b></b>');
			flag = 1;
		} else {
			$(this).unwrap();
			flag = 0;
		};
	});


	$paragraph = $('div.chapter p');
	$paragraph.each(function() {
		var a = $paragraph.attr('class');
		if (a = null) {
			$paragraph.attr('class', ' inhabitants');
		} else {
			$paragraph.attr('class', a + '  inhabitants');
		};
	});
});
