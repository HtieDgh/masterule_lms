 /* скрол на топ    */
 var chosen_off=$('#chosen').offset().top;
 window.onscroll=function(){
     if(window.pageYOffset>chosen_off){
         $('.mbtn').show();
     }else{
         $('.mbtn').hide();
     }
    
 };
 $(".mbtn").click( function (event) {
    //отменяем стандартную обработку нажатия по ссылке
    event.preventDefault();
    //забираем идентификатор бока с атрибута href
    let id  = $(this).attr('href'),
    //узнаем высоту от начала страницы до блока на который ссылается якорь
    top = $(id).offset().top-150;
    //анимируем переход на расстояние - top за 1000 мс

    $('body,html').animate({scrollTop: top}, 1000);

});