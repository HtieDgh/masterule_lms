<?php
require_once('security.php');
$user=new Security();
$html_txt='';
$c_les='';
$les_mas=array();
error_reporting(E_ERROR);
// Консоль админа
$menu=$user->access>0?'<a href="profile.php"><img src="img/settings.png" width="20px"></a>':'';
//кнопка Войти
$enter_btn=$user->access>0?'<a href="exit.php">Выйти <span>'.$user->user_data['name'].'</span></a>':'<a href="login.php">Войти</a>';
/*Запрос к БД */
queryMysql("SET lc_time_names = 'ru_UA'");
    $q="SELECT DATE_FORMAT(created,'%M') as 'mons', DATE_FORMAT(`created`,'%d.%m') as 'frmtd_created',id,title,article,author_id FROM rasp 
        WHERE created >CURDATE() and created < DATE_ADD(CURDATE(), INTERVAL 3 month) AND `author_id`=2
        order by created, id;";
    $result=queryMysql($q);/**Получаем месяцы */
    if($result->num_rows>0){
       
        for($i=0;$i<$result->num_rows;$i++){
            $rec=$result->fetch_assoc();
            /**Контролы */
            $control=$user->access>1 && $user->user_data['id']==$rec['author_id']?'<hr><div class="rasp_control">Управление: <a class="note_ed note_cntrl_btn" href="note.php?id='.$rec['id'].'&edit_type=3">Изменить урок</a> 
        <a class="note_del note_cntrl_btn" id="notedel_'.$rec['id'].'" href="1">Удалить урок</a></div>':'';
            /**Сам урок */
            $les_mas[$rec['mons']].='<div class="note rasp_les" id="note_'.$rec['id'].'">
                <div class="flex_sb_r_ac">
                    <h2 class="note_title" id="rasptitle_'.$rec['id'].'">'.$rec['title'].'</h2>
                    <p class="note_date italyc page_nums">' . $rec['frmtd_created']. '</p>
                </div>
                
                <p class="rasp_text italyc">'.$rec['article'].'</p><br>
                    '.$control.'    
                </div>';
        }
        foreach($les_mas as $mon=>$les){
            $html_txt.='<div class="month_rasp flex_fs_r_ac"><div class="mon_name">'.$mon.'</div><div style="width:100%;" class="flex_fs_r flex_wr">'.$les.'</div></div><hr>';
        }
          
    }else{
        $html_txt.='<div class="month_rasp flex_c_r"><div>Уроков на ближайщие 3 месяца не запланировано)</div></div>';
    }

/*Вывод html страницы */
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/styles_console.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
    <script src="js/jquery-3.3.1.js"></script><!--Библиотека jquery-->
    <title>Расписание уроков - Мастерюля</title>
</head>
<body>

    <header> 
    <div class="flex_sb_r_ac header_line ">
        <div class="logo">
            <a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля" class=""></a>
        </div>
        <h1 class="head_txt">Расписание уроков</h1>
        <div class="menu-btn-wrap flex_fe_r_ac">
            <div class="menu-btn">
                <span></span>
            </div>
            <div class="console_wrap">
            '.$menu.'
            </div>
        </div>	
    </div>
    </header>
    <div class="nav_wrap">
        <ul class="nav">
            <li><a href="email.php">Отправить письмо</a></li>
            <li><a href="rasp.php">Расписание</a></li>
            <li>'.$enter_btn.'</li>
        </ul>
    </div>
    <section class="content ">
        <div class="ClearFix">
            <div class="note">
                '.$html_txt.'
                <div class="flex_c_r">
                <a class="page_nums" href="blog.php" >Вернуться на главную страницу сайта</a>
                </div>
            </div>
        </div>
    </section>
    <a class="mbtn" href="#chosen">&uArr;</a>
    <footer>
		<p id="chosen"><a target="_blank" href="https://vk.com/id7167157">Тимохина Юлия</a> - мастер, творящий чудеса</p>
	</footer>
<script src="js/main.js"></script>  
<script src="js/burger.js"></script> 
</body>
</html>';
?>