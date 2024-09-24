<?php
class Upload{
	public $img_dir,$file_dir,$video_dir,$ava_dir,$course_ava_dir,$hashed_dir;
	public function __construct($user){
		$this->hashed_dir=md5($user->user_data['login']);
		$this->img_dir='img/uploaded_photos/'.$this->hashed_dir.'/';;
		$this->file_dir='files/'.$this->hashed_dir.'/';
		$this->video_dir='video/'.$this->hashed_dir.'/';
		$this->ava_dir='img/user_avas/u_id_'.$user->user_data['id'];
		$this->course_ava_dir='img/course_avas/u_id_'.$user->user_data['id'];
	}
	public function upload_file($upl_type,$f_ind){//index в масиве FILES
		$_FILES=reArrayFiles($_FILES[$f_ind]);
		$out="Небыли загружены следующие файлы, проверьте формат файла для: ";
		$err=FALSE;
		//var_dump($_FILES);
		foreach($_FILES as $val){
			//$ext - Допустимые форматы файлов
			switch($upl_type){
				case 0:
					$path=$this->img_dir;
					$ext=array('image/bmp','image/jpeg','image/png','image/gif');
					break;
				case 1:
					$path=$this->file_dir;
				//загрузить как файл можно картинки видео, файлы office,html,xml,txt, openDocument, swf, rar
					$ext=array(
						'image/bmp','image/jpeg','image/png','image/gif',
						'video/mpeg','video/mp4','video/webm','video/x-flv','video/3gpp','video/3gpp2',
						'text/plain',
						'text/html',
						'text/xml',
						'application/vnd.ms-excel',
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'application/msword',
						'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'application/vnd.openxmlformats-officedocument.presentationml.presentation',
						'application/vnd.ms-powerpoint',
						'application/vnd.oasis.opendocument.text',
						'application/vnd.oasis.opendocument.spreadsheet',
						'application/vnd.oasis.opendocument.presentation',
						'application/vnd.oasis.opendocument.graphics',
						'application/x-shockwave-flash',
						'application/x-rar-compressed');
					break;
				case 2:
					$path=$this->video_dir;	
					$ext=array('video/mpeg',
						'video/mp4',
						'video/webm',
						'video/x-flv',
						'video/3gpp',
						'video/3gpp2');
					break;
			}
			if(in_array($val['type'],$ext)){
				$tmp_file_name = $val["tmp_name"];
				$dest_file_name = $path.$val["name"];
				move_uploaded_file($tmp_file_name, $dest_file_name);
			}else{
				$out.=$val["name"].", ";
				$err=TRUE;
				continue;
			}
		}
		return $err?substr($out,0,-2):$err;
	}
	/*
======================================================
-----------Удаление файлов пользователя---------
======================================================
*/
	public function delUserfile($files,$del_type=3){
		foreach($files as $file){
			unlink($file);
		}
	}
/*
======================================================
-----------Изменение аватарки ---------
Возвращает путь до аватарки пользователя
======================================================
*/
	public function editUserAva($f_ind,$user_id,$ext){
		//удаляем старую аватарку если это не default_ava.png
		$prev_ava=queryMysql("SELECT `ava` FROM `s_a` WHERE `id`=".$user_id)->fetch_assoc()['ava'];
		if($prev_ava!="img/user_avas/default_ava.png") unlink($prev_ava);
		//загружаем новую
		$path=$this->ava_dir.'_'.date("YmdHis").'.'.$ext;
		$tmp_file_name = $_FILES[$f_ind]["tmp_name"];
		$dest_file_name = $path;
		move_uploaded_file($tmp_file_name, $dest_file_name);
		queryMysql("UPDATE `s_a` SET `ava`='$path' WHERE `id`=".$user_id);
		return $path;
	}
	public function editCourseAva($f_ind,$course_id,$ext)
	{
		$prev_ava=queryMysql("SELECT `ava` FROM `courses` WHERE `id`=".$course_id)->fetch_assoc()['ava'];
		if($prev_ava!="img/course_avas/default_ava.png") unlink($prev_ava);
		//загружаем новую
		$path=$this->course_ava_dir.'_'.$course_id.'_'.date("YmdHis").'.'.$ext;
		$tmp_file_name = $_FILES[$f_ind]["tmp_name"];
		$dest_file_name = $path;
		move_uploaded_file($tmp_file_name, $dest_file_name);
		queryMysql("UPDATE `courses` SET `ava`='$path' WHERE `id`=".$course_id);
		return $path;
	}
/*
======================================================
-----------Чтение директории пользователя---------
Вход: 0 - обычный режим, 1 - режим для "Редактора" (убирает некоторые элементы из разметки)
Возвращает html разметку файлов пользователя
======================================================
*/
	public function getFilesHtml($op=0){
		//проверка директорий и их создание если их нет
		if(!is_dir($this->img_dir)) {
			mkdir($this->img_dir, 0777, true);
		}
		if(!is_dir($this->file_dir)) {
			mkdir($this->file_dir, 0777, true);
		}
		if(!is_dir($this->video_dir)) {
			mkdir($this->video_dir, 0777, true);
		}
		
		/*===========ФОТО============= */
		$dir_id = opendir($this->img_dir);
		$i = 0;
		$array_files=array();
		while(($path_to_file = readdir($dir_id)) !==false)

		{
			if(($path_to_file !=".") && ($path_to_file !="..")) 

			{
				$array_files[$i][0] = $this->img_dir.basename($path_to_file); 
				$array_files[$i][1]=basename($path_to_file);
				$i++;

			}

		}
		closedir($dir_id);

		$html_txt='<div class="note"> 	
				<div class="photos_block'.($op?'_l':'').'">
					<div class="files_control">
						<div>
							<p>Двойное нажатие по фото увеличит его</p>
							<p>Выбраные фото:<span id="chosen_ph"></span></p>
						</div>
						'.($op?'':'<a class="photo_del rounded_red_rev" href="#">Удалить выбранные фото</a>').'
					</div>
					<div class="photo_wrap">
			';
			if(count($array_files)>0){
				$html_txt.='<p>';
				foreach($array_files as $val){
					$html_txt.='<img class="files_img" src="'.$val[0].'" alt="Картинка">';
				}
				$html_txt.='</p>';
			}else{
				$html_txt.='<p class="good_txt">У вас нет ни одного фото</p>'.($op?'':'<div class="flex_c_r"><a href="0" class="page_nums upl_fl">Загрузить</a></div>');
			}
		$html_txt.='</div></div></div>';

	/*===========ФАЙЛЫ============= */

		$array_files=array();
		$dir_id = opendir($this->file_dir);
		$i = 0;

		while(($path_to_file = readdir($dir_id)) !==false)
		{
			if(($path_to_file !=".") && ($path_to_file !="..")) 
			{
				$array_files[$i][0] = $this->file_dir.basename($path_to_file); 
				$array_files[$i][1]=basename($path_to_file);
				$i++;
		
			}

		}
		closedir($dir_id);

		$html_txt.='<div class="note"> 	
				<div class="files_block">
					<div class="files_control">
						<div>
							<p>Выбраные файлы:<span id="chosen_fl"></span></p>
						</div>
						'.($op?'':'<a class="file_del rounded_red_rev" href="#">Удалить выбранные файлы</a>').' 
					</div>
					<div class="photo_wrap ">
			';
		if(count($array_files)>0){
			foreach($array_files as $val){
				if($op===1){
					$html_txt.='<p><a class="page_nums file" href="'.$val[0].'">Вставить</a><a download="" href="'.$val[0].'"">'.$val[1].'</a></p>';
				}else{
					$html_txt.='<input class="file_chk" type="checkbox" name="file_url" value="'.$val[0].'"><a class="file" href="'.$val[0].'"" download="">'.$val[1].'</a><br>';
				}
			}
		}else{
			$html_txt.='<p class="good_txt">У вас нет ни одного файла</p>'.($op?'':'<div class="flex_c_r"><a href="1" class="page_nums upl_fl">Загрузить</a></div>');
		}
		$html_txt.='</div></div></div>';

	/*===========ВИДЕО============= */

		$array_files=array();
		$dir_id = opendir($this->video_dir);
		$i = 0;

		while(($path_to_file = readdir($dir_id)) !==false)
		{
			if(($path_to_file !=".") && ($path_to_file !="..")) 

			{
				$array_files[$i][0] = $this->video_dir.basename($path_to_file); 
				$array_files[$i][1]=basename($path_to_file);
				$i++;

			}

		}
		closedir($dir_id);

		$html_txt.='<div class="note"> 	
				<div class="video_block">
					<div class="video_control">
						<div>
							<p>Выбраные видео:<span id="chosen_vd"></span></p>
						</div>
						'.($op?'':'<a class="video_del rounded_red_rev" href="#">Удалить выбранные видео</a>').'
					</div>
					<div class="photo_wrap ">
			';
			if(count($array_files)>0){
				foreach($array_files as $val){
					if($op===1){
						$html_txt.='<p><a class="page_nums file" href="'.$val[0].'">Вставить</a><a download="" href="'.$val[0].'"">'.$val[1].'</a></p>';
					}else{
						$html_txt.='<input class="video_chk" type="checkbox" name="file_url" value="'.$val[0].'"><a class="video" href="'.$val[0].'"" target="_blank">'.$val[1].'</a><br>';
					}
				}
			}else{
				$html_txt.='<p class="good_txt">У вас нет ни одного видео</p>'.($op?'':'<div class="flex_c_r"><a href="2" class="page_nums upl_fl">Загрузить</a></div>');
			}
		$html_txt.='</div></div></div>';

		return $html_txt;
	}
}
/* пути к фото и файлам */

?>