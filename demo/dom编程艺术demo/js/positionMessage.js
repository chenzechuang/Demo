/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-29 23:54:52
 * @version $Id$
 */
function positionMessage() {
	if (!document.getElementsByTagName) return false;
	if (!document.getElementById("message")) return false;
	var elem = document.getElementById("message");
	elem.style.position = "absolute";
	elem.style.left = "50px";
	elem.style.top = "100px";
	moveElement("message",200,100,10);
}

addLoadEvent(positionMessage);
