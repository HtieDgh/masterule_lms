<?php
$login_error=isset($_GET['log_err'])?urldecode($_GET['log_err']):"";
$cur_url=isset($_GET['cur_url'])?urldecode($_GET['cur_url']):"/practicum_trzbd_2020/masterula/blog.php";
$domain="http://188.243.170.117";
echo'
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
	
	<title>Вход - Мастерюля</title>

</head>
<body>
<!--Форма входа-->
<div class="welcome">
    <div id="signin_form" class="ComeIn">
    	<form action="'.$domain.$cur_url.'" method="POST" class="LogIn">	
    		<h2 class="LogInTxt" id="serverInfo">Введите ваш логин и пароль</h2>
    		
    	   <div class="group">      
    		  <input class="UserIn" name="security_login" type="text" required>
    		  <span class="bar"></span>
    		  <label>Логин</label>
    	   </div>
    	   <div class="group">      
    		  <input  class="UserIn" name="security_password" type="password" required>
    		  <span class="bar"></span>
    		  <label>Пароль</label>
    	   </div>
    	   
    	   <div class="Entering">
			<input class="EnterBtn" type="submit" value="Войти">
			<br>
			<p class="alert_txt">'.$login_error.'</p>
			</div>
			Еще нет учетной записи? <a href="regist.php">Зарегистрироваться</a><br><br>
			<a href="blog.php">На главную</a>
    	</form>
    </div>
</div>
</body>
</html>';
?>