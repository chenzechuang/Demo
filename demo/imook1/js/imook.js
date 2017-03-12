/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-26 22:40:24
 * @version $Id$
 */

$(document).ready(function() {
	$('div.news_type_content a').click(function(event) {
		event.preventDefault();
		var $news = $(this);
		var $news_type = $news.attr('href');
		$('div.list_box').load($news_type + '  div.list_box');
		$('div.news_type_content a').removeClass('seleted');
		$(this).addClass('seleted');
	});

	$('div.news_type h2').click(function(event) {
		event.preventDefault();
		$('div.list_box').load('list.html div.list_box');
	});
});