<?php
require_once('security.php');
$user=new Security();

require_once('lib/phpmailer/PHPMailerAutoload.php');
function sendMail($email='неизвестен',$name='неизвестен',$subject='ошибка',$txt='ошибка'){
	/*Настройка почты*/
	$mail = new PHPMailer;
	$mail->CharSet = 'utf-8';
	
	
	//$mail->SMTPDebug = 3;                               // Enable verbose debug output
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.mail.ru';  																							// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'komarovin.max@mail.ru'; // Ваш логин от почты с которой будут отправляться письма
	$mail->Password = 'mishawor123'; // Ваш пароль от почты с которой будут отправляться письма
	$mail->SMTPSecure = 'ssl';                   // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465; // TCP port to connect to / этот порт может отличаться у других провайдеров
	
	$mail->setFrom('komarovin.max@mail.ru'); // от кого будет уходить письмо?
	$mail->addAddress('komarovin.max@mail.ru');     // Кому будет уходить письмо 
	//$mail->addAddress('ellen@example.com');               // Name is optional
	//$mail->addReplyTo('info@example.com', 'Information');
	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML
	
	$mail->Subject = 'Письмо с сайта Мастерюля';
	$mail->Body    = '<br>Почта этого пользователя: '.$email.'
	<br>Имя этого пользователя: '.$name.'
	<br>Тема письма: '.$subject.'
	<br>Текст письма:<br> '.$txt.'
	';
	$mail->AltBody = '';
	
	return $mail->send();
	/*Конец Настройка почты*/
}

/**Ограничение кнопок меню для обычного пользователя */
$enter_btn=$user->access>0?'<li><a href="exit.php">Выйти <span>'.$user->user_data['name'].'</span></a></li>':'<li><a href="login.php">Войти</a></li>';
$menu=$user->access>0?'<a href="profile.php"><img src="img/settings.png" width="20px"></a>':'';
//Кнопка Расписание
	$rasp=$user->access>0?'<li><a href="rasp.php">Расписание</a></li>':'';

$html_txt='';
if(isset($_POST['sender_subject']) && isset($_POST['sender_name'])&& isset($_POST['sender_email'])&& isset($_POST['sender_txt'])){
	/*var_dump($_POST);*/
	$err='';
	try{
		foreach($_POST as $val){
			if($val=='') throw new Exception('Пустое поле');
		}
	}catch(Exception $e){
		$err=$e->getMessage();
	}
	if($err!='Пустое поле'){
		$_POST['sender_email']=sanitizeString($_POST['sender_email']);
		$_POST['sender_name']=sanitizeString($_POST['sender_name']);
		$_POST['sender_subject']=sanitizeString($_POST['sender_subject']);	
		$_POST['sender_txt']=sanitizeString($_POST['sender_txt']);
		/*Отправка email*/
		if(sendMail($_POST['sender_email'],$_POST['sender_name'],$_POST['sender_subject'],$_POST['sender_txt'])){
			$html_txt='<p class="good_txt">Письмо отправлено! <a href="blog.php">На главную</a></p>';
		}else{
			$html_txt='<p>Ошибка отправки попробуйте еще раз! <a href="blog.php">На главную</a></p>';
		}
	}else{
		$html_txt='<span class="alert_txt">Вставте данные во все поля!!!</span>
		';
	}	
}

$html_txt='
		<form class="decor" method="post" action="email.php">
			<div class="form-left-decoration"></div>
			<div class="form-right-decoration"></div>
			<div class="circle"></div>
			<div class="form-inner">
				<h3>Написать нам</h3>
				<input type="email" name="sender_email" required placeholder="Ваша почта">
				<input type="text" name="sender_name" required placeholder="Ваше имя">
				<input name="sender_subject" required type="text" placeholder="Тема сообщения">
				<textarea name="sender_txt" required placeholder="Сообщение..." rows="10"></textarea>
				<div class="flex_c_r"><input type="submit" value="Отправить"></div>
				'.$html_txt.'
			</div>
		</form>
		';

/*
	<ul class="nav"> 
	<li>'.$enter_btn.'</li>
	'.$menu.'
	</ul>


Вывод html старницы */
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
	<title>Отправить сообщение - Мастерюля</title>

</head>
<body>
	
		<header>
		<div class="flex_sb_r_ac header_line ">
		<div class="logo">
			<a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля" class=""></a>
		</div>
			<h1 class="head_txt">Обратная связь</h1>

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
				<li><a class="active" href="email.php">Отправить письмо</a></li>
				'.$rasp.'
				'.$enter_btn.'
			</ul>	
		</div>	
		<div class="ClearFix">
			<section class="content ">
			
			'.$html_txt.'
			
			</section>
		</div>
	<footer>
		<p><a target="_blank" href="https://vk.com/id7167157">Тимохина Юлия</a> - мастер, творящий чудеса</p>
	</footer>
	<script src="js/burger.js"></script>
</body>
</html>
';
?>