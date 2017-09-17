$(function() {

	$('.index-nav li').click(function(event) {
		$(this).addClass('selected')
			.siblings().removeClass('selected');
	});

	var flag = 0;
	$('.data-btn').click(function(event) {
		var $this = $(this);
		if ($this.hasClass('chioce')) {
			$(this).removeClass('chioce');
		} else {
			$(this).addClass('chioce');		
		}
	});
});
