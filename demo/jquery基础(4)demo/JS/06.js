/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-09-01 18:53:51
 * @version $Id$
 */

$(document).ready(function() {
	$('#letter-a a').click(function(event) {
		event.preventDefault();
		$('#dictionary').load('06a.html');
		alert('Loaded!');
	});

	$('#letter-b a').click(function(event) {
		event.preventDefault();
		$.getJSON('06b.json', function(data) {
			var html = '';
			$.each(data, function(entryIndex, entry) {
				html += '<div class="entry">';
				html += '<h3 class="term">' + entry.term + '</h3>';
				html += '<div class="part">' + entry.part + '</div>';
				html += '<div class="definition">';
				html += entry.definition;
				if (entry.quote) {
					html += '<div class="quote">';
					$.each(entry.quote, function(lineIndex, line) {
						html += '<div class="quote-line">' + line + '</div>';
					});
					if (entry.author) {
						html += '<div class="quote-author">' + entry.author + '</div>';
					}
					html += '</div>';
				}
				html += '</div>';
				html += '</div>';
			});
			$('#dictionary').html(html);
		});
	});

	$('#letter-c a').click(function(event) {
		event.preventDefault();
		$.getScript('06c.js');
	});

	$('#letter-d a').click(function(event) {
		event.preventDefault();
		$.get('06d.xml', function(data) {
			$('#dictionary').empty();
			$(data).find('entry:has(quote[author])').each(function() {
				var $entry = $(this);
				var html = '<div class="entry">';
				html += '<h3 class="term">' + $entry.attr('term') + '</h3>';
				html += '<div class"part">' + $entry.attr('part') + '</div>';
				html += '<div class="definition">';
				html += $entry.find('definition').text();
				var $quote = $entry.find('quote');
				if ($quote.length) {
					html += '<div class="quote">';
					$quote.find('line').each(function() {
						html += '<div class="quote-line">' + $(this).text() + '</div>';
					});
					if ($quote.attr('author')) {
						html += '<div class="quote-author">' + $quote.attr('author') + '</div>';
					}
					html += '</div>';
				}
				html += '</div>';
				html += '</div>';
				$('#dictionary').append($(html));
			});
		});
	});

	$('#letter-e a').click(function(event) {
		event.preventDefault();
		var requestData = {term: $(this).text()};
		$.get('06e.php', requestData, function(data) {
			$('#dictionary').html(data);
		});
	});
});