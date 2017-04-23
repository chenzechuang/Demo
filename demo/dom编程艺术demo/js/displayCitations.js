/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-25 19:53:19
 * @version $Id$
 */
 function displayCitations() {
 	var quotes = document.getElementsByTagName("blockquote");
 	for (var i = 0; i < quotes.length; i++) {
 		if(!quotes[i].getAttribute("cite")) continue;
 		var url = quotes[i].getAttribute("cite");
 		var qouteChildren = quotes[i].getElementsByTagName("*");
 		if(qouteChildren.length < 1) continue;
 		var elem = qouteChildren[qouteChildren.length - 1];
	 	var link = document.createElement("a");
	 	var link_text = document.createTextNode("source");
	 	link.appendChild(link_text);
	 	link.setAttribute("href",url);
	 	var superscript = document.createElement("sup");
	 	superscript.appendChild(link);
	 	elem.appendChild(superscript);
	 }
 }
 addLoadEvent(displayCitations);

