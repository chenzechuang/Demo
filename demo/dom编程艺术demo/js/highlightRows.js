/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-28 16:01:37
 * @version $Id$
 */
function highlightRows() {
	if(!document.getElementsByTagName) return false;
	var rows = document.getElementsByTagName("tr");
	for (var i = 0; i < rows.length; i++) {
		rows[i].onmouseout = function() {
			this.style.fontWeight = "bold";
		}
	}
}
addLoadEvent(highlightRows);
