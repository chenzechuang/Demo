/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-30 21:58:01
 * @version $Id$
 */function insertAfter(newElement,targetElement) {
 	var parent = targetElement.parentNode;
 	if(parent.lastChild == targetElement) {
 		parent.appendChild(newElement);
 	}else {
 		parent.insertBefore(newElement,targetElement.nextSibling);
 	}
 }
