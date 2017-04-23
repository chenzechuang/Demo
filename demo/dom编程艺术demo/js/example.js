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

function prepareGallery() {
	if (!document.getElementsByTagName || !document.getElementById) return false;
	if (!document.getElementById("imageGallery")) return false;
	var iamgeGallery = document.getElementById("iamgeGallery");
	var links = imageGallery.getElementsByTagName("a");
	for (var i = 0; i < links.length; i++) {
		links[i].onclick = function() {
			return !showPic(this);
		}
	}
}

function insertAfter(newElement,targetElement) {
	var parent = targetElement.parentNode;
	if (parent.lastChild == targetElement) {
		parent.appendChild(newElement);
	}else {
		parent.insertBefore(newElement,targetElement.nextSibling);
	}
}

function preparePlaceholder() {
	if (!document.createElement || !document.createTextNode || !document.getElementById || !document.getElementsByTagName) return false;
	var placeholder =  document.createElement("img");
	placeholder.setAttribute("id","placeholder");
	placeholder.setAttribute("src","images/5.jpg");
	placeholder.setAttribute("alt","my image gellery");
	var description = document.createElement("p");
	description.setAttribute("id","description");
	var desctext = document.createTextNode("choose an image.")
	description.appendChild(desctext);
	var gallery = document.getElementById("imageGallery")
	insertAfter(placeholder,gallery);
	insertAfter(description,placeholder);
}

function showPic(whichpic) {
	if (!document.getElementById("placeholder")) return false;
	var source = whichpic.getAttribute("href");
	var placeholder = document.getElementById("placeholder");
	placeholder.setAttribute("src",source);
	if (document.getElementById("description")) {
		var text = whichpic.getAttribute("title") ? whichpic.getAttribute("title") : "";
		var description = document.getElementById("description");
		description.firstChild.nodeValue = text;
	}
	return true;
}
addLoadEvent(preparePlaceholder);
addLoadEvent(prepareGallery);
