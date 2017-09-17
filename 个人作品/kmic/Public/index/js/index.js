$(function() {
    $('.signup').click(function() {
        $('#window').show();
        return false;
    })
    $('#window').click(function() {
        $(this).hide();
    })

    $('#nav li').click(function() {
        $(this).addClass('selected')
            .siblings().removeClass('selected');     
    });
    var scrollTop = 0;
    var page_height = new Array();
    page_height[0] = $('#page2').offset().top;
    page_height[1] = $('#page3').offset().top;
    page_height[2] = $('#page4').offset().top - 200;
    $(window).scroll(function() {
        scrollTop = $(window).scrollTop();
        for (var i = 0; i < page_height.length; i++) {
            if (scrollTop > page_height[i]) {
                btnSelect(i);
            } else if (scrollTop < 200) {
                 btnSelect(-1);
            }
        }
        
    });

    function btnSelect(i) {
        $('#nav li').eq((i+1)).addClass('selected')
            .siblings().removeClass('selected');
    }
});