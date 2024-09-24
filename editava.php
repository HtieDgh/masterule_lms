<?php
//Этот файл обрабатывает ajax запросы на изменение аватарки
require_once('security.php');
$user=new Security();
require_once('lib/move_uploaded.php');
$upl=new Upload($user);

$returnOut['err']=TRUE;
$returnOut['err_txt']="Параметры не переданы";

if(isset($_FILES['user_ava'])){
    switch($_FILES['user_ava']['type'])
    {
        case 'image/bmp': $ext = 'png'; break;
        case 'image/jpeg': $ext = 'png'; break;
        case 'image/png': $ext = "png"; break;
    }
    if(isset($ext)){
        switch ($_POST['ed_type']) {
            case '0':
                $returnOut['new_ava_url']=$upl->editUserAva('user_ava',$user->user_data['id'],$ext);
                $returnOut['err']=FALSE;
                $returnOut['err_txt']="";
                break;
            case '1':
                $returnOut['new_ava_url']=$upl->editCourseAva('user_ava',$_POST['crs_id'],$ext);
                $returnOut['err']=FALSE;
                $returnOut['err_txt']="";
                break;
            default:
                $returnOut['err_txt']="Неверный тип редактирования";
                break;
        }
        
    }else{
        $returnOut['err_txt']="Неподдерживаемый тип фотографии - пожалуйста, выберите другой"; 
    }
    
}

echo json_encode($returnOut); 
?>