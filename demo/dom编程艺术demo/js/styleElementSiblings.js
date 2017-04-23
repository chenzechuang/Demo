/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-26 13:50:21
 * @version $Id$
 */
function styleElementSiblings(tag,theclass) {
	if(!document.getElementsByTagName) return false;
	var elems = document.getElementsByTagName(tag)
	var elem;
	for (var i = 0; i < elems.length; i++) {
		elem = getNextElement(elems[i].nextSibling);
		addClass(elem,theclass);
	}
}
function getNextElement(node) {
	if(node.nodeType == 1) {
		return node;
	}
	if(node.nextSibling) {
		return getNextElement(node.nextSibling);
	}
	return null;
}
addLoadEvent(function(){
 styleElementSiblings("h1","intro");
});