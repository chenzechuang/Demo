//基于jquery mobile
//先导入<script src="Public/ScrollPage.js"></script>
//然后加载$(document).on("scrollstop", checkScroll);
//在function addMore()中写要加载的内容
var page = 0;
var isEnd = null;
var isSearchEnd = null;
$(document).on("pagebeforecreate", "#base", function(e, ui) {
    addMore();
});
function checkScroll() {
    console.log("here.");
    var activePage = $.mobile.pageContainer.pagecontainer("getActivePage"),
        screenHeight = $.mobile.getScreenHeight(),
        contentHeight = $(".ui-content", activePage).outerHeight(),
        header = $(".ui-header", activePage).outerHeight() - 1,
        scrolled = $(window).scrollTop(),
        footer = $(".ui-footer", activePage).outerHeight() - 1,
        scrollEnd = contentHeight - screenHeight + header + footer;
    if (activePage[0].id == "base" && scrolled >= scrollEnd) {
        console.log("adding...");
        if (isEnd == false) {
            addMore();
        } else if (isSearchEnd == false) {
            search();
        }
    }
}