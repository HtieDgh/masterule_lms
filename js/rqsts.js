//Обработка заявок
$('.show_rqst').click(function(e){
    e.preventDefault();
    let id_str_btn=$(this);
    let id=id_str_btn.attr('id').substring(id_str_btn.attr('id').indexOf('_')+1);
    $.post(
        'become_sub.php',
        {
            send_id:id,
            op:5
        },
        function(data){
            console.log(data);
            let msg=JSON.parse(data);
            if(!msg['err']){
                let rqst_html='';
                msg['data']['rqsts'].forEach(function(el){
                    rqst_html+='<div id="rqst_'+el['user_id']+'" class="flex_sb_r_ac"><div><div class="flex_sb_r_ac"><div class="ava_img cmnt_ava_img mr_r_10"><img id="img_'+el['user_id']+'" src="'+el['ava']+'"></div><div><h2 class="comment_title">'+el['name']+'</h2></div></div></div><div class="flex_sb_r"><a class="cnfrm_sub_btn page_nums_rev" href="'+el['user_id']+'" id="cs_'+el['course_id']+'">Приянть</a><a class="cncl_sub_btn rounded_red_rev" href="'+el['user_id']+'" id="cls_'+el['course_id']+'">Отклонить</a></div></div>';
                });
                rqst_html='<div id="rqstlist_'+msg['data']['rqsts'][0]['course_id']+'"><div class="flex_c_r"><p class="good_txt italyc">Заявки</p></div><hr>'+rqst_html+'</div>';
                id_str_btn.removeClass("show_rqst");
                id_str_btn.addClass("hide_rqst");
                
                $('#crsblock_'+id).after(rqst_html);
                newEvents();
            }else{
                alert(msg['err_txt']);
            }
        }
    );
});
function newEvents(){
 $('.cnfrm_sub_btn').click(function(e){
    e.preventDefault();
    let id_str_btn=$(this);
    let c_id=id_str_btn.attr('id').substring(id_str_btn.attr('id').indexOf('_')+1);
    let u_id=id_str_btn.attr('href');
    $.post(
        'become_sub.php',
        {
            send_id:c_id,
            sub_id:u_id,
            op:6
        },
        function(data){
            console.log(data);
            let msg=JSON.parse(data);
            if(!msg['err']){
                $('#rqst_'+u_id).detach();
            }else{
                alert(msg['err_txt']);
            }
        }
    );
 });
 $('.cncl_sub_btn').click(function(e){
     e.preventDefault();
     let id_str_btn=$(this);
     let c_id=id_str_btn.attr('id').substring(id_str_btn.attr('id').indexOf('_')+1);
     let u_id=id_str_btn.attr('href');
     $.post(
         'become_sub.php',
         {
             send_id:c_id,
             sub_id:u_id,
             op:7
         },
         function(data){
             console.log(data);
             let msg=JSON.parse(data);
             if(!msg['err']){
                 $('#rqst_'+u_id).detach();
             }else{
                 alert(msg['err_txt']);
             }
         }
     );
 });
}

    /*
<div id="rqstlist_">
    <div class="flex_sb_r">
        <div>
            <div class="author_wrap flex_fs_r_ac">
                <div class="ava_prof_block">
                    <div class="ava_img">
                        <img id="img_'.el['user_id'].'" src="'.el['ava'].'">
                    </div>
                </div> 
                <div>
                    <h2 class="note_title">'.el['name'].'</h2>
                </div>
            </div> 
        </div>
        <div class="flex_sb_r">
            <a class="confirm" href="el['course_id']">Приянть</a>
            <a class="cancel" href="el['course_id']">Отклонить</a>
        </div>
    </div>
</div>
*/