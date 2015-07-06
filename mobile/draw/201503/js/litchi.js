var fixTt = $(".fixed-title");
var deItem = $('.details-item');
var tTop = fixTt.position().top;
//fixed-title切换
$('.fixed-title li').on('click',function (){
  var idx = $(this).index();
  $(this).addClass('curr').siblings().removeClass('curr');
  deItem.hide().eq(idx).show(); 
	$(document).scrollTop(tTop-47);
});

//fixed-title钉住
$(window).scroll(function() { 
    var scrolls = $(this).scrollTop();
    if (scrolls > (tTop-50)) { 
      fixTt.addClass('pos');
      deItem.addClass('pad');
    }else {
      fixTt.removeClass('pos');
      deItem.removeClass('pad');
    }
});