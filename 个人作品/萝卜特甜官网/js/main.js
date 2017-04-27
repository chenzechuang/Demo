Math.easeout = function (A, B, rate, callback) {
  if (A == B || typeof A != 'number') {
    return;
  }
  B = B || 0;
  rate = rate || 2;
  
  var step = function () {
    A = A + (B - A) / rate;
      
    if (A < 1) {
      callback(B, true);
      return;
    }
    callback(A, false);
    requestAnimationFrame(step);
  };
  step();
};
$(document).ready(function() {
  var timer = null;
  var timer1 = null;
  var $WINDOW = $(window);
  function scroll(fn) {
    var beforeScrollTop = document.documentElement.scrollTop || document.body.scrollTop,
    fn = fn || function() {};
    window.addEventListener("scroll", function() {
      var afterScrollTop = document.documentElement.scrollTop || document.body.scrollTop,
          delta = afterScrollTop - beforeScrollTop;
      if (delta === 0) return false;
      fn(delta > 0 ? "down" : "up");
      beforeScrollTop = afterScrollTop;
    }, false);
  }
  scroll(function(direction) {
    var nav_h = parseInt($('.navbar').css('height'));
    if(direction=="down"){
      if (nav_h > 60) {
        $('.navbar').stop().animate({
          lineHeight: '60px',
          height: '60px',
          backgroundColor: 'rgba(0,0,0,1)'
        }, 500);
        $('.nav').stop().animate({
          marginTop: '5px'
        }, 500);
        $('.navbar-brand').stop().animate({
          lineHeight: '60px'
        }, 500);
      }
    } else {
      if($WINDOW.scrollTop() < 40 && nav_h < 100) {
        $('.navbar').stop().animate({
          lineHeight: '100px',
          height: '100px',
          backgroundColor: 'rgba(0,0,0,0)'
        }, 500);
        $('.nav').stop().animate({
          marginTop: '20px'
        }, 500);
        $('.navbar-brand').stop().animate({
          lineHeight: '100px'
        }, 500);
      }
    }
  });

  $('.scrollup').click(function() {
    var doc = document.body.scrollTop? document.body : document.documentElement;
    Math.easeout(doc.scrollTop, 0, 8, function (value) {
        doc.scrollTop = value;
    });
  });
});