<?php
require_once('security.php');
require_once('lib/subscribe.php');
    $user=new Security();
    $sub=new Subscriber($user->user_data['id']);
    $returnOut['err']=TRUE;
    $returnOut['err_txt']='Параметры не переданы';
    $a_i='';
    if(isset($_POST['send_id']) 
    && isset($_POST['op']) && $user->user_data['id']!==0){   
        $send_id=sanitizeString($_POST['send_id']);
        $sub_id=isset($_POST['sub_id'])?sanitizeString($_POST['sub_id']):$user->user_data['id'];
        switch($_POST['op']){
            case '1':
                if($sub->subscribe($send_id)){
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Не удалось подписаться, свяжитесь с админом';
                }
                break;
            case '2':
                if($sub->unscribe($send_id)){
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Не удалось отписаться, свяжитесь с админом';
                }
                break;
            case '3'://Подписка, подача заявки на курс
                $c=$sub->subOnCourse($send_id,$sub_id);
                if($c!==2) {
                    $returnOut['data']['code']= $c;
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Ошибка: Заявка была уже подана ранее';
                }
                break;
            case '4'://Отписка, отмена заявки на курс 
            case '7':// Отмена заявки
                $c=$sub->unsubOnCourse($send_id,$sub_id);
                if($c!==2) {
                    $returnOut['data']['code']= $c;
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Ошибка: такого подписчика и/или курса нет';
                }
                break;
            case '5'://Получить список заявок
                if($user->checkCourseAuth()){
                    $returnOut['data']['rqsts']=$sub->getRqstList($send_id,$sub_id);
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Ошибка: Forbidden';
                }
                break;
            case '6'://Подтвердить заявку
                if($user->checkCourseAuth()){
                    $sub->confimRqst($send_id,$sub_id);
                    $returnOut['err']=FALSE;
                    $returnOut['err_txt']='';
                }else{
                    $returnOut['err']=TRUE;
                    $returnOut['err_txt']='Ошибка: Forbidden';
                }
                break;
            
                
        }
        
    }
    echo json_encode($returnOut);
?>