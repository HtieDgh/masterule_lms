<?php
require_once('security.php');
require_once('lib/notes_process.php');
require_once('lib/subscribe.php');
$user=new Security();
$notes=new Notes();
$sub=new Subscriber($user->user_data['id']);
$a_i=0;
$sub_id=0;
$c_id=0;
$auth_block_html='';//блок слева с подписками
$auth_page_html='';//Блок с информацией об авторах
$crs_block_html='';//Блок с курсами
$main_btn_html='
<div class="note">
	<a class="author_href" href="blog.php">
		<div class="author_wrap flex_fs_r_ac">
			<div class="ava_prof_block">
				<div class="ava_img">
					<img src="img/bg/main_btn_ico.png">
				</div>
			</div> 
			<div>
				<h2 class="note_title">Главная</h2>
			</div>
		</div>
	</a>
	<a class="author_href '.($sub_id!==0?'cur_author':'').'" href="blog.php?cur_sub=1">
	<div class="author_wrap flex_fs_r_ac">
		<div class="ava_prof_block">
			<div class="ava_img">
				<img src="img/bg/subs_btn_ico.png"">
			</div>
		</div> 
		<div>
			<h2 class="note_title">Подписки</h2>
		</div>
	</div>
</a>
</div>
';
//имя сайта
$title='Мастерюля';
/**Ограничение кнопок меню в зависимости от доступа */
		$menu=$user->access>0?'<a href="profile.php"><img src="img/settings.png" width="20px"></a>':'';
	//кнопка Войти
		$enter_btn=$user->access>0?'<li><a href="exit.php">Выйти <span>'.$user->user_data['name'].'</span></a></li>':'<li><a href="login.php">Войти</a></li>';
	//Кнопка Расписание
		$rasp=$user->access>0?'<li><a href="rasp.php">Расписание</a></li>':'';

/*Навигация по страницам */
	if(isset($_GET['page'])){
		$page= preg_replace_callback("/[^0-9]/",function(){
			echo 'Произошла ошибка 404 : Такой страницы не существует. Попробуйте снова<br><br><a href="blog.php">На главную</a> ';
			exit;
		},$_GET['page']);
		$title='Страница '.$page.' - '.$title;
	}else{
		$page=1;
	}
//отобразить подписки
if(isset($_GET['cur_sub'])){
	if($user->access>0){
		$sub_id=$user->user_data['id'];
	}
}
//Отображение записей конкретного курса
if(isset($_GET['c_id'])){
	$c_id=preg_replace_callback("/[^0-9]/",function(){
		echo 'Произошла ошибка 404 : Такой страницы не существует. Попробуйте снова<br><br><a href="blog.php">На главную</a> ';
		exit;
	},sanitizeString($_GET['c_id']));
}
//отобразить записи только выбраного автора
	if(isset($_GET['a_i'])){
		$a_i=preg_replace_callback("/[^0-9]/",function(){
			echo 'Произошла ошибка 404 : Такой страницы не существует. Попробуйте снова<br><br><a href="blog.php">На главную</a> ';
			exit;
		},sanitizeString($_GET['a_i']));
		$a=$sub->getAuthorsList("",0,2,$a_i);
	//Вывод инф-и выбраного автора
		$auth_page_html.='
			<div class="note">
				<div class="author_wrap flex_sb_r_ac">
					<div class="ava_prof_block">
						<div class="ava_img">
							<img id="img_'.$a[0]['id'].'" src="'.$a[0]['ava'].'">
						</div>
					</div> 
					<div>
						<h2 class="note_title">'.$a[0]['name'].'</h2>
						<div class="author_stats">'.$a[0]['subs_count'].' подписчиков &#8226; '.$a[0]['notes_count'].' Записей</div>
						<p>'.$a[0]['status'].'</p>
					</div>
					<div>
						'.($user->user_data['id']==0?'<a class="page_nums" href="login.php">Подписатся</a>':($a[0]['is_subbed']==1?'<a id="authorid_'.$a[0]['id'].'" class="a_sub_btn page_nums_rev" href="#">Отписаться</a>'
							:($a[0]['is_subbed']==2?'<span class="sub_btn_noa page_nums_rev">Это вы</span>'
								:'<a id="authorid_'.$a[0]['id'].'" class="a_sub_btn new_sub_btn page_nums" href="#">Подписатся</a>'))).'
					</div> 
				</div>
			</div>
		';
	//смена заголовка в title
		$title=$a[0]['name'].' - Мастерюля';
	//Вывод инф-и о курсах выбранного автора
		$a=$sub->getCourseList($a[0]['id']);
		$auth_page_html.='<div class="flex_c_r">';
		foreach ($a as $v) {
			$auth_page_html.='
				<div class="note course_block">
					<div class="flex_sb_c">
						<div class="ava_prof_block flex_c_r">
							<div class="ava_img">
								<img id="img_'.$v['id'].'" src="'.$v['ava'].'">
							</div>
						</div> 
						<div class="inner_cont_block">
							<div class="flex_sb_r_ac"><h2 class="note_title">'.$v['title'].'</h2><p>'.$v['created'].'</p></div>
							<div class="author_stats">'.($v['private']==1?'&#128274; &#8226; ':'').$v['subs_count'].' Учеников &#8226; '.$v['notes_count'].' Записей</div>
							<p>'.$v['article'].'</p>
						</div>
						
						<div class="flex_c_r">
						'.($v['is_subbed']==1?'<a class="page_nums'.($v['id']==$c_id?'_rev"':'').'" href="blog.php?a_i='.$a_i.'&c_id='.$v['id'].'">Перейти</a><a id="cauthorid_'.$v['id'].'" class="c_sub_btn page_nums_rev" href="#">Отписаться</a>'
							:($v['is_subbed']==2?'<a id="cauthorid_'.$v['id'].'" class="c_sub_btn c_rqst_send page_nums_rev" href="#">Заявка подана</a>'
							:($v['is_subbed']==3?'':'<a id="cauthorid_'.$v['id'].'" class="c_sub_btn new_sub_btn page_nums" href="#">Подписатся</a>'))).'
						</div>
					</div>
					
				</div>
			';
		}
		$auth_page_html.='</div>';
	}
	
//отображение всех текущих подписок в левой части главной стр
	if($user->access>0){//пользователь авторизован
		$authors=$sub->getAuthorsList("",0,1);
		$crs=$sub->getCourseList($user->user_data['id'],"",0);
		if(count($authors)>0){
			foreach($authors as $a){
				$auth_block_html.='
				<a class="author_href '.($a['id']==$a_i?'cur_author':'').'" href="blog.php?a_i='.$a['id'].'">
					<div class="author_wrap flex_fs_r_ac">
						<div class="ava_prof_block">
							<div class="ava_img">
								<img id="img_'.$a['id'].'" src="'.$a['ava'].'">
							</div>
						</div> 
						<div>
							<h2 class="note_title">'.$a['name'].'</h2>
						</div>
					</div> 
				</a>
				';
			}
		}else{//пользователь еще не подписан ни на одного автора
			$auth_block_html='
				<p class="note_cntrl_btn">Вы еще не подписались ни на одного автора!</p>
				<div class="flex_c_r"><a href="profile.php?op=3" class="page_nums note_cntrl_btn">Найти автора</a></div>
			';
		}
		//Курсы
		if(count($crs)>0){
			foreach($crs as $a){
				$crs_block_html.='
				<a class="author_href '.($a['id']==$c_id?'cur_author':'').'" href="blog.php?c_id='.$a['id'].'">
					<div class="author_wrap flex_fs_r_ac">
						<div class="ava_prof_block">
							<div class="ava_img">
								<img id="img_'.$a['id'].'" src="'.$a['ava'].'">
							</div>
						</div> 
						<div>
							<h2 class="note_title">'.$a['title'].'</h2>
						</div>
					</div> 
				</a>
				';
			}
		}else{
			$crs_block_html='
				<p class="note_cntrl_btn">Вы еще не подписались ни на одного автора!</p>
				<div class="flex_c_r"><a href="profile.php?op=3" class="page_nums note_cntrl_btn">Найти автора</a></div>
			';
		}
		$crs_block_html='
			<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Ваши Курсы</p></div><hr>
				'.$crs_block_html.'
			</div>
		';
	}else{//пользователь не авторизован
		$auth_block_html='
			<p class="note_cntrl_btn">Чтобы подписаться на автора или курс вам необходимо</p>
			<div class="flex_c_r"><a href="login.php" class="page_nums note_cntrl_btn">Войти</a></div>
		';
	}
	$auth_block_html='
		<div class="note"><div class="flex_c_r"><p class="good_txt italyc">Ваши подписки</p></div><hr>
			'.$auth_block_html.'
		</div>
			'.$crs_block_html;
	

/* 
	Отображение записей
	Файл notes_proccess.php
	Вход:
	$cur_url=blog.php, $user_search='', $a_i=0, $page=1, $user, $sub_id=0
*/
$notes->get_notes('blog.php',
		(isset($_GET['user_search'])?$_GET['user_search']:''),
		$a_i,$page,
		$user,$sub_id,$c_id);

/*
<li><a href="email.php">Отправить письмо</a></li>
			<li>'.$enter_btn.'</li>
Вывод html страницы*/

echo '
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">

	<script src="js/jquery-3.3.1.js"></script><!--Библиотека jquery-->
	<title>'.$title.'</title>

</head>
<body>
<header>

	<div class="flex_sb_r_ac header_line ">
		<div class="logo">
			<a href="blog.php"><img src="img/bg/masterula.png" alt="Мастерюля" class="logo_full"><img class="logo_short" src="img/bg/masterula_short.png" alt="Мастерюля"></a>
		</div>
		<div class="d7">
			<form method="GET" action="blog.php" class="flex_c_r" id="user_search">
				<input type="text" required name="user_search" placeholder="'.$notes->search.'">
				<button type="submit"><img src="img/search.png"></button>
			</form>
		</div>
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

	<section class="content">
		<div class="ClearFix">
			'.$auth_page_html.'
			<div class="exposed page_block_wrap">
				<p id="chosen">Всего записей: '.$notes->note_count.'</p>
				'.$notes->drop_search.'
				<div class="exposed page_block">
					<hr>
					<p>Перейти на страницу:</p>
						'.$notes->page_html.'
				</div>
			
			</div>
			
			<article class="flex_sb_r artcl_block">
				<div class="left_block">
				'.$main_btn_html.'
					'.$auth_block_html.'
					<aside class="flex_c_r_ac asd_block Pacifico_txt">
						<a class="tag_btn" href="Лепка">Лепка</a>
						<a class="tag_btn" href="ИЗО">ИЗО</a>
						<a class="tag_btn" href="Подки_из_пр_мат">Поделки из природных материалов</a>
						<a class="tag_btn" href="Подки_из_нит">Поделки из ниток</a>
						<a class="tag_btn" href="Аппликация">Аппликация</a>
						<a class="tag_btn" href="Народное_творчество">Народное творчество</a>
					</aside>
				</div>
				<div class="right_block">
					'.$notes->notes_html.'
				</div>
			</article>

			<div class="page_block_wrap exposed page_block">
				<p>Перейти на страницу:</p>
					'.$notes->page_html.'
			</div>	
		</div>
	</section>
	'.(isset($user->user_data['name'])?'
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
				<textarea id="comment_txt" name="comment_txt" class="UserIn comment_textarea"></textarea>
					</div>
				<div class="Entering">
					<input class="EnterBtn send_cmnt" type="submit" value="Отправить">
				</div>
			</form>
		</div>
	</div>
	':'').'
	<a class="mbtn" href="#chosen">&uArr;</a>

	<footer>
		<p><a target="_blank" href="https://vk.com/id7167157">Тимохина Юлия</a> - мастер, творящий чудеса</p>
	</footer>
<script src="js/main.js"></script>
<script src="js/burger.js"></script>
<script src="js/scroll.js"></script>
<script src="js/subs.js"></script>
</body>
</html>
';
?>