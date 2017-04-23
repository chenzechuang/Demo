/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-24 01:15:34
 * @version $Id$
 */
 function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != "function") {
		window.onload = func;
	}else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}


