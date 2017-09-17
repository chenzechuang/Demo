$.fn.ImgZoomIn = function() {
	bgstr = '<div id="ImgZoomInBG" style=" background:#000000;position:fixed; left:0; top:0; z-index:10000; width:100%; height:100%; display:none;"><iframe src="about:blank" scrolling="no" style="width:100%; height:100%;border:0;"><html><body style="margin:0;padding:0;overflow:hidden" scroll="no"></body></html></iframe></div>';
	//alert($(this).attr('src'));
	imgstr = '<img id="ImgZoomInImage" src="' + $(this).attr('xsrc') + '" onclick=$(\'#ImgZoomInImage\').hide();$(\'#ImgZoomInBG\').hide(); style="cursor:pointer; display:none; position:absolute; width:100%; z-index:10001;" />';
	if ($('#ImgZoomInBG').length < 1) {
		$('body').append(bgstr);
	}
	if ($('#ImgZoomInImage').length < 1) {
		$('body').append(imgstr);
	} else {
		$('#ImgZoomInImage').attr('src', $(this).attr('xsrc'));
	}
	//alert($(window).scrollLeft());
	//alert( $(window).scrollTop());
	$('#ImgZoomInImage').css('left', $(window).scrollLeft() + ($(window).width() - $('#ImgZoomInImage').width()) / 2);
	$('#ImgZoomInImage').css('top', $(window).scrollTop() + ($(window).height() - $('#ImgZoomInImage').height()) / 2);
	$('#ImgZoomInBG').show();
	$('#ImgZoomInImage').show();
	$('iframe body').css('margin', '0');
};