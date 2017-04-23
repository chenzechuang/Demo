/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-20 00:01:04
 * @version $Id$
 */
$(document).ready(function() {
	var pageNum = 1;
	$('#more-photos').click(function(event) {
		event.preventDefault();
		var $link = $(this);
		var url = $link.attr('href');
		if (url) {
		    $.get(url, function(data) {
		    	$('#gallery').append(data);
		    });
		    pageNum++;
		    if (pageNum < 20) {
		        $link.attr('href', 'pages/' + pageNum + '.html');
		    } else {
		        $link.remove();
		    }
		}
	});
});			
