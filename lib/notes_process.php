<?php
class Notes{
    public $note_count,$page_html,$notes_html,$drop_search,$page_count;
    public function __construct(){
        $this->note_count=0;
        $this->page_count=0;
        $this->page_html='';
        $this->notes_html='';
        $this->drop_search='';
        $this->search='Поиск...';

    }
    public function get_notes($cur_url='blog.php',$user_search='',$author_id=0,$page=1,$user=NULL,$sub_id=0,$crs_id=0){
        $n_ids=[];//Массив для проверки уникальных пользователей у записи
        $limit='LIMIT '.($page*10-10).',10';
/*Поиск */
        if($user_search!==''){
            $where='';
                /*замена лишних пробелов и запятых*/
                $search_words=getSearchList($user_search);
                
                foreach($search_words as $word){
                    $where.=" n.`created` LIKE '%$word%' OR n.`title` LIKE '%$word%' OR n.`article` LIKE '%$word%' OR `tags` LIKE '%$word%' OR s.`name` LIKE '%$word%' OR";
                }
                $where = substr($where, 0, -2);
                $where='WHERE ((c.`private`=0 OR n.`course_id` IS NULL) OR (cs.`sub_id`='.$user->user_data['id'].' AND cs.`confirmed`=1)  OR c.`author_id`='.$user->user_data['id'].') AND '.$where;
                
                $this->drop_search="<br>Показаны результаты поиска на запрос: '$user_search'".'<br><br><a class="page_nums" href="'.$cur_url.'">Отменить поиск</a> ';
            }else{
                $this->drop_search='';
                $where='WHERE ((c.`private`=0 OR n.`course_id` IS NULL) OR (cs.`sub_id`='.$user->user_data['id'].' AND cs.`confirmed`=1) OR c.`author_id`='.$user->user_data['id'].')';
            }
            if($author_id!==0){
                $where.=" AND n.`author_id`=$author_id";
            }
            if($sub_id!==0){
                $where.=" AND ss.`sub_id`=$sub_id";
            }
            if($crs_id!==0){
                $where.=" AND c.`id`=$crs_id";
            }
            $q="SELECT DISTINCT n.`author_id`,s.`name`,s.`ava`,n.`id` as 'note_id',n.`views` ,n.`title`,n.`article`,DATE_FORMAT(n.`created`,'%e %M %Y')as 'frmtd_created', n.`tags`,
                c.`id` as 'c_id',c.`title` as 'c_title',c.`ava` as 'c_ava',c.`private`, c.`created`,c.`article` as 'c_article', 
                (SELECT count(*) FROM `course_subs` WHERE `course_id`=c.`id`) as 'subs_count',
                (SELECT count(*) FROM `notes` WHERE `course_id`=c.`id`) as 'notes_count'
                FROM `notes` as n 
                    INNER JOIN `s_a` as s on n.`author_id`=s.`id` 
                    LEFT JOIN `subs` as ss on n.`author_id`=ss.`author_id`
                    LEFT JOIN `courses` c on n.`course_id`=c.`id`
                    LEFT JOIN `course_subs` cs USING(`course_id`)
                $where 
                ORDER BY n.`created` DESC,n.`id` DESC 
                $limit
            ";
            
        /*Конец Поиск */

        /*Получение кол-ва заметок*/
            $this->note_count=queryMysql("SELECT count(DISTINCT n.`id`) as 'note_count' FROM `notes` as n INNER JOIN `s_a` as s on n.`author_id`=s.`id` LEFT JOIN `subs` as ss on n.`author_id`=ss.`author_id` LEFT JOIN `courses` c on n.`course_id`=c.`id` LEFT JOIN `course_subs` cs USING(`course_id`) $where")->fetch_assoc()['note_count'];
            $this->note_count=(int)$this->note_count;

            $this->page_html.='<a class="'.($page==1?'invis':'').' page_nums_rev" href="'.$cur_url.'?page='.($page-1).($author_id!==0?'&a_i='.$author_id:'').($sub_id!==0?'&cur_sub=1':'').'">Предыдущая</a> ';
            for($i=10;$i<$this->note_count+10;$i+=10){
                $this->page_html.=' <a class='.(($page*10)==$i?'"page_nums"':'"page_nums_rev"').' href="'.$cur_url.'?page='.($i/10).($author_id!==0?'&a_i='.$author_id:'').($sub_id!==0?'&cur_sub=1':'').'">'.($i/10).'</a>';
            }
            $this->page_html.='<a class="'.($i/10-1==$page || $this->note_count==0?'invis':'').' page_nums_rev" href="'.$cur_url.'?page='.($page+1).($author_id!==0?'&a_i='.$author_id:'').($sub_id!==0?'&cur_sub=1':'').'">Следующая</a> ';
            $this->page_count=$i/10;
        /*Конец навигации по страницам */


        /*Запрос к БД */
        queryMysql("SET lc_time_names ='ru_ru'");
            $result=queryMysql($q);


        if($result->num_rows==0){
            $this->drop_search.=' <br><br> Ничего не найдено. Попробуйте изменить запрос!';
        }else{
            $cntrl_block_html='';
            $old_c_title=NULL;//Доп переменая для хранения старого значения c_tilte
            for($i=0;$i<$result->num_rows;$i++){
                $rec=$result->fetch_assoc();
                $n_ids[]=$rec['note_id'];
            /*Кнопки управления в зависимости от доступа*/
                switch($user->access){
                    case 0:
                        $cmnt_btn='<p class="note_auth_name">Чтобы комментировать запись вам необходимо <a class="page_nums note_cntrl_btn" href="login.php">Войти</a> </p>';
                        break;
                    case 3:
                        $cntrl_block_html='<div class="flex_sb_r_ac"><a class="note_ed page_nums_rev" href="note.php?id='.$rec['note_id'].'&edit_type=1">Изменить запись</a> 
                            <a class="note_del rounded_red_rev" id="notedel_'.$rec['note_id'].'" href="0">Удалить запись</a></div>
                        ';
                        $cmnt_btn='<a class="page_nums note_cntrl_btn note_new_comment" id="notenewcom_'.$rec['note_id'].'" href="#">Комментировать запись</a>';
                        break;
                    default:
                        $cntrl_block_html=$cur_url=="profile.php"?'<div class="flex_sb_r_ac"><a class="note_ed page_nums_rev" href="note.php?id='.$rec['note_id'].'&edit_type=1">Изменить запись</a> 
                            <a class="note_del rounded_red_rev" id="notedel_'.$rec['note_id'].'" href="0">Удалить запись</a></div>
                        ':'';
                        
                        $cmnt_btn='<a class="page_nums note_cntrl_btn note_new_comment" id="notenewcom_'.$rec['note_id'].'" href="#">Комментировать запись</a>';
                        break;
                }
                //Вывод курса если есть
                if($old_c_title!==$rec['c_title'] ){
                    if($rec['c_title']!==NULL){
                        $this->notes_html.='
                        <div class="exposed page_block_wrap" id="course_'.$rec['c_id'].'">
                            <div class="flex_sb_r_ac">
                                <div class="ava_prof_block">
                                    <div class="ava_img">
                                        <img id="cimg_'.$rec['c_id'].'" src="'.$rec['c_ava'].'">
                                    </div>
                                </div> 
                                <div class="inner_cont_block">
                                    <div class="flex_sb_r_ac"><h2 class="note_title">Курс: '.$rec['c_title'].'</h2><p>'.$rec['created'].'</p></div>
                                        <div class="author_stats">'.($rec['private']==1?'&#128274; &#8226; ':'').$rec['subs_count'].' Учеников &#8226; '.$rec['notes_count'].' Записей</div>
                                        <p>'.$rec['c_article'].'</p>
                                </div>
                                
                            </div>
                            <hr>
                    ';
                    }else{
                        $this->notes_html.='</div>';
                    }
                    $old_c_title=$rec['c_title'];
                }
                
                $this->notes_html.= '<div class="note" id="note_'.$rec['note_id'].'">
                
                <div class="flex_sb_r_ac">
                    <h2 class="note_title" id="notetitle_'.$rec['note_id'].'">'.$rec['title'].'</h2>
                    <div class="flex_sb_r_ac">
                        <div class="mr_r_10">
                            <p class="note_auth_name">'.$rec['name'].'</p>
                            <p class="note_date italyc">' . $rec['frmtd_created']. '</p>
                        </div>
                        <a  href="blog.php?a_i='.$rec['author_id'].'">
                            <div class="ava_img cmnt_ava_img">
                                <img id="img_'.$rec['author_id'].'" src="'.$rec['ava'].'">
                            </div>
                        </a>
                    </div>
                </div>
                
                <p class="note_text">'.$rec['article'].'</p><br>
                <div class="flex_sb_r"><p class="italyc">Теги:'.$rec['tags'].'</p><p>&#128065; '.$rec['views'].'</p></div>
                <hr>
                '.$cntrl_block_html.'
                <div class="flex_sb_r_ac">
                    <a class="note_cmt note_cntrl_btn page_nums" id="notecom_'.$rec['note_id'].'" href="#">Открыть комментарии...</a>
                    '.$cmnt_btn.'
                </div>
                <div id="cmntblock_'.$rec['note_id'].'" class="cmnt_block"></div>
                    
                </div>';
            }
        }
        //Обновление кол-ва просмотренных записей
        $_COOKIE['vstd_ids']=isset($_COOKIE['vstd_ids'])?$_COOKIE['vstd_ids']:NULL;
        $u_n_ids=explode(',',$_COOKIE['vstd_ids']);
        $n_ids=array_diff($n_ids,$u_n_ids);
        if(count($n_ids)>0){
            $where='';
            foreach ($n_ids as $v) {
                $where.=" `id`=$v OR";
            }
            $where = substr($where, 0, -2);
            queryMysql("UPDATE `notes` SET `views`=`views`+1 WHERE $where");
            $user->updateCookie('vstd_ids',implode(',',array_merge($u_n_ids,$n_ids)),'/',$_SERVER['HTTP_HOST']);
        }
        return $this;
    }
}
?>