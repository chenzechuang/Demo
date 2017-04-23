/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-12 23:34:59
 * @version $Id$
 */
(function($) {
  $.expr.setFilters.group = function(elements, argument) {
    var resultElements = [];
    for (var i = 0; i < elements.length; i++) {
      var test = (i % 3 == argument);
      if (test) {
        resultElements.push(elements[i]);
      }
    }
    return resultElements;
  };
})(jQuery);

(function($) {
	$.fn.column = function() {
		var $cells = $();
		this.each(function() {
			var $td = $(this).closest('td, th');
			if($td.length) {
				var colNum = $td[0].cellIndex + 1;
				var $columnCells = $td
				 	.closest('table')
				 	.find('td, th')
				 	.filter(':nth-child(' + colNum + ')');
				$cells = $cells.add($columnCells);
			}
		});
		return this.pushStack($cells);
	};
})(jQuery);


$(document).ready(function() {	
	function stripe() {
		$('#news')
		.find('tr.alt').removeClass('alt').end()
		.find('tr.alt-2').removeClass('alt-2').end()
		.find('tbody').each(function() {
			$(this).children(':visible').has('td')
			.filter(':group(1)').addClass('alt').end()
			.filter(':group(2)').addClass('alt-2');
		});
	}
	stripe();	

	$('#topics a').click(function(event) {
		event.preventDefault();
		var topic = $(this).text();
		$('#topics a.selected').removeClass('selected');
		$(this).addClass('selected');

		$('#news').find('tr').show();
		if (topic != 'All') {
			$('#news').find('tr:has(td)').not(function() {
				return $(this).children(':nth-child(4)').text() == topic;
			}).hide();
		}
	stripe();
	});

  $('#release').nextAll().addBack().addClass('highlight');

  $('#news td').click(function() {
 		$('#news td.active').removeClass('active');
 		$(this).column().addClass('active');
 	});
});
