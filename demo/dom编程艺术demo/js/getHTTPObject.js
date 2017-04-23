/**
 *
 * @authors Your Name (you@example.org)
 * @date    2016-05-24 01:15:33
 * @version $Id$
 */
function getHTTPObject() {
	if(typeof XMLHttpRequest == "undefined")
		XMLHttpRequest = function() {
			try {return new ActiveXObject("Msxml2.XMLHTTP.6.0");}
				catch(e) {}
			try {return new ActiveXObject("Msxml2.XMLHTTP.3.0");}
				catch(e) {}
			try {return new ActiveXObject("Msxml2.XMLHTTP");}	
				catch(e) {}
			return false;
		}
		return new XMLHttpRequest();
}