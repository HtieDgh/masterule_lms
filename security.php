<?php
require('lib/functions.php');
class Security
{
	
	public $user_data,$access,$cur_url;
	
	function __construct()
	{	
		$this->cur_url=$_SERVER['REQUEST_URI'];
		$this->loginTest();
		
	}
	
	public function loginTest()
	{
		
		// Извлекается логин и пароль из переданных данных
		$user_data['login']= isset($_POST['security_login']) ? $_POST['security_login'] : (isset($_COOKIE['security_login']) ? $_COOKIE['security_login'] : '');
		$user_data['password']=isset($_POST['security_password']) ? md5($_POST['security_password']) : (isset($_COOKIE['security_password']) ? $_COOKIE['security_password'] : '');
		
		// Изначально устанавливается id пользователя=0 (пользователя нет)
		$user_data['id']=0;
		
		// Если заданы логин и пароль, проверяется их актуальность
		if($user_data['login'] !== '' && $user_data['password'] !== '')
		{
			$query="SELECT s.`id`,s.`access`,s.`name` FROM `s_a` as s WHERE  s.`login`='".preg_replace("/[^a-zA-Z0-9_@.]/","",$user_data['login'])."' AND  s.`pass`='".$user_data['password']."'";
			$result=queryMysql($query);

			// Если пользователь с такими данными найден
			if ($result->num_rows>0)
			{
                // Данные о пользователе сохраняются в переменную
                $rec=$result->fetch_assoc();
				$user_data['access']=$rec['access'];
				$user_data['name']=$rec['name'];
				$user_data['id']=$rec['id'];
				// Логин и пароль сохраняются в COOKIE пользователя
				$this->updateCookie('security_login',$user_data['login'],"/",$_SERVER['HTTP_HOST']);
				$this->updateCookie('security_password',$user_data['password'],"/",$_SERVER['HTTP_HOST']);
				$this->updateCookie('user_name',$user_data['name'],"/",$_SERVER['HTTP_HOST']);
								
				$this->access=(int)$rec['access'];
				
				unset($login_error);
			}
			else
			{
				$login_error='Неправильный логин или пароль.';	
			}
		}else if($user_data['login'] !== '' xor $user_data['password'] !== ''){
			$login_error='Параметры не переданы, попробуйте снова!<br><br>';
        }else{
			$this->access=0;
		}
		$this->user_data=$user_data;
		// Орграничение доступа
		switch($this->access){
			case 0:// Роль Мимо-проходящий - Неавторизованый пользователь
				if(strpos($this->cur_url,'profile.php')!==false
				|| strpos($this->cur_url,'editprofile.php')!==false ){
					$login_error="Вы не авторизованы: Войдите сейчас<br><br>";
					$conc=true;
				}else{
					$conc=strpos($this->cur_url,'deletenote.php')===false
					|| strpos($this->cur_url,'note.php')===false
					|| strpos($this->cur_url,'delfile.php')===false 
					|| strpos($this->cur_url,'deluser.php')===false 
					|| strpos($this->cur_url,'edituser.php')===false 
					|| strpos($this->cur_url,'photos.php')===false;
				}
				break;
			case 1://Роль Читатель - Обычный пользователь
				$conc=(strpos($this->cur_url,'deletenote.php')===false
					|| strpos($this->cur_url,'note.php')===false
					|| strpos($this->cur_url,'delfile.php')===false 
					|| strpos($this->cur_url,'deluser.php')===false 
					|| strpos($this->cur_url,'edituser.php')===false 
					|| strpos($this->cur_url,'photos.php')===false) 
					&& (strpos($this->cur_url,'op=4')===false);//курсы в профиле
					
				break;
			case 2://Роль Автор - Создатель контента
				$conc=strpos($this->cur_url,'deluser.php')===false
					|| strpos($this->cur_url,'edituser.php')===false ;
				break;
			case 3://Роль Художник ДПИ - Полный доступ
				$conc=true; 
				break;
			default://Любой другой пользователь
				$conc=false; 
				break;
		}
		
		if(!$conc){
			echo 'Доступ запрещен!<br><a href="blog.php">Вернутся на главную</a>';
			exit;	
		}
		// Конец проверки 
		if(isset($login_error))
		{
			$this->loginPage($login_error);
		}
		unset($user_data);
	}
	
	function loginPage($login_error="")
	{
        header("Location: ".SITE_DOMAIN."/login.php?log_err=".urlencode($login_error)."&cur_url=".$this->cur_url);
		exit;
	}
	public function getUserInfo($id=0){
		queryMysql("SET lc_time_names = 'ru_UA'");
		$id=$id===0?$this->user_data['id']:$id;
		$query="SELECT DATE_FORMAT(`created`,'%e %M %Y')as 'frmtd_created',s.`ava`,s.`status` FROM `s_a` as s WHERE  s.`id`=".$id;
		$result=queryMysql($query);
		$rec=$result->fetch_assoc();
		$this->user_data['created']=$rec['frmtd_created'];
		$this->user_data['ava_url']=$rec['ava'];
		$this->user_data['status']=$rec['status'];
		return $this->user_data;
	}
	/**
	 * Обновляет cookie
	 */
	public function updateCookie($key,$val,$url,$host,$cookie_time=false)
	{
		// время жизни COOKIE-данных продлевается на 24 часа
		if($cookie_time){$cookie_time=time() + 24 * 3600;}
		setcookie($key,$val,$cookie_time,$url,$host);
	}
	public function exitPage()
	{
		
		$cookie_time=time() - 3600;
		// Логин и пароль сохраняются в COOKIE пользователя
		setcookie('security_login', '', $cookie_time, "/", $_SERVER['HTTP_HOST']);
		setcookie('security_password', '', $cookie_time, "/", $_SERVER['HTTP_HOST']);
		setcookie('user_name', '', $cookie_time, "/", $_SERVER['HTTP_HOST']);
		
		header("Location: ".SITE_DOMAIN."/blog.php");
	}
	public function becomeAuthor(){
		return queryMysql("UPDATE `s_a` SET `access`=2 WHERE `id`=".$this->user_data['id']);
	}
	public function checkCourseAuth()
	{
		return queryMysql("SELECT ".$this->user_data['id']." IN(SELECT s.`id` FROM `s_a` s INNER join `courses` c ON s.`id`=c.`author_id` WHERE c.`author_id`=".$this->user_data['id'].")");
	}
}
?>