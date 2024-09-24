<?php
require_once('security.php');
$user=new Security();

$html_txt='';
$head_label='Новый пользователь';
$pass_notiece='';
$rec=array("name"=>"","login"=>"","pass"=>"","access"=>"","id"=>"","status"=>"","created"=>"");

function getUserDB($user_id){//для возврата пользователя
    $result=queryMysql("SELECT * FROM `s_a` WHERE `id`=$user_id");
    return $result->fetch_assoc();
}

/**Принимаем user id */
    if(isset($_GET['user_id'])&&isset($_GET['edit_type'])){
        /**(И) если: Первый вход на страницу */
        $user_id=$_GET['user_id'];
        $ed_type=$_GET['edit_type'];

        $pass_notiece='<p class="alert_txt">Внимание пароль зашфрован</p><p class="alert_txt">Введите свое значение если хотите изменить пароль</p>';
        $head_label='Редактирование пользователя';
        $rec=getUserDB($user_id);
    }else if(isset($_POST['user_id'])&&isset($_POST['edit_type'])){
        /*(И) если: отправлены данные из формы */
        $user_id=sanitizeString($_POST['user_id']);
        $ed_type=sanitizeString($_POST['edit_type']);
    }else{
        $user_id='NULL';
        $ed_type='0';
    }
    //var_dump($ed_type);
if(isset($_POST['login']) && 
    isset($_POST['pass'])&&
    isset($_POST['access'])&&
    isset($_POST['name'])&&
    isset($_POST['status'])){
        $login=sanitizeString($_POST['login']);
        $pass=strlen($_POST['pass'])==32 ?  sanitizeString($_POST['pass']):md5(sanitizeString($_POST['pass']));
        $acs=preg_replace("/[^0-3]/","",sanitizeString($_POST['access'])); 
        $name=sanitizeString($_POST['name']);
        $status=sanitizeString($_POST['status']);
        $creat=isset($_POST['created'])?sanitizeString($_POST['created']):date('Y-m-d');    
        switch($ed_type){
            case '1':
                $q="UPDATE `s_a` SET `login` = ?, `pass` = ?,`access`=?,`name`=?, `status`=?,`created`=? WHERE `s_a`.`id` = ?";
                $params=array("ssisssi",$login,$pass,$acs,$name,$status,$creat,$user_id);
                $scs_msg='Пользователь изменен!';
                break;
            case '0':
                $q="INSERT INTO `s_a` (`login`, `pass`, `access`,`name`,`status`,`created`) VALUES (?, ?, ?, ?, ?, ?)";
                $params=array("ssisss",$login,$pass,$acs,$name,$status,$creat);
                $scs_msg='Пользователь добавлен!';
                break;
            default:
                echo ' Ошибка: передан неверно тип редактирования записи<br><a href="blog.php">На главную</a>';
                exit;
                break;
        }
        
   //Проверка на существующего пользователя
   $result=queryMysql("SELECT * FROM `s_a` WHERE `login`='$login'");
   if($result->num_rows===0){
    /*
    Входные параметры: запрос, параметры array("bind_patern",переменые..), строка, которая вернется в случае удачного выполнения
    Пример аргументов
    $q="UPDATE `rasp` SET `created` = ?, `title` = ?, `article` = ? WHERE `rasp`.`id`=?";
    $params=array("sssi",$date,$title,$article,$id);
    */
        $html_txt.=preparedQuery($q,$params,'<p class="good_txt"><br>'.$scs_msg.' <a class="page_nums" href="profile.php?op=0">Назад к списку</a><a class="page_nums" href="blog.php">На главную</a></p>'); 
    /* Конец подготавливаемый запрос */
   }else{
    $rec=array("name"=>$name,"login"=>$login,"pass"=>"","access"=>$acs,"id"=>"","status"=>$status,"created"=>$creat);
    $html_txt='<p class="alert_txt"><br>Пользователь с таким логином уже существует!<a class="page_nums" href="profile.php?op=0">Назад к списку</a><a class="page_nums" href="blog.php">На главную</a></p>';
   }

    
}else{
    $html_txt.=($_SERVER['REQUEST_METHOD']=='POST'?"<p class='alert_txt'>Внимание, данные не переданы! Попробуйте снова</p>":"").'<p><br><a class="page_nums" href="profile.php?op=0">Назад к списку</a><a class="page_nums" href="blog.php">На главную</a></p>';
}


$html_txt='
<form class="decor" method="POST" action="edituser.php">
    <div class="form-inner">
        <p class="italyc"> 
        <h3>'.$head_label.'</h3><br>
        </p>
        <input type="hidden" name="user_id" value="'.$user_id.'">
        <input type="hidden" name="edit_type" value="'.$ed_type.'">
        
        <p class="good_txt">Имя</p>
        <input type="text" name="name" required placeholder="Имя" value="'.$rec['name'].'">
        <p class="good_txt">Статус</p>
        <input type="text" name="status" required placeholder="Статус" value="'.$rec['status'].'">
        <p class="good_txt">Дата регистрации. Поле можно оставить пустым - будет внесена текущая дата</p>
        <input type="text" name="created" placeholder="Напр. 2020-05-26" value="'.$rec['created'].'">
        
        <p class="good_txt">Логин</p>
        <input type="text" name="login" required placeholder="Логин" value="'.$rec['login'].'">
        <p class="good_txt">Пароль</p>
        '.$pass_notiece.'
        <input type="text" name="pass" required placeholder="Пароль" value="'.$rec['pass'].'">
        <p class="good_txt">Уровень доступа</p>
        <input type="text" name="access" required placeholder=" Уровень доступа" value="'.$rec['access'].'">

        <div class="flex_c_r flex_wr">
            <input type="submit" value="OK">
        </div>
        
        '.$html_txt.'
    </div>
</form>

';
/**Ограничение кнопок меню для обычного пользователя */

echo '
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/styles_console.css">
	<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
	<title>Редактирование списка пользователей - Мастерюля</title>

</head>
<body>
	
    <header> 
    <div class="flex_sb_r_ac header_line ">

        <h1 class="head_txt">Изменение учетных записей</h1>
        <div class="console_wrap">
            <a href="profile.php"><img src="img/settings.png" width="20px"></a>
        </div>
        
    </div>
    </header>
    <div class="ClearFix">
        <section class="content ">
        '.$html_txt.'	
        </section>
    </div>
</body>
</html>
';
?>
