/*====Загрузка файла=======*/
$( '.upl_fl').click(function(e){
    e.preventDefault();
    let upld_type='';
    let accept='';
    $('#upld_type').val($(this).attr('href'));
    switch($('#upld_type').val()){
        case '0':
            upld_type="Загрузка фото";
            accept="image/jpeg,image/png,image/gif";
            break;
        case '1':
            upld_type="Загрузка файла";
            accept="image/bmp,image/jpeg',image/png',image/gif,video/mpeg,video/mp4,video/webm,video/x-flv,video/3gpp,video/3gpp2,text/plain,text/html,text/xml,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',application/msword',application/vnd.openxmlformats-officedocument.wordprocessingml.document',application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint,application/vnd.oasis.opendocument.text,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.graphics,application/x-shockwave-flash,application/x-rar-compressed";
            break; 
        case '2':
            upld_type="Загрузка видео";
            accept="video/mpeg,video/mp4,video/webm,video/x-flv,video/3gpp,video/3gpp2";
            break;
    }
    $('#upl_type_txt').html(upld_type);
    $(".my_window").show();
    $("#upload_files").attr('accept',accept);
});
$('.close').click(function(e){
   e.preventDefault();
   $(".my_window").hide();
});