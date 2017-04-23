/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-08-19 19:52:14
 * @version $Id$
 */
$(document).ready(function() {
	$('#selected-plays > li').addClass('horizontal');
	$('#selected-plays li:not(.horizontal)').addClass('sub-level');

	$('a[href^="mailto:"]').addClass('mailto');
	$('a[href$="pdf"]').addClass('pdflink');
	$('a[href^="http"][href*="henry"]').addClass('henrylink');

	$('a').filter(function() {
		return this.hostname && this.hostname != location.hostname;
	}).addClass('external');

	$('#selected-plays > li > ul > li').addClass('special');

	$('#selected-plays > li >ul >li:has(a)').nextAll().not('li:has(a)').addClass('afterlink');

	$('a[href$="pdf"]').parent().parent().addClass('tragedy');

	$('tr:nth-child(odd)').addClass('alt');
	
	$('td:contains(Henry)').nextAll().addBack().addClass('highlight');

	$('td:nth-child(3)').addClass('year');

	$('tr:contains(Tragedy)').filter('tr:eq(0)').addClass('special');
});
