$(document).ready(function(){
 /* скрол на топ    */
 var domain="http://188.243.170.117/practicum_trzbd_2020/masterula/";
 var chosen='';
function delete_file(del_t){
    if(confirm("Удалить выбраные файлы: "+chosen+"?")){
       $.post('delfile.php',
        {
            chosen:chosen,
            del_type:del_t
        },
        function(data){
            console.log(data);
            let msg=JSON.parse(data);
            if(!msg['err']){
                document.location.href=domain+'photos.php';
            }else{
                alert(msg['err_txt'])
            }
            
        });
    }
}

/**Конец добавление фото */
/*Удаление фото */

    $('.note img').click(function(){
        $(this).toggleClass('exposed');
        chosen='';
        $('#upld_type').val("")
        /*Заполнение поля span выбраними изображениями для отправки на удаление */
        let chosen_n='';
        let src='';
        $('.exposed').each(function(){
            src=$(this).attr('src');
            chosen+=', '+src;
            chosen_n+=', '+src.substring(src.lastIndexOf('/')+1);
        })
        if(chosen!=''){
            $('.photo_del').css('visibility','visible');
            $('.photo_del').attr('href','0');
        }else{
            $('.photo_del').css('visibility','hidden');
        }
        chosen=chosen.substring(1);
        $("#chosen_ph").html(chosen_n);

    }); 
    $('.photo_del,.file_del,.video_del').click(function(e){
        e.preventDefault();
        let del_t=$(this).attr("href");
        delete_file(del_t);
    });
/*Конец Удаление фото */
/*Удаление файла */
    $('.file_chk').change(function () {
        chosen='';
        /*Заполнение поля span выбраними изображениями для отправки на удаление */
        let chosen_n='';
        let src='';
        $('.file_chk:checked').each(function(){
            src=$(this).val();
            chosen+=', '+src;
            chosen_n+=', '+src.substring(src.lastIndexOf('/')+1);
        })
        if(chosen!=''){
            $('.file_del').css('visibility','visible');
            $('.file_del').attr('href','1');
        }else{
            $('.file_del').css('visibility','hidden');
        }
        chosen=chosen.substring(1);
        $("#chosen_fl").html(chosen_n);
    });
    

    
/*Конец Удаление файла */
/*Удаление видео */
    $('.video_chk').change(function () {
        chosen='';
        let chosen_n='';
        let src='';
        $('.video_chk:checked').each(function(){
            src=$(this).val();
            chosen+=', '+src;
            chosen_n+=', '+src.substring(src.lastIndexOf('/')+1);
        })
        if(chosen!=''){
            $('.video_del').css('visibility','visible');
            $('.file_del').attr('href','2');
        }else{
            $('.video_del').css('visibility','hidden');
        }
        chosen=chosen.substring(1);
        $("#chosen_vd").html(chosen_n);
    });
/*Конец Удаление видео */


 /**картинка на весь экран */
 $('.note img').dblclick(function(){
    $('#open_full_img').attr('src',$(this).attr('src'));
    $('#open_full_img').show();
 });
 $('#open_full_img').click(function(){
    $(this).hide();
    });
});