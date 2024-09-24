<?php
require_once('security.php');
require_once('lib/notes_process.php');
require_once('lib/subscribe.php');

    $user=new Security();
    $notes=new Notes();
    if(strpos($user->cur_url,'op=0')){
        require_once('lib/statistics.php');
        $stat=new Stats($user);
    }
    
    $html_txt='';
    $page_html='';
    $cntrl_panel_html='';
    $cont_page_block='';
    //become author block html содержит подсказку если пользователь еще не автор
    $b_a_html=$user->access<2?'
        <div class="note">
            <h2 class="note_title" id="notetitle_">Станьте автором</h2>
            <hr>          
            <p class="profile_txt">
                Создавайте и делитись своим творчеством!
            </p>
            <div class="flex_c_r"><a href="profile.php?b_a=1" class="page_nums">Начать творить</a></div>
        </div>
    ':'';
    $cr_course_html=$user->access==2?'
        <div class="note">
            <h2 class="note_title" id="notetitle_">Курсы</h2>
            <hr>          
            <p class="profile_txt">
                Вы учитель? Много уроков - это хорошо, но как в них разобраться? Попробуйте создать курс!
            </p>
            <div class="flex_c_r"><a href="note.php?edit_type=4" class="page_nums">Создать курс</a></div>
        </div>
    ':'';
//Нажата ли кнопка Стать автором?
if(isset($_GET['b_a'])){
    if($user->access<2 && $user->becomeAuthor()){
        header("Location: ".SITE_DOMAIN."/profile.php");
    }
}
//дополняем инфу о пользователе
    $user_info=$user->getUserInfo();
    $user_info['status']=$user_info['status']!=''?$user_info['status']:'Расскажите людям о том, что вас интересует...';


/*Навигация по страницам Статистика ваши записи, Ваши подписки, Все авторы, Курсы.*/
    if(isset($_GET['op'])){
        $op= preg_replace_callback("/[^0-4]/",function(){
            echo 'Произошла ошибка 404 : Такой страницы не существует. Попробуйте снова<br><br><a href="blog.php">На главную</a> ';
            exit;
        },$_GET['op']);
    }else{
        $op=1;
    }
/*Навигация по страницам записей*/   
    if(isset($_GET['page'])){
        $page= preg_replace_callback("/[^0-9]/",function(){
            echo 'Произошла ошибка 404 : Такой страницы не существует. Попробуйте снова<br><br><a href="blog.php">На главную</a> ';
            exit;
        },$_GET['page']);
    }else{
        $page=1;
    }
//переключение активной кнопки Статистика ваши записи, Ваши подписки, Все авторы, Курсы.
    $page_style=array('page_nums_rev','page_nums_rev','page_nums_rev','page_nums_rev','page_nums_rev');
    $page_style[$op]='page_nums';
    
    $page_html=$user->access>=2?'<a class="prof_op_btn prof_op_page '.$page_style[0].'" href="profile.php?op=0">Статистика</a><a class="prof_op_btn prof_op_page '.$page_style[4].'" href="profile.php?op=4">Курсы</a>':'';
    $page_html.='<a id="chosen" class="prof_op_btn prof_op_page '.$page_style[1].'" href="profile.php?op=1">Ваши записи</a><a class="prof_op_btn prof_op_page '.$page_style[2].'" href="profile.php?op=2">Подписки</a>';
    
// $stat->getUserList()    
switch($op){
    case 0:
//получение статистики для Автора       
        if($user->access==3){
            $cont_page_block.='<div class="note">'.$stat->getStat([$user->user_data['id']],1).'<hr>
            '.($user->access==3?$stat->getUserList():'').'</div>';
        }else{
            $cont_page_block.='<div class="note">'.$stat->getStat([$user->user_data['id']]).'<hr> </div>';
        }
        
       
     
        break;
//отображение записей сделаных пользователем
    case 1:
        if($user->access>1){
        /*
        $user_search='',$author_id=0,$page=1,$user=0
        */
            $notes->get_notes('profile.php',
                    (isset($_GET['user_search'])?$_GET['user_search']:''),
                    $user->user_data['id'],$page,
                    $user);
            $cont_page_block=$notes->note_count>0?
                    '<div class="exposed page_block_wrap">
                        <div class="flex_c_r">
                            <div class="d_p">
                                <form method="GET" action="profile.php?op=1" class="flex_c_r" id="user_search">
                                <input type="hidden" name="op" value="1">
                                    <input type="text" required name="user_search" placeholder="'.$notes->search.'">
                                    <button type="submit"><img src="img/search.png"></button>
                                </form>
                            </div>
                        </div>
                        <br>
                        <p id="chosen">Всего статей: '.$notes->note_count.'</p>
                        '.$notes->drop_search.'
                        <div class="exposed page_block">
                            <hr>
                            <p>Перейти на страницу:</p>
                                '.$notes->page_html.'
                        </div>
                    </div>
                ':'';
            $cntrl_panel_html.='<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Панель управления</p></div><hr>
                <div class="flex_c_r_ac">
                    <a class="page_nums_rev" href="note.php?edit_type=0">Новая запись</a>
                    <a class="page_nums_rev" href="note.php?edit_type=2">Новый урок</a>
                    <a class="page_nums_rev" href="photos.php">Фото / Файлы</a><br><br>
                </div>
            </div>
            ';
        }
        
        if($notes->note_count>0){
            $cont_page_block.=$notes->notes_html;
        }else{
            $cont_page_block.='<div class="note"><p class="profile_txt">
                Вы еще не создали ни одной записи, вы можете попробовать '.($user->access<2?'<a href="profile.php?b_a=1">стать автором</a>':'<a href="note.php">написать свою первую заметку</a>').'
            </p></div>';
        }
        break;
//Работа с подписками
    case 2:
    case 3:
        $sub=new Subscriber($user->user_data['id']);
        $search=sanitizeString(isset($_GET['user_search'])?$_GET['user_search']:'');
        
        if($op==2){//отобразить только текущие подписки
            $authors=$sub->getAuthorsList($search,$page,1);
        }else{//отобразить всех авторов
            $authors=$sub->getAuthorsList($search,$page,0);
        }
        $cntrl_panel_html.='<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Панель управления</p></div><hr>
            <div class="flex_c_r_ac">
                <a class="'.$page_style[2].'" href="profile.php?op=2">Ваши подписки</a>
                <a class="'.$page_style[3].'" href="profile.php?op=3">Все авторы</a>
                </div>
            </div>
        ';
    //отображение всех найденых авторов
         $cont_page_block.='<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Все авторы</p></div><hr>';
         foreach($authors as $a){
             
             $cont_page_block.='<div class="author_wrap flex_sb_r_ac">
                 <div class="ava_prof_block">
                     <div class="ava_img flex_c_c">
                         <img id="img_'.$a['id'].'" src="'.$a['ava'].'">
                     </div>
                 </div> 
                 <div  class="inner_cont_block">
                 <h2 class="note_title">'.$a['name'].'</h2>
                     <div class="author_stats">'.$a['subs_count'].' подписчиков &#8226; '.$a['notes_count'].' Записей</div>
                     <p>'.$a['status'].'</p>
                 </div>
                 <div>
                     '.($a['is_subbed']==1?'<a id="authorid_'.$a['id'].'" class="w_100 sub_btn page_nums_rev" href="#">Отписаться</a>'
                         :($a['is_subbed']==2?'<span class="w_100 page_nums_rev">Это вы</span>'
                             :'<a id="authorid_'.$a['id'].'" class="w_100 sub_btn new_sub_btn page_nums" href="#">Подписатся</a>')).'
                 </div>   
             </div>
             <hr>';
         }
         $cont_page_block.='</div>';
    //Форма поиска по авторам
        $cont_page_block='<div class="exposed page_block_wrap">
        <div class="flex_c_r">
            <div class="d_p">
                <form method="GET" action="profile.php?op='.$op.'" class="flex_c_r" id="user_search">
                <input type="hidden" name="op" value="'.$op.'">
                    <input type="text" required name="user_search" placeholder="">
                    <button type="submit"><img src="img/search.png"></button>
                </form>
            </div>
        </div>
        <br>
        <p id="chosen">Всего авторов: '.$sub->auth_count.'</p>
        
            <div class="exposed page_block">
                <hr>
                <p>Перейти на страницу:</p>
                    '.$sub->page_html.'
            </div>
        
        </div>'.$cont_page_block;
        break;
//Работа по курсам
    case 4:
        $sub=new Subscriber($user->user_data['id']);
        $search=sanitizeString(isset($_GET['user_search'])?$_GET['user_search']:'');
        $cntrl_panel_html.='<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Панель управления</p></div><hr>
            <div class="flex_c_r_ac">
                <a class="'.$page_style[4].'" href="profile.php?op=4">Ваши Курсы</a>
                <a class="page_nums_rev" href="note.php?edit_type=4">Новый Курс</a>
            </div>
        </div>
        ';
        
    //отображение всех найденых курсов
        $cont_page_block.='<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Ваши Курсы</p></div><hr>';
        foreach($sub->getCourseList($user->user_data['id'],$search,$page,true) as $a){
            $cont_page_block.='
            <div id="crsblock_'.$a['id'].'" class="author_wrap flex_sb_r">
                 <div class="ava_prof_block">
                     <div class="ava_img flex_c_c">
                         <img id="img_'.$a['id'].'" src="'.$a['ava'].'">
                     </div>
                 </div> 
                 <div class="inner_cont_block">
                    <div class="flex_sb_r_ac"><h2 class="note_title">'.$a['title'].'</h2><p>'.$a['created'].'</p></div>
                        <div class="author_stats">'.($a['private']==1?'&#128274; &#8226; <a id="shwrqst_'.$a['id'].'" class="show_rqst" href="#">'.$a['rqst_count'].' Заявок</a> &#8226; ':'').$a['subs_count'].' Учеников &#8226; '.$a['notes_count'].' Записей</div>
                        <p>'.$a['article'].'</p>
                    </div>
                 <div>
                    <a class="w_100 sub_btn page_nums_rev" href="blog.php?c_id='.$a['id'].'">Перейти</a>
                    <a class="w_100 sub_btn page_nums_rev" href="note.php?edit_type=5&id='.$a['id'].'">Изменить</a>
                    <a class="crs_del w_100 sub_btn rounded_red_rev" id="crs_'.$a['id'].'" href="2">Удалить</a>
                 </div>
             </div>
             <hr>';
        }
        $cont_page_block.='</div>';
    //Форма поиска по курсам
        $cont_page_block='<div class="exposed page_block_wrap">
            <div class="flex_c_r">
                <div class="d_p">
                    <form method="GET" action="profile.php?op='.$op.'" class="flex_c_r" id="user_search">
                    <input type="hidden" name="op" value="'.$op.'">
                        <input type="text" required name="user_search" placeholder="">
                        <button type="submit"><img src="img/search.png"></button>
                    </form>
                </div>
            </div>
            <br>
            <p id="chosen">Всего курсов: '.$sub->crs_count.'</p>
        </div>'.$cont_page_block;
        break;
}
/*
========Ввывод html============ 
*/ 
echo '
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
	<script src="js/jquery-3.3.1.js"></script><!--Библиотека jquery-->
	<title>Профиль - Мастерюля</title>

</head>
<body>
	
        <header> 
        <div class="flex_sb_r_ac header_line ">
            <div class="logo">
                <a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля" class=""></a>
            </div>
            <h1 class="head_txt">Профиль</h1>
            
            <div class="menu-btn-wrap flex_fe_r_ac">
                <div class="menu-btn">
                    <span></span>
                </div>
               
            </div>
        </div>
        </header>
<div class="nav_wrap">
    <ul class="nav">
        <li><a href="email.php">Отправить письмо</a></li>
        <li><a href="rasp.php">Расписание</a></li>
        <li><a href="exit.php">Выйти</a></li>
    </ul>	
</div>
        <section class="content ">
        <div class="ClearFix">
            <article class="flex_c_r artcl_block">
                <div class="left_block">
                    <div class="wrap_block prof_u_stat">
                        <div class="flex_c_r">
                            <div class="ava_img flex_c_c">
                                <img id="imgprof_'.$user->user_data['id'].'" src="'.$user->user_data['ava_url'].'">
                            </div>
                            
                        </div>
                        
                        <div class="profile_info">
                            <h2 class="note_title" id="notetitle_">'.$user->user_data['name'].'</h2>
                            <p>
                            Зарегистрирован: '.$user->user_data['created'].'
                            </p>
                        </div>
                        <div class="flex_c_r"><a href="editprofile.php" class="page_nums">Изменить профиль</a></div>
                    </div>
                    <div class="note">
                        <h2 class="note_title" id="notetitle_">Обо мне</h2>
                        <hr>          
                        <p>
                            '.$user_info['status'].'
                        <p>
                    </div>
                </div>
                <div class="center_page">
                    <div class="note page_block flex_c_r_ac flex_wr">
                        '.$page_html.'
                    </div>
            
                    '.$cntrl_panel_html.'
                    '.$cont_page_block.'
                </div>
                <div class="right_block_profile" >
                    '.$b_a_html.'
                    '.$cr_course_html.'
                    <div class="note">
                        <h2 class="note_title" id="notetitle_">Найдите своего автора</h2>
                        <hr>          
                        <p class="profile_txt">
                            Подпишитесь чтобы не пропускать записи вашего любимого автора! 
                            Все ваши подписки отображены в разделе "Подписки".
                        </p>
                        <div class="flex_c_r"><a href="profile.php?op=3" class="page_nums">Найти автора</a></div>
                    </div>
                </div>
            </article>
        </div>
        </section>
        
<div class="my_window">
    <a href="#" class="close">&times;</a>		
    <div class="modal-body">
        <form method="POST" action="comments.php" class="LogIn">
            <input name="id" type="hidden" id="comment_note_id">
            <div class="flex_sb_r comment_txt_form">
                <p style="font-weight:bold;">Вы коментируете:</p><p id="comment_note_title"></p>
            </div>
            <h2 class="LogInTxt" id="serverInfo">'.$user->user_data['name'].', Введите ваш коментариий</h2>

            <div class="group">      
            <textarea id="comment_txt" name="comment_txt" class="UserIn comment_textarea" oninput="auto_grow(this)"></textarea>
                </div>
            <div class="Entering">
                <input class="EnterBtn send_cmnt" type="submit" value="Отправить">
            </div>
        </form>
    </div>
</div>
        <a class="mbtn" href="#chosen">&uArr;</a>
    <footer>
        <p><a target="_blank" href="https://vk.com/id7167157">Тимохина Юлия</a> - мастер, творящий чудеса</p>
    </footer>
    <script src="js/main.js"></script>
    <script src="js/burger.js"></script>
    <script src="js/scroll.js"></script>
    <script src="js/subs.js"></script>
    <script src="js/rqsts.js"></script>
</body>
</html>
';
?>