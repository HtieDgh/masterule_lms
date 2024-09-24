<?php
/*
Файл сожержит класс реализующий возможность подписываться, отображать подписки и авторов контента
*/
class Subscriber{
    public $user_id,$authors=array();
    public $auth_count=0;
    public $page_count=0;
    public $drop_search,$page_html,$crs_count=0;
    public function __construct($user_id){
        $this->user_id=$user_id;
        $this->drop_search='';
        $this->page_html='';
    }
    /*
        ===================================
        ---Стать подписчиком---
        Вход: id автора или одномерный массив из id авторов
        Выход: true если ок
        ===================================
    */
    public function subscribe($author_id='')
    {
        $val='';
        if(preg_match("/[^0-9]/",$author_id)!=1){
            if($this->user_id!=$author_id){
                    $q="SELECT `sub_id` FROM `subs` WHERE `sub_id`=".$this->user_id." AND `author_id`=$author_id";
                    $result=queryMysql($q);
                if($result->num_rows==0){
                    $val.="(".$this->user_id.", $author_id)";
                    $q="INSERT INTO `subs`(`sub_id`,`author_id`)VALUES $val";
                    $result=queryMysql($q);    
                    return TRUE;
                }else{
                    return FALSE;    
                }
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    /*
        ===================================
        ---Отписка---
        Вход: id автора
        Выход: true если ок
        ===================================
    */
public function unscribe($author_id='')
{
    if(preg_match("/[^0-9]/",$author_id)!=1){
        $q="DELETE FROM `subs` WHERE `sub_id`=".$this->user_id." AND `author_id`=$author_id";
        $result=queryMysql($q);
        return TRUE;
    }else{
        return FALSE;
    }
}

    /*
        =================================================
        --------Отображение и Поиск среди авторов----------
        Вход: обычный Массив с фразами для поиска, страница результата, 
            режим(0 - все авторы; 1 - только текущие подписки; 2 - только одного автора)
            id автора для режима 2
        Выход: двумерный массив содержащий авторов контента
        ================================================
    */
    public function getAuthorsList($search_words="",$page=0,$mode=0,$a_id=0){
        $cur_url='profile.php';
        $limit=$page!==0?'LIMIT '.($page*10-10).',10':'';
        $where='';
        $search=getSearchList($search_words);
        if($search[0]!=""){
            foreach($search as $word){
                $where.=" `name` LIKE '%$word%' OR `status` LIKE '%$word%' OR";
            }
            $where = substr($where, 0, -2);
            $where=" AND($where)";
        }
        $where="WHERE `access`>1 $where";
        switch ($mode) {
            case 1://Инфа о Всех подписках
                $where.=" AND `sub_id`=".$this->user_id." AND `author_id`=`s_a`.`id`";
                $q="SELECT `id`,`name`,`status`,`ava`
                    ,(SELECT count(*) FROM subs WHERE `author_id`=`s_a`.`id`) as 'subs_count' 
                    ,(SELECT count(*) FROM `notes` WHERE `author_id`=`s_a`.`id`) as 'notes_count' 
                    ,(SELECT 1 FROM `subs` WHERE`sub_id`=".$this->user_id." AND `author_id`=`s_a`.`id`) as 'is_subbed'
                    FROM `s_a` INNER join `subs` on `author_id`=`id`
                    $where
                    ORDER BY `subs_count` DESC $limit
                ";
                break;
            case 2://Информация об 1 авторе
                $q="SELECT `id`,`name`,`status`,`ava`
                    ,(SELECT count(*) FROM subs WHERE `author_id`=`s_a`.`id`) as 'subs_count' 
                    ,(SELECT count(*) FROM `notes` WHERE `author_id`=`s_a`.`id`) as 'notes_count'
                    ,(SELECT (CASE WHEN (".$this->user_id." IN (
                                                    SELECT `sub_id` FROM `subs` WHERE `s_a`.`id`=`subs`.`author_id`
                                                    )
                                        ) THEN 1 
                                WHEN `s_a`.`id`=".$this->user_id." THEN 2 
                                ELSE 0 END) as 'ext' 
                        FROM `subs` GROUP BY 'ext'
                    ) as 'is_subbed'
                    FROM `s_a` WHERE `s_a`.`id`=$a_id
                ";
                break;
            default://все авторы
                $q="SELECT `id`,`name`,`status`,`ava`
                    ,(SELECT count(*) FROM subs WHERE `author_id`=`s_a`.`id`) as 'subs_count' 
                    ,(SELECT count(*) FROM `notes` WHERE `author_id`=`s_a`.`id`) as 'notes_count'
                    ,(SELECT (CASE WHEN (".$this->user_id." IN (
                                                    SELECT `sub_id` FROM `subs` WHERE `s_a`.`id`=`subs`.`author_id`
                                                    )
                                        ) THEN 1 
                                WHEN `s_a`.`id`=".$this->user_id." THEN 2 
                                ELSE 0 END) as 'ext' 
                        FROM `subs` GROUP BY 'ext'
                    ) as 'is_subbed'
                    FROM `s_a` $where
                    ORDER BY `subs_count` DESC 
                    $limit
                ";
                break;
        }
        
       
        $result=queryMysql($q);
        
        $this->authors=$result->fetch_all(MYSQLI_ASSOC);
        //кол-во авторов
        $this->auth_count=$result->num_rows;
    //Получение страниц
        $this->page_html.='<a class="'.($page==1?'invis':'').' page_nums_rev" href="'.$cur_url.'?page='.($page-1).'">Предыдущая</a> ';
        for($i=10;$i<$this->auth_count+10;$i+=10){
            $this->page_html.=' <a class='.(($page*10)==$i?'"page_nums"':'"page_nums_rev"').' href="'.$cur_url.'?page='.($i/10).'">'.($i/10).'</a>';
        }
        $this->page_html.='<a class="'.($i/10-1==$page || $this->auth_count==0?'invis':'').' page_nums_rev" href="'.$cur_url.'?page='.($page+1).'">Следующая</a> ';
        $this->page_count=$i/10;

        return $this->authors;
    }
    /*
     =====================
        Курсы
     =====================
     */
    
    /**
     * @param user_id int
     * @param search string
     * @param page int
     * @param mode bool курсы в профиле
     * @return array
     */
    public function getCourseList($user_id=0,$search_words="",$page=0,$mode=false)
    {
        $requst=$mode?"(SELECT count(*) FROM `course_subs` WHERE `course_id`=c.`id` AND `confirmed`=0) as 'rqst_count',":'';
        $where='';
        $search=getSearchList($search_words);
        $limit=$page!==0?'LIMIT '.($page*10-10).',10':'';
        if($search[0]!=""){
            foreach($search as $word){
                $where.=" `title` LIKE '%$word%' OR `article` LIKE '%$word%' OR";
            }
            $where = substr($where, 0, -2);
            $where="WHERE `author_id`=$user_id AND($where)";
        }
        
        $result=queryMysql("SELECT c.`id`,c.`title`,c.`article`,c.`ava`,c.`created`,c.`private`,
            (SELECT count(*) FROM `course_subs` WHERE `course_id`=c.`id` AND `confirmed`=1) as 'subs_count',
            $requst
            (SELECT count(*) FROM `notes` WHERE `course_id`=c.`id`) as 'notes_count',
            (SELECT (CASE WHEN (".$this->user_id." IN (
                                                    SELECT `sub_id` FROM `course_subs` cs WHERE c.`id`=cs.`course_id` AND cs.`confirmed`=1
                                                    )
                                        ) THEN 1
                            WHEN (".$this->user_id." IN (
                                        SELECT `sub_id` FROM `course_subs` cs WHERE c.`id`=cs.`course_id` AND cs.`confirmed`=0
                                        )
                                ) THEN 2
                            WHEN c.`author_id`=".$this->user_id." THEN 3
                            ELSE 0 END) as 'ext' 
                    FROM `subs` GROUP BY 'ext'
                ) as 'is_subbed'
            FROM `courses` c 
            $where 
            ORDER BY `created` DESC $limit"
        );
        
        $this->crs_count=$result->num_rows;
        $this->courses=$result->fetch_all(MYSQLI_ASSOC);
        
        return $this->courses;
        
    }
    /**
     * Получить список заявок на курс
     */
    public function getRqstList($course_id=0,$user_id=0)
    {
        $result=queryMysql("SELECT ss.`name`,ss.`id` as 'user_id' ,ss.`ava`,cs.`course_id`
            FROM `s_a` ss 
            INNER JOIN `course_subs` cs ON ss.`id`=cs.`sub_id`
            INNER JOIN `courses` c ON cs.`course_id`=c.`id`
            WHERE c.`author_id`=$user_id AND cs.`course_id`=$course_id AND cs.`confirmed`=0
            ORDER BY ss.`name` DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * Подтвердить подписку на курс
     */
    public function confimRqst($course_id=0,$user_id=0)
    {
        return queryMysql("UPDATE `course_subs` SET `confirmed`=1 WHERE `course_id`=$course_id AND `sub_id`=$user_id");
    }
    /**
     * Подача заявки или подписка
     * @param course_id int
     * @param user_id int
     * @return int
     */
    public function subOnCourse($course_id=0,$user_id=0)
    {
        //$err_code 0 - Заявка подана, 1 - подисан, 2 - ошибка заявка была подана ранее;
        $user_id=$user_id==0?$this->user_id:$user_id;
        $res=queryMysql("SELECT `confirmed` FROM `course_subs` WHERE `course_id`=$course_id AND `sub_id`=$user_id");
        if($res->num_rows==0){
            $res=queryMysql("SELECT `private` FROM `courses` WHERE `id`=$course_id")->fetch_assoc();
            queryMysql("INSERT INTO `course_subs`(`course_id`,`sub_id`,`confirmed`)VALUES($course_id,$user_id,".((int)!$res['private']).")");
            return (int)!$res['private'];
        }else{
            return 2;
        }
        
    }
    public function unsubOnCourse($course_id=0,$user_id=0)
    {
        $user_id=$user_id==0?$this->user_id:$user_id;
        $res=queryMysql("SELECT `confirmed` FROM `course_subs` WHERE `course_id`=$course_id AND `sub_id`=$user_id");
        if($res->num_rows!=0){
            queryMysql("DELETE FROM `course_subs` WHERE `course_id`=$course_id AND `sub_id`=$user_id");
            return 0;
        }else{
            return 2;
        }
    }
     /**
     * @param id
     */
    public static function Delete(int $course_id)
    {
        return queryMysql("DELETE FROM `courses` WHERE `course_id`=$course_id");
    }
}


?>