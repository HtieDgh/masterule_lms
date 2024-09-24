$(document).ready(function(){
    var domain="http://188.243.170.117/practicum_trzbd_2020/masterula/";
    var timerId=[];
    /*Вставка обьектов в текстовый редактор*/
   
    /*=======ФОТО====== */
        $('.note img').click(function(){
            timerId.push(setTimeout(function(img){
                        let chosen='';
                        chosen=$(img).attr('src');
                        $('#note_txt').val($('#note_txt').val()+'<img src="'+chosen+'" alt="">');
                        chosen=chosen.substring(chosen.lastIndexOf('/')+1);
                        $("#chosen_ph").html(chosen);
                    },300,this)
            )
        });
    /*=======ФАЙЛЫ====== */
        $('.file').click(function(e){
            e.preventDefault();
            let chosen='';
            chosen=$(this).attr('href');
            $('#note_txt').val($('#note_txt').val()+'<a href="'+chosen+'" download>'+chosen+'</a>');
            chosen=chosen.substring(chosen.lastIndexOf('/')+1);
            $("#chosen_fl").html(chosen);
            
        });
    /*=======ВИДЕО====== */
        $('.video').click(function(e){
            e.preventDefault();
            let chosen='';
            chosen=$(this).attr('href');
            $('#note_txt').val($('#note_txt').val()+'<video src="'+chosen+'" controls></video>');
            chosen=chosen.substring(chosen.lastIndexOf('/')+1);
            $("#chosen_vd").html(chosen);
            
        });
/*Показ изображения на весь экран */
        $('.note img').dblclick(function(){
            for (let i=0; i<timerId.length; i++) {
                clearTimeout(timerId[i]);
            }
            $('#open_full_img').attr('src',$(this).attr('src'));
            $('#open_full_img').show();
         });
         $('#open_full_img').click(function(){
            $(this).hide();
        });
/*Функционал для маленьких экранов*/
    $('.insert_fl').click(function(e){
        e.preventDefault();
        $('.right_block').show();
    });

    $('.close,.ok').click(function(e){
        e.preventDefault();
        $(".right_block").hide();
    });
});