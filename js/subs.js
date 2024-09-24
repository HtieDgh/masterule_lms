//Кнопка Подписаться
$('.a_sub_btn:not(span)').click(function(e){
    e.preventDefault();
    let id_str_btn=$(this);
    let id=id_str_btn.attr('id').substring(id_str_btn.attr('id').indexOf('_')+1);
//Подписка
    if(id_str_btn.hasClass("page_nums")){
        $.post(
            'become_sub.php',
            {
                send_id:id,
                op:1
            },
            function(data){
                console.log(data);
                let msg=JSON.parse(data);
                if(!msg['err']){
                    id_str_btn.html("Отписаться");
                    id_str_btn.removeClass("page_nums");
                    id_str_btn.addClass("page_nums_rev");

                }else{
                    alert(msg['err_txt']);
                }
            }
        );
//Отписка
    }else if(id_str_btn.hasClass("page_nums_rev")){
        $.post(
            'become_sub.php',
            {
                send_id:id,
                op:2
            },
            function(data){
                console.log(data);
                let msg=JSON.parse(data);
                if(!msg['err']){
                    id_str_btn.html("Подписаться");
                    id_str_btn.removeClass("page_nums_rev");
                    id_str_btn.addClass("page_nums");
                }else{
                    alert(msg['err_txt']);
                }
            }
        );
    }
});
$('.c_sub_btn:not(span)').click(function(e){
    e.preventDefault();
    let id_str_btn=$(this);
    let id=id_str_btn.attr('id').substring(id_str_btn.attr('id').indexOf('_')+1);
//Подача заявки
    if(id_str_btn.hasClass("new_sub_btn")){
        $.post(
            'become_sub.php',
            {
                send_id:id,
                op:3
            },
            function(data){
                console.log(data);
                let msg=JSON.parse(data);
                if(!msg['err']){
                    id_str_btn.removeClass("new_sub_btn");
                    id_str_btn.removeClass("page_nums");
                    id_str_btn.addClass("page_nums_rev");
                    switch(msg['data']['code']){
                        case 0:
                            id_str_btn.html('Заявка подана');
                            break;
                        case 1:
                            id_str_btn.html('Отписаться');
                            break;
                    }
                }else{
                    alert(msg['err_txt']);
                }
            }
        );
   
    }else{
//Отписка от курса / отмена заявки
        $.post(
            'become_sub.php',
            {
                send_id:id,
                op:4
            },
            function(data){
                console.log(data);
                let msg=JSON.parse(data);
                if(!msg['err']){
                    id_str_btn.html("Подписаться");
                    id_str_btn.addClass("new_sub_btn");
                    id_str_btn.removeClass("page_nums_rev");
                    id_str_btn.addClass("page_nums");
                }else{
                    alert(msg['err_txt']);
                }
            }
        );
    }

});