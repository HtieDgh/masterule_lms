<?php
require_once('security.php');
$user=new Security();

$returnOut['err']=TRUE;
$returnOut['err_txt']="Параметры не переданы";

if(isset($_POST['edit_type'])){
    /**если пришло по _GET то это update */
    $ed_type=preg_replace_callback('/[^0-2]/',function(){
        $returnOut['err']=TRUE;
        $returnOut['err_txt']="Неверный ed_type";
        echo json_encode($returnOut);
        exit;
    },sanitizeString($_POST['edit_type']));
}else{
    $ed_type='0';
}


if(isset($_POST['id'])){
    $id= preg_replace_callback('/[^0-9]/',function(){
        $returnOut['err']=TRUE;
        $returnOut['err_txt']="Неверный id_note: ".$_POST['id'];
        echo json_encode($returnOut);
        exit;
    },sanitizeString($_POST['id']));

    switch($ed_type){
        case '0':
            $q="DELETE FROM `notes` WHERE `notes`.`id` = ?";
            /* Проверка является ли пользователь автором этой записи или имеет наивысший приоритет 3*/
            $result=queryMysql("SELECT `author_id` FROM `notes` WHERE `id`=$id AND `author_id`=".$user->user_data['id']);
            $trstd_a_id=$result->fetch_assoc()['author_id'];
            break;
        case '1':
            $q="DELETE FROM `rasp` WHERE `rasp`.`id` = ?";
            break;
        case '2':
            $q="DELETE FROM `courses` WHERE `courses`.`id`=?";
            $result=queryMysql("SELECT `author_id` FROM `courses` WHERE `id`=$id AND `author_id`=".$user->user_data['id']);
            $trstd_a_id=$result->fetch_assoc()['author_id'];
            break;
    }
    if($user->access==3 || $user->access==2 && $trstd_a_id==$user->user_data['id']){
        $params=array("i",$id);
        $err=preparedQuery($q,$params);
        $returnOut['err']=$err!=""?TRUE:FALSE;
        $returnOut['err_txt']=$err;
    }else{
        $returnOut['err_txt']="Недостаточно прав";
    }
}

echo json_encode($returnOut);
?>