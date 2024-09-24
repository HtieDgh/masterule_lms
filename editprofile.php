<?php
require_once('security.php');
$user=new Security();

$cur_date=date('Y-m-d');
$html_txt='';
$head_label='Редактирование профиля';
$rec=array("name"=>"","login"=>"","pass"=>"","access"=>"","id"=>"","created"=>"","status"=>"");

    $result=queryMysql("SELECT * FROM `s_a` WHERE `id`=".$user->user_data['id']);
    $rec=$result->fetch_assoc();
//Проверка текущего пароля    
    if( isset($_POST['cur_pass']) &&
        md5($_POST['cur_pass'])===$user->user_data['password'])
    {
        if(isset($_POST['name'])&&
            isset($_POST['status'])&&
            isset($_POST['new_pass']))
        {
            $name=sanitizeString($_POST['name']);
            $status=sanitizeString($_POST['status']);
            
            $new_pass=strlen($_POST['new_pass'])==0 ?  $user->user_data['password']:md5(sanitizeString($_POST['new_pass']));

            $q="UPDATE `s_a` SET `name`=?,`status`=?,`pass` = ?  WHERE `s_a`.`id` =".$user->user_data['id'];
            $params=array("sss",$name,$status,$new_pass);
           
        //Выполнение запроса на изменение профилей
        /*
            Входные параметры: запрос, параметры array("bind_patern",переменые..), строка, которая вернется в случае удачного выполнения
            Пример аргументов
            $q="UPDATE `rasp` SET `created` = ?, `title` = ?, `article` = ? WHERE `rasp`.`id`=?";
            $params=array("sssi",$date,$title,$article,$id);
        */
            $html_txt=preparedQuery($q,$params,'<p class="good_txt"><br>Успешно! <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>');            
        // время жизни COOKIE-данных продлевается на 24 часа
            $cookie_time=time() + 24 * 3600;
            setcookie('security_login', $user->user_data['login'], $cookie_time, "/", $_SERVER['HTTP_HOST']);
            setcookie('security_password', $new_pass, $cookie_time, "/", $_SERVER['HTTP_HOST']);
            setcookie('user_name', $user->user_data['name'], $cookie_time, "/", $_SERVER['HTTP_HOST']);	
        }else{
            $html_txt='<p class="alert_txt"><br>Ошибка передачи данных. Попробуйте снова!</p><p> <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>';
        }
    }else if($_SERVER['REQUEST_METHOD']=='POST'){
        $html_txt='<p class="alert_txt"><br>Проверьте правильность веденого пароля</p><p> <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>';
    }else{
        $html_txt='<p><br><a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>';
    }

$html_txt='
<form class="decor" method="POST" action="editprofile.php">
    <div class="form-inner">
        <p class="italyc"> 
        <h3>'.$head_label.'</h3><br>
        </p>
        <div class="flex_c_r">
            <div class="ava_img flex_c_c">
                <img id="imgprof_'.$user->user_data['id'].'" src="'.$rec['ava'].'">
            </div>
        </div>
        <br>
     
        <div class="flex_c_r_ac"><p class="good_txt">Изменить фото</p></div>
        <div class="flex_c_r_ab">
            <input class="file_in" id="user_ava" accept="image/bmp,image/jpeg,image/png" name="user_ava" class="UserIn" type="file">
            <a href="0_" class="page_nums_rev" id="send_ava_btn">Отправить фото</a>
       </div>

        <p class="good_txt">Имя</p>
        <input type="text" name="name" required placeholder="Имя" value="'.$rec['name'].'">

        <p class="good_txt">Статус</p>
        <input type="text" name="status" required placeholder="Статус" value="'.$rec['status'].'">

        <p class="good_txt">Изменение пароля</p>
        <input type="password" name="new_pass" placeholder="Новый пароль" value="">  
        <p class="good_txt">Подтвердите изменение</p>
        <input type="password" name="cur_pass" required placeholder="Текущий пароль" value="">
        <div class="flex_c_r flex_wr">
            <input type="submit" value="OK">
        </div>
        
        '.$html_txt.'
    </div>
</form>';
/**Ограничение кнопок меню для обычного пользователя */

echo '
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/styles_console.css">
    <link rel="stylesheet" type="text/css" href="css/profile.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
    <script src="js/jquery-3.3.1.js"></script><!--Библиотека jquery-->
	<title>Редактирование профиля - Мастерюля</title>

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
        '.$html_txt.'
        </div>
    </section>

    <img src="" id="open_full_img" alt="Вспомогательное изображение">

    <script src="js/burger.js"></script>
    <script src="js/send_ava.js"></script>
</body>
</html>
';
?>