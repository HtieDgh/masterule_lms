<?php
require_once('security.php');
$user=new Security();

$err='';
if(isset($_GET['user_id'])){
    $id=sanitizeString($_GET['user_id']);
    $q="DELETE FROM `s_a` WHERE `s_a`.`id` = ?";
    $params=array("i",$id);
    $err=preparedQuery($q,$params);
    if($err!=""){
        echo $err;
    }else{
        header("Location: ".SITE_DOMAIN."/profile.php");
    }
}
?>