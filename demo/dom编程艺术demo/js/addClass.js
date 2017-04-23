/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-28 16:30:29
 * @version $Id$
 */
function addClass(element,value) {
	if(!element.className) {
		element.className = value;
	}else {
		newClassName = element.className;
		newClassName += " ";
		newClassName += value;
		element.className = newClassName;
	}
}

