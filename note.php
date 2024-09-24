<?php
require_once('security.php');
$user=new Security();
require_once('lib/subscribe.php');
require_once('lib/move_uploaded.php');
$upl=new Upload($user);
$sub=new Subscriber($user->user_data['id']);
$notes='';
$html_txt='';
$courses_html='';
/* пути к фото и файлам */
    $img_dir='img/uploaded_photos/';
    $file_dir='files/';
    $video_dir="video/";
$cur_date=date('Y-m-d');
$rec=array("article"=>"","title"=>"","tags"=>"");//для разметки
$params=array();//параметры для bind_param
$file_block_html='';//для html разметки блока с файлами

/**Ограничение кнопок меню в зависимости от доступа */
$menu=$user->access>0?'<a href="profile.php"><img src="img/settings.png" width="20px"></a>':'';
	//кнопка Войти
		$enter_btn=$user->access>0?'<li><a href="exit.php">Выйти <span>'.$user->user_data['name'].'</span></a></li>':'<li><a href="login.php">Войти</a></li>';
	//Кнопка Расписание
		$rasp=$user->access>0?'<li><a href="rasp.php">Расписание</a></li>':'';

/**Принимаем id и переключаем тип редактирования*/
/*Переключение режима 
0 - note insert
1 - note update
2 - rasp insert
3 - rasp update
4 - course insert
5 - course update
*/
    if(isset($_GET['id'])){
    //получение id записи которую необходимо изменить
        $id=$_GET['id'];
        $ed_type=isset($_GET['edit_type'])?$_GET['edit_type']:'err';

        switch($ed_type){
            case 0:
            case 1:
                $q="`notes`";
                break;
            case 2:
            case 3:
                $q="`rasp`";
                break;
            case 4:
            case 5:
                $q="`courses`";
                
                break;
        }

        $rec=queryMysql("SELECT * FROM $q WHERE `id`=$id")->fetch_assoc();
        $rec['article']=preg_replace("/<br>/","\r\n",$rec['article']);
    }else if(isset($_POST['edit_type']) && isset($_POST['id'])){
    //получение id записи которую необходимо изменить, после внесения измениний в форму
        $id=sanitizeString($_POST['id']);
        $ed_type=isset($_POST['edit_type'])?sanitizeString($_POST['edit_type']):'err';
        if($ed_type==5){
            $rec=queryMysql("SELECT * FROM `courses` WHERE `id`=$id")->fetch_assoc();
        }
    }else{
        $id='NULL';
        $ed_type=isset($_GET['edit_type'])?$_GET['edit_type']:'err';
    } 
//Разметка Блок справа при редактировании записи и курсов
if($ed_type=='0' || $ed_type=='1'){
    $file_block_html=$upl->getFilesHtml(1);
    $file_block_html=' <div class="right_block">
        <a href="#" class="close">&times;</a>      
            '.$file_block_html.'
        <div class="flex_fe_r"><a href="#" class="page_nums ok">OK</a></div>
    </div>';

    $corurses=$sub->getCourseList($user->user_data['id']);
    foreach ($corurses as $v) {
        $courses_html.='<option value="'.$v['id'].'">'.$v['title'].'</option>';
    }
    
    $courses_html='<select name="course" id="">
    <option value="0">Выбререте Курс</option selected>
    '.$courses_html.'
    </select>';
}
if(isset($_POST['note_title']) && isset($_POST['note_txt'])){
    $title=sanitizeString($_POST['note_title']);
    $tags=isset($_POST['note_tags'])?sanitizeString($_POST['note_tags']):'';
    $date=isset($_POST['note_date'])?sanitizeString($_POST['note_date']):$cur_date;
    $private=isset($_POST['private']) && $_POST['private']=='on'?'1':'0';
    $course_id=isset($_POST['course']) && $_POST['course']!='0'?sanitizeString($_POST['course']):NULL;
    $article=$_POST['note_txt'];
    $article=preg_replace("/\r\n/","<br>",$article);
    
/*Переключаем тип редактирования (notes|rasp|course)-<type> : 1,3,5 - update 0,2,4 - insert  default - ошибка*/
    switch($ed_type){
        case '0':
            $q="INSERT INTO `notes` (`course_id`,`created`, `title`, `article`,`tags`,`id`,`author_id`) VALUES (?,?, ?, ?,?,?,?)";
            $params=array("issssii", $course_id, $cur_date, $title, $article, $tags, $id, $user->user_data['id']);
            $output_str='<p class="good_txt">Запись опубликована! <a href="blog.php">Вернуться на главную</a></p>';
            //1 - режим "Редактора", скрывает некоторые управляющие элементы
            break;
        case '1':
            $q="UPDATE `notes` SET `course_id`=?,`author_id`=?, `created` = ?, `title` = ?, `article` = ?,`tags`=? WHERE `notes`.`id` = ?";
            $params=array("iissssi",$course_id,$user->user_data['id'],$cur_date,$title,$article,$tags,$id);
            $output_str='<p class="good_txt">Запись изменена! <a href="blog.php">Вернуться на главную</a></p>';
            break;
        case '2':
            $q="INSERT INTO `rasp` (`created`, `title`, `article`,`id`,`author_id`) VALUES (?, ?, ?,?,?)";
            $params=array("sssii",$date,$title,$article,$id,$user->user_data['id']);
            $output_str='<p class="good_txt">Расписание добавлено! <a href="rasp.php">Вернуться к расписанию</a></p>';
            break;
        case '3':
            $q="UPDATE `rasp` SET `author_id`=?, `created` = ?, `title` = ?, `article` = ? WHERE `rasp`.`id`=?";
            $params=array("isssi",$user->user_data['id'],$date,$title,$article,$id);
            $output_str='<p class="good_txt">Расписание изменено! <a href="rasp.php">Вернуться к расписанию</a></p>';
            break;
        case '4':
            $q="INSERT INTO `courses` (`title`, `article`,`private`, `author_id`) VALUES (?, ?, ?,?)";
            $params=array("ssii",$title,$article,$private,$user->user_data['id']);
            $output_str='<p class="good_txt">Курс создан! <a href="profile.php?op=4">Вернуться к списку Курсов</a></p>';
            break;
        case '5':
            $q="UPDATE `courses` SET `title`=?, `article`=?,`private`=?, `author_id`=? WHERE `id`=?";
            $params=array("ssiii",$title,$article,$private,$user->user_data['id'],$id);
            $output_str='<p class="good_txt">Курс изменен! <a href="profile.php?op=4">Вернуться к списку Курсов</a></p>';
            break;
        default:
            echo 'Oшибка: неверно передан тип редактирования записи edit_type! Свяжитесь с администратором<br><a href="email.php">Отправить письмо</a><br><a href="blog.php">На главную</a>';
            exit;
            break;
    }
    
    
 /*
  Входные параметры: запрос, параметры array("bind_patern",переменые..), строка, которая вернется в случае удачного выполнения
  Пример аргументов
  $q="UPDATE `rasp` SET `created` = ?, `title` = ?, `article` = ? WHERE `rasp`.`id`=?";
  $params=array("sssi",$date,$title,$article,$id);
 */
    $html_txt=preparedQuery($q,$params,$output_str); 
}else if($_SERVER['REQUEST_METHOD']=='POST'){
    $html_txt='<p class="alert_txt">Параметры не переданы, попробуйте снова!</p>';
}


/*
Переключение разметки для разных режимов редактора
    <2 - note 
    2-3 - rasp
    4-5 - course
 */
switch($ed_type){
    case 0:
    case 1:
        $html_txt='
        <form class="decor" method="post" action="note.php">
            <div class="form-inner">
                <p class="italyc"> 
                <h3>Новая запись</h3><br>
                <h3>Текущая дата: '.$cur_date.'</h3><br>
                </p>
                <input type="hidden" name="id" value="'.$id.'">
        
                <input type="hidden" name="edit_type" value="'.$ed_type.'">
        
                <input type="text" name="note_title" placeholder="Заголовок" value="'.$rec['title'].'" required>
                <textarea name="note_txt" id="note_txt"  class="edit_txt" placeholder="Текст записи" rows="15" required>'.$rec['article'].'</textarea>
                <input type="text" name="note_tags" placeholder="Теги" value="'.$rec['tags'].'">
                '.$courses_html.'
                <div class="flex_sb_r_ac flex_wr">
                    
                    <div class="flex_c_r" style="width:100%;"><a href="#" class="insert_fl">Вставить фото/файл</a><input type="submit" value="Подтвердить"></div>
                </div>
                
                '.$html_txt.'
            </div>
        </form>
        ';
        break;
    case 2:
    case 3:
        $html_txt='
        <form class="decor" method="post" action="note.php">
            <div class="form-inner">
                <p class="italyc"> 
                <h3>Редактирование урока</h3><br>
                <h3>Текущая дата: '.$cur_date.'</h3><br>
                </p>
                <input type="hidden" name="id" value="'.$id.'">
                <input type="hidden" name="edit_type" value="'.$ed_type.'">
        
                <input type="text" name="note_date"  placeholder="Дата урока" value="'.(isset($rec['created'])?$rec['created']:'').'" required>
                <input type="text" name="note_title" placeholder="Заголовок" value="'.$rec['title'].'" required>
                <textarea name="note_txt" id="note_txt"  class="edit_txt" placeholder="Описание" rows="20" required>'.$rec['article'].'</textarea>
                <div class="flex_sb_r_ac flex_wr">
                    
                    <div class="flex_c_r" style="width:100%;"><input type="submit" value="Подтвердить"></div>
                </div>
                
                '.$html_txt.'
            </div>
        </form>
        ';
        break;
    case 4:
    case 5:
        $html_txt='
        <form class="decor" method="post" action="note.php">
            <div class="form-inner">
                <p class="italyc"> 
                <h3>Редактирование Курса</h3><br>
                <h3>Текущая дата: '.$cur_date.'</h3><br>
                </p>
                <input type="hidden" name="id" value="'.$id.'">
                <input type="hidden" name="edit_type" value="'.$ed_type.'">
                '.($ed_type==5?'<div class="flex_c_r">
                    <div class="ava_img">
                            <img id="imgprof_'.$user->user_data['id'].'" src="'.$rec['ava'].'">
                        </div>
                    </div>
                    <br>
                
                    <div class="flex_c_r_ac"><p class="good_txt">Изменить фото</p></div>
                    <div class="flex_c_r_ab">
                        <input class="file_in" id="user_ava" accept="image/bmp,image/jpeg,image/png" name="user_ava" class="UserIn" type="file">
                        <a href="1_'.$id.'" class="page_nums_rev" id="send_ava_btn">Отправить фото</a>
                    </div>':'').'
                
                
                <input type="text" name="note_title" placeholder="Заголовок" value="'.$rec['title'].'" required>
                <textarea name="note_txt" id="note_txt"  class="edit_txt" placeholder="Описание" rows="20" required>'.$rec['article'].'</textarea>
                <input class="" type="checkbox" name="private" id="private"><label for="private">Закрытый курс?</label>
                
                <div class="flex_sb_r_ac flex_wr">
                    
                    <div class="flex_c_r_ac" style="width:100%;"><input type="submit" value="Подтвердить"></div>
                </div>
                '.$html_txt.'
                
            </div>
        </form>
        ';
        break;
}

/*========Ввывод html============ */ 
echo '
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/styles_console.css">
    
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
	<script src="js/jquery-3.3.1.js"></script><!--Библиотека jquery-->
		<script src="js/insert_files.js"></script>  
	<title>Редактор - Мастерюля</title>

</head>
<body>
	
        <header> 
        <div class="flex_sb_r_ac header_line ">
        <div class="logo">
			<a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля" class=""></a>
		</div>
            <h1 class="head_txt">Редактор</h1>
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
        '.$rasp.'
        '.$enter_btn.'
    </ul>	
</div>
        <section class="content ">

        <article class="flex_c_r artcl_block flex_wr">
            <div class="left_block">
                '.$html_txt.'
            </div>
            '.$file_block_html.'
        </article>        

        </section>
        <img src="" id="open_full_img" alt="Вспомогательное изображение">
        <script src="js/burger.js"></script>
        '.($ed_type==5?'<script src="js/send_ava.js"></script>':'').'
</body>
</html>
';
?>