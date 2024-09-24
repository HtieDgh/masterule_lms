<?php
require_once('security.php');
$user=new Security();
require_once('lib/move_uploaded.php');
$upl=new Upload($user);
$err_upload="";
$menu=$user->access>0?'<a href="profile.php"><img src="img/settings.png" width="20px"></a>':'';
	//кнопка Войти
		$enter_btn=$user->access>0?'<li><a href="exit.php">Выйти <span>'.$user->user_data['name'].'</span></a></li>':'<li><a href="login.php">Войти</a></li>';
	//Кнопка Расписание
		$rasp=$user->access>0?'<li><a href="rasp.php">Расписание</a></li>':'';

if(isset($_FILES['user_files']) && isset($_POST['upld_type'])){
	$_POST['upld_type']=preg_replace_callback("/[^0-2]/",function(){
		echo 'Произошла ошибка: неправельный тип загрузки файла, свяжитесь с админомнистратором или попробуйте снова<br><br><a href="photos.php">К Фото / файлам (back to photos)</a> <br>File_upload error';
		exit;
	},sanitizeString($_POST['upld_type']));
	//если проверки прошли успешно, то загружаем файл
	$err_upload=$upl->upload_file($_POST['upld_type'],'user_files');
}

//Запускаем просмотр папок и получаем html для файлов
$html_txt=$upl->getFilesHtml();
/*========вывод htmll старницы======== */
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
		<script src="js/photos.js"></script>  
	<title>Фото и файлы - Мастерюля</title>

</head>
<body>
	
	<header>
		<div class="flex_sb_r_ac header_line ">
			<div class="logo">
				<a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля" class=""></a>
			</div>
			<h1 class="head_txt">Фото и файлы</h1>
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
	<div class="ClearFix">
		<section class="content ">
			'.$html_txt.'
			<div class="upl_fl_wrap flex_sb_r">
				<a class="page_nums upl_fl" href="0">Загрузить фото</a>
				<a class="page_nums upl_fl" href="1">Загрузить файлы</a>
				<a class="page_nums upl_fl" href="2">Загрузить видео</a>
			</div>
		</section>
	</div>
	
	<img src="" id="open_full_img" alt="Вспомогательное изображение">

	<div class="my_window">
		<a href="#" class="close">&times;</a>		
		<span id="upl_type_txt"></span>
		<div class="modal-body">
			<form enctype="multipart/form-data" method="POST" action="photos.php" class="flex_sb_r">
				<div class="group">
					<input type="hidden" value="none" id="upld_type" name="upld_type">      
				   <input name="user_files[]" class="UserIn" id="upload_files" type="file" multiple required>
				</div>
				
				<div class="Entering">
					<input class="page_nums" type="submit" value="Отправить">
				</div>
			</form>
		</div>
	</div>

<a class="mbtn nxt" href="#chosen_ph">&uArr;</a>
<footer>
	<p><a target="_blank" href="https://vk.com/id7167157">Тимохина Юлия</a> - мастер, творящий чудеса</p>
</footer>
<script src="js/burger.js"></script>
<script src="js/file_upload.js"></script>
'.($err_upload?
'<script>
alert("'.$err_upload.'");
</script>':'').'
</body>
</html>
';
?>