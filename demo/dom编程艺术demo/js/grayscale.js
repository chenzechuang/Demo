/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-31 21:31:03
 * @version $Id$
 */
function convertToGS(img) {
//如果浏览器不支持<canva>就返回
	if (!Modernizr.canvas) return;
//存储原始的彩色版
	img.color = img.src;
//创建灰度版
	img.grayscale = createGSCanvas(img);
	img.onmouseover = function() {
		this.src = this.color;
	}	
	img.onmouseout = function() {
		this.src = this.grayscale;
	}
	img.onmouseout();
}

function createGSCanvas(img) {
	var canvas = document.createElement("canvas");
	canvas.width = img.width;
	canvas.height = img.height;
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img,0,0);
	//getImageData只能操作与脚本位于同一域的图片
	var c = ctx.getImageData(0, 0, img.width, img.height);
	for (i=0; i< c.height; i++) {
		for (j=0; j<c.width; j++) {
			var x = (i*4) * c.width + (j*4);
			var r = c.data[x];
			var g = c.data[x+1];
			var b = c.data[x+2];
			c.data[x] = c.data[x+1] = c.data[x+2] = (r+g+b)/3;		
		}
	}

	
	ctx.putImageData(c,0,0,0,0,c.width,c.height);
	return canvas.toDataURL();
}
window.onload = function() {
	convertToGS(document.getElementById("avatar"));
}
