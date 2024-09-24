<?php
require_once('security.php');
$user=new Security();
require_once('lib/move_uploaded.php');
$upl=new Upload($user);

$returnOut['err']=FALSE;
if(isset($_POST['chosen'])){
    $files_str=sanitizeString($_POST['chosen']);
    if(isset($_POST['del_type'])){
        //получаем масив для проверки директорий
        $files=explode(', ',$files_str);
        foreach($files as $file){
            //если директория переданого файла не совпадает с доступной, то вывод ошибки
            if(strpos($file,$upl->hashed_dir)===FALSE){
                $returnOut['err']=TRUE;
               $returnOut['err_txt']="Недостаточно прав";
                break;
            }
        }
        if(!$returnOut['err']){
            $d_t=sanitizeString($_POST['del_type']);
            $upl->delUserfile($files,$d_t);
        }   
    }else{
        $returnOut['err']=TRUE;
        $returnOut['err_txt']="Непередан del_type";
    }
}else{
    $returnOut['err']=TRUE;
    $returnOut['err_txt']="Непередан список файлов для удаления";
}
echo json_encode($returnOut); 
?>