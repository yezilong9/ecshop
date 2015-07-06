 
var F7 = new Framework7({ajaxLinks:'.ajax'});
var $$ = Dom7;

if(F7.device.os != 'android'){
  $$('html').removeClass('android');
}
var mySlider = F7.slider('.slider-container', {
  pagination:'.slider-pagination'
});

/*搜索條
$$('.navbar .link-search').on('click',function(){
  $$('form.searchbar');
})*/

/*搜索條*/
$$('.navbar .link-search, .btn-cancel').on('click',function(){
  $$('form.searchbar').toggleClass('none');
})



/*排序*/
productsSortingItem = $$('.products-sorting .item');
productsSortingItem.on('click', function(){
  productsSortingItem.removeClass('on');
  $$(this).addClass('on');
})




