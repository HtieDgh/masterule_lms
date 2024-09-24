$(document).ready(function(){
    /*для павельной работы скрипта указать корнивую папку с файлами */
    var domain="http://188.243.170.117/practicum_trzbd_2020/masterula/";
    function displayComments(data,id_note,cur_btn=false){
        console.log(data);
        let msg=JSON.parse(data);let html_txt='';
        if(msg['err']){
            alert(msg['err_txt']);
        }else{
            $('#cmntblock_'+id_note).html('<div id="comment_'+id_note+'"><br>Всего коментариев:<span class="comment_count">'+msg['comments']['count']+'</span><br>'+msg['comments']['html']+'</div>');
            $('#cmntblock_'+id_note).show();
            if(cur_btn){
                cur_btn.html("Скрыть коментарии");
            }
            if(msg['access']){newEvents();}
        }
    }
    //=======Клик на кнопку Открыть коментарии=========
    $('.note_cmt').click(function(e){
        e.preventDefault();
        
        let id_str=$(this).attr('id');
        let id_note=id_str.substring(id_str.indexOf('_')+1);
        let cur_btn=$('#'+id_str);
 
        if(cur_btn.html()!="Скрыть коментарии"){
            $.post(
                'comments.php',
                {
                    id:id_note,
                    com_option:'show_cur'
                },
                function(data){
                    displayComments(data,id_note,cur_btn);
                }
            );
        }else{
            $('#cmntblock_'+id_note).html('');
            $('#cmntblock_'+id_note).hide();
            cur_btn.html("Открыть комментарии...")
        }
    });
    function delete_ent(id,th){
        let id_str=th.attr('id');
        let id_note=id_str.substring(id_str.indexOf('_')+1);
        let cur_note=$('#'+id+id_note);
        if(confirm("Удалить: '"+$('#'+id+id_note+' .note_title').text()+"'?")){
            
            $.post(
                'deletenote.php',
                {
                    id:id_note,
                    edit_type:th.attr('href')
                },
                function(data){
                    console.log(data);
                    let msg=JSON.parse(data);
                    if(msg['err']){
                        alert(msg['err_txt']);
                    }else{
                        cur_note.detach();
                    }
                }
            );
        }
    }
    /*Удаление заметки*/
    $('.note_del').click(function(e){
        e.preventDefault();
        delete_ent('note_',$(this));
   }); 
   /*Удаление курса */
   $('.crs_del').click(function(e){
        e.preventDefault();
        delete_ent('crsblock_',$(this));
   }); 
/*Модальное окно добавления коментария*/
    var modal = document.getElementById('myModal');

    $(".note_new_comment").click(function(e){
        e.preventDefault();
        let id_str=$(this).attr('id');
        let id_note=id_str.substring(id_str.indexOf('_')+1);
        $('#comment_note_id').attr('value',id_note);
        $('#comment_note_op').attr('value','new_com');
        $('#comment_note_title').html($('#notetitle_'+id_note).html())
        $(".my_window").show();
    });
    $('.close').click(function(e){
        e.preventDefault();
        $(".my_window").hide();
        
    });
    $('.send_cmnt').click(function(e){
        e.preventDefault();
        let id_note=$("#comment_note_id").val()
        $.post(
            'comments.php',
            { 
                id:$("#comment_note_id").val(),
                comment_txt:$("#comment_txt").val(),
                com_option:'new_com'
            },
            function(data){
                displayComments(data,id_note);
            }
        );
    });
    /*Конец Модальное окно*/
/*===========Удаление коментария=================*/
function newEvents(){
    $('.comment_del').click(function(e){
        e.preventDefault();
        let id_str=$(this).attr('id');
        let id_comment=id_str.substring(id_str.indexOf('_')+1);
        let comment_props=id_comment.split('_');
        let cur_comment=$('#comment_'+comment_props[0]+"_"+comment_props[1]);
        let span_count=$('#comment_'+comment_props[0]+' .comment_count');
        $.post(
            'comments.php',
            {
                id:id_comment.substring(id_comment.indexOf('_')+1),
                com_option:'delete_com'
            },
            function(data){
                console.log(data);
                let msg=JSON.parse(data);
                if(msg['err']){
                    alert(msg['err_txt']);
                }else{
                    cur_comment.detach();
                    span_count.html(span_count.html()-1);
                }
            }
        );
    });

}
/*===========Конец Удаление коментария=================*/
/**Теги */
$('.tag_btn').click(function(e){
    e.preventDefault();
    $('#user_search input[name="user_search"]').val($(this).attr('href'));
    $('#user_search').submit();
});
/**Конец Теги */
/*Картинки на весь экран */


});

