<?php
require_once('lib/functions.php');
$cur_date=date('Y-m-d');
$html_txt='';
if(isset($_POST['security_name'])&&
isset($_POST['security_password'])&&
isset($_POST['security_login'])
){
    $login=sanitizeString($_POST['security_login']);
    $pass=md5(sanitizeString($_POST['security_password']));
    $name=sanitizeString($_POST['security_name']);
    /**проверка на существующие учетные записи */
    $q="SELECT `login`,`name` FROM `s_a` where `name`='$name' OR `login`='$login'";
    $result=queryMysql($q);
    if($result->num_rows==0){
        $q="INSERT INTO `s_a` (`id`, `login`, `pass`,`name`,`access`,`created`) VALUES (NULL,?, ?, ?,1,'$cur_date')";
        $html_txt=preparedQuery($q,array('sss', $login,$pass,$name),'<p class="good_txt">Учетная запись зарегистрирована! Введите свой email и пароль <a href="login.php">здесь</a></p>');
       
    }else{
        $html_txt='<p class="alert_txt">Учетная запись с таким логином или именем уже занята! Попробуйте снова</p>';
    }
}else if($_SERVER['REQUEST_METHOD']=='POST'){
    $html_txt='<p class="alert_txt">Параметры не переданы! Попробуйте снова</p>';
}
echo'
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
	
	<title>Регистрация - Мастерюля</title>

</head>
<body>
<!--Форма входа-->
<div class="welcome">
    <div id="signin_form" class="ComeIn">
    	<form action="regist.php" method="POST" class="LogIn">	
    		<h2 class="LogInTxt" id="serverInfo">Регистрация</h2>
    		<div class="group">      
    		  <input class="UserIn" name="security_name" type="text" required>
    		  <span class="bar"></span>
    		  <label>Имя</label>
    	   </div>
    	   <div class="group">      
    		  <input class="UserIn" placeholder="введите email" name="security_login" type="email" required>
    		  <span class="bar"></span>
    		  <label>e-mail</label>
    	   </div>
    	   <div class="group">      
    		  <input  class="UserIn" name="security_password" type="password" required>
    		  <span class="bar"></span>
    		  <label>Пароль</label>
    	   </div>
    	   <div class="Entering">
			<input class="EnterBtn" type="submit" value="Зарегитрироваться">
			<br>
			'.$html_txt.'
			</div>
            Уже зарегистрированы? <a href="login.php">Войти</a><br><br>
            <a href="blog.php">На главную</a>
    	</form>
    </div>
</div>
</body>
</html>';
?>