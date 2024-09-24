<?php
require_once('security.php');
$user=new Security();
 
$cur_date=date('Y-m-d');
$returnOut['err']=TRUE;
$returnOut['err_txt']="Параметры не переданы";
$returnOut['access']=$user->access;
function showCurComments($id,$user){
    $returnOut['html']='';
    $q="SELECT c.`id`,c.`created`,c.`comment`,s.`name`,s.`ava`,n.`author_id`
        FROM `comments` as c inner join `s_a` as s on c.`author_id`=s.`id` INNER JOIN `notes` n on c.`note_id`=n.`id`
        WHERE c.`note_id`=$id 
        ORDER BY c.`created` DESC,c.`id` DESC
    ";
    $result=queryMysql($q);
    $returnOut['count']=$result->num_rows;
    for($i=0;$i<$result->num_rows;$i++){
        $rec=$result->fetch_assoc();
        $returnOut[$i]=$rec;
        $returnOut['html'].='
            <div class="comment" id="comment_'.$id.'_'.$rec['id'].'">
                <div class="flex_sb_r_ac">
                    <div class="flex_sb_r_ac"> 
                        <div class="ava_img cmnt_ava_img mr_r_10">
                            <img id="img_'.$rec['author_id'].'" src="'.$rec['ava'].'">
                        </div>
                        <h2 class="comment_title">'.$rec['name'].'</h2>
                    </div>
                    <p class="comment_date italyc">' .$rec['created'].'</p>
                </div>'.
                    ($rec['author_id']==$user->user_data['id'] || $user->access==3?'
                         <div class="comment_control">
                            Управление:<a class="comment_del note_cntrl_btn" id="commentdel_'.$id .'_'.$rec['id'].'" href="#">Удалить коментарий</a>
                        </div>
                    ':'').'
                <hr>
                <p class="comment_text">'.$rec['comment'].'</p>
            </div>
        ';
    }
    
    return $returnOut;
}
if(isset($_POST['id']) && isset($_POST['com_option'])){
    $id=sanitizeString($_POST['id']);
    $id=preg_replace("/[^0-9]/","",$id);
    switch($_POST['com_option']){
/*Показать все текущие коментарии */
        case 'show_cur':
            $returnOut['comments']=showCurComments($id,$user);
            $returnOut['err']=FALSE;
            $returnOut['err_txt']="";
            break;
        case 'new_com':
/*Новый коментарий*/
            $comment=sanitizeString($_POST['comment_txt']);
            $q="INSERT INTO `comments`(`id`,`note_id`,`author_id`,`created`,`comment`)VALUES(NULL,?,".$user->user_data['id'].",'$cur_date',?)";
            $params=array("is",$id,$comment);
            $result=preparedQuery($q,$params);
            if($result==""){
                $returnOut['err']=FALSE;
                $returnOut['err_txt']="";
                $returnOut['comments']=showCurComments($id,$user);
            }else{
                $returnOut['err']=TRUE;
                $returnOut['err_txt']=$result;
            }
            
            break;
        case 'delete_com':
 /**Удаление коментария */
            $q="SELECT n.`author_id` 
                FROM `notes` as n INNER JOIN `comments` as c on c.`note_id`=n.`id` 
                WHERE c.`id`=$id
            ";
            $note_auth_id=queryMysql($q)->fetch_assoc()['author_id'];
            if($user->access>1 && $user->user_data['id']==$note_auth_id || $user->access==3){
                $q="DELETE FROM `comments` WHERE `id`=?"; 
                $params=array("i",$id);
                $result=preparedQuery($q,$params);
                if($result==""){
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']="";
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']=$result;
                }
              
            }else{
                $returnOut['err']=TRUE;
               $returnOut['err_txt']="Недостаточно прав";
            }
            break;
    }
    
}
echo json_encode($returnOut);
?>