/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-25 21:16:30
 * @version $Id$
 */
function displayAccessKey() {
	var links = document.getElementsByTagName("a");
	var akeys = new Array();
	for (var i = 0; i < links.length; i++) {
		if(!links[i].getAttribute("accesskey")) continue;
		var key = links[i].getAttribute("accesskey");
		var text = links[i].lastChild.nodeValue;
		akeys[key] = text;
	}
	var list = document.createElement("ul");
	for(key in akeys) {
		var text = akeys[key];
		var str =key + ": "+text;
		var item = document.createElement("li");
		var item_text = document.createTextNode(str);
		item.appendChild(item_text);
		list.appendChild(item);
	}
	var header = document.createElement("h3");
	var header_text = document.createTextNode("AccessKeys");
	header.appendChild(header_text);
	document.body.appendChild(header);
	document.body.appendChild(list);
}
addLoadEvent(displayAccessKey);
