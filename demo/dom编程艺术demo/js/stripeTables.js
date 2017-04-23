/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-28 14:03:51
 * @version $Id$
 */
function stripeTables() {
	if (!document.getElementsByTagName) return false;
	var table = document.getElementsByTagName("table");
	var odd,rows;
	for (var i = 0; i < table.length; i++) {
		odd = false;
		rows = document.getElementsByTagName("tr")
		for (var j = 0; j < rows.length; j++) {
			if(odd == true) {
				addClass(rows[j],"jsodd");
				odd =false
			}else {
				odd = true;
			}
		}
	}
}

addLoadEvent(stripeTables);