/**бургер меню */
var sw=true;
    $('.menu-btn, .nav_wrap').on('click', function() {
        $('.menu-btn').toggleClass('menu-btn_active');
        if(sw){
            $('.nav_wrap').show();
        }else{
            $('.nav_wrap').hide();
        }
        sw=!sw;
    });