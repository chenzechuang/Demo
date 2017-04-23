/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-20 15:46:43
 * @version $Id$
 */

window.onload = prepareLinks;
function prepareLinks() {
	if (!document.getElementsByTagName) return false;
	var links = document.getElementsByTagName("a");
	for (var i = 0; i < links.length; i++) {
		if (links[i].getAttribute("class") == "popUp"){
			links[i].onclick = function() {
				popUp(this.getAttribute("href"));
				return false;
			}	
		}
	}
}
function popUp(winURL) {
	window.open(winURL,"popUp","width=320,height=480")
}