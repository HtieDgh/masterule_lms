<?php
class Stats{
    private $stat_html,$user_list_html;
    function __construct(){

    }
    /**
     * @param user_ids array
     * @param mode int
     */
    public function getStat($user_ids,$mode=0){
    /*========Сбор Статистики========= */
        /*Получение кол-ва заметок*/
        $where='';
        foreach($user_ids as $u_id){
            $where.="n.`author_id`=$u_id OR";
        }
        $where = substr($where, 0, -2);
        $result=queryMysql("SELECT count(*) as 'note_count' FROM `notes` n WHERE $where");
        $note_count=$result->fetch_assoc()['note_count'];

        /*Получение кол-ва коментариев*/
        $result=queryMysql("SELECT count(*) as 'com_count' FROM `comments` c INNER JOIN `notes` n ON c.`note_id`=n.`id` WHERE $where");
        $com_count=$result->fetch_assoc()['com_count'];

        /*Получение кол-ва заметок за последний месяц*/
        $cur_date=date("Y-m-d");
        $Date = new DateTime($cur_date);
        $shift = -1;
        //  сохраним день
        $day = $Date->format('d');
        // первый день целевого месяца  
        $Date->modify('first day of this month')->modify(($shift > 0 ? '+':'') . $shift . ' months');
        // если наш день больше числа дней в месяце, возьмем последний
        $day = $day > $Date->format('t') ? $Date->format('t') : $day;
        $start_date=$Date->modify('+' . $day-1 . ' days')->format('Y-m-d');

        $q="SELECT count(*) as 'note_count_lm' FROM `notes` n
        WHERE n.`created` BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d') 
        AND STR_TO_DATE('$cur_date', '%Y-%m-%d') AND ($where)";
        $result=queryMysql($q);
        $note_count_lm=$result->fetch_assoc()['note_count_lm'];

        /*Получение кол-ва комментов за последний месяц*/
        $q="SELECT count(*) as 'com_count_lm' FROM `comments` c
        INNER JOIN `notes` n ON c.`note_id`=n.`id`
        WHERE n.`created` BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d') 
        AND STR_TO_DATE('$cur_date', '%Y-%m-%d') AND($where)";

        $result=queryMysql($q);
        $com_count_lm=$result->fetch_assoc()['com_count_lm'];

        /*Получение последней добавленой заметки */
        $q="SELECT n.`title` from `notes` n WHERE $where order by `created` DESC,`id` DESC LIMIT 0,1 ";
        $result=queryMysql($q);
        $note_last=$result->num_rows?$result->fetch_assoc()['title']:'';

        /*Получение обсуждаемой заметки */
        $q="SELECT n.`title` FROM `comments` c
        INNER JOIN `notes` n on c.`note_id`=n.`id`
        WHERE $where
        ORDER BY COUNT(c.`note_id`) DESC LIMIT 0,1";
        $result=queryMysql($q);
        $note_mc=$result->num_rows?$result->fetch_assoc()['title']:'';

        /*Получение общего кол-ва просмотров */
        $q="SELECT SUM(`views`) as 'sum' FROM `notes` n WHERE $where";
        $note_sv=queryMysql($q)->fetch_assoc()['sum'];

        /*Получение кол-ва просмотров за последний месяц */
        $q="SELECT SUM(`views`) as 'sum' FROM `notes` n
         WHERE n.`created` BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d') 
         AND STR_TO_DATE('$cur_date', '%Y-%m-%d') AND($where)";
        $note_sv_lm=queryMysql($q)->fetch_assoc()['sum'];

        /*Самая просматриваемая заметка */
        $q="SELECT n.`title` FROM `notes` n
        WHERE $where
        ORDER BY n.`views` DESC LIMIT 0,1";
        $result=queryMysql($q);
        $note_mv=$result->num_rows?$result->fetch_assoc()['title']:'';

        /*Самая просматриваемая заметка за последний месяц*/
        $q="SELECT n.`title` FROM `notes` n
        WHERE n.`created` BETWEEN STR_TO_DATE('$start_date', '%Y-%m-%d') 
         AND STR_TO_DATE('$cur_date', '%Y-%m-%d') AND($where)
        ORDER BY n.`views` DESC, n.`id` DESC LIMIT 0,1";
        $result=queryMysql($q);
        $note_mv_lm=$result->num_rows?$result->fetch_assoc()['title']:'';

    //Вывод данных
        $this->stat_html='<p><span class="good_txt italyc">Статистика:</span><br><br>';
        //кол-во пользователей и авторов
        if($mode===1){
            $user_author_count=$user_count='err';
            $q="SELECT count(*) as 'u_c' FROM `s_a`";
            $result=queryMysql($q);
            $user_count=$result->fetch_assoc()['u_c'];

            $q="SELECT count(*) as 'u_a_c' FROM `s_a` WHERE `access`>1";
            $result=queryMysql($q);
            $user_author_count=$result->fetch_assoc()['u_a_c'];
            $this->stat_html.="Кол-во пользователей: $user_count<br><br>Кол-во авторов контента:  $user_author_count<br><br>";
        }
        

        $this->stat_html.='
        Сделано записей: '.$note_count.'<br><br>
        Оставлено коментариев:'.$com_count.'<br><br>
        За последний месяц создано записей: '.$note_count_lm.'<br><br>
        Кол-во комментариев за последний месяц: '.$com_count_lm.'<br><br>
        Последняя запись: <span class="italyc">'.$note_last.'</span><br><br>
        Самая обсуждаемая запись: <span class="italyc">'.$note_mc.'</span><br><br>
        Кол-во просмотров: '.$note_sv.'<br><br>
        Кол-во просмотров за последний месяц: '.$note_sv_lm.'<br><br>
        Самая просматриваемая запись: '.$note_mv.'<br><br>
        Самая просматриваемая запись за последний месяц: '.$note_mv_lm.'
        </p>';
        /*========Конец Сбор Статистики========= */     
        return $this->stat_html;
    }
    public function getUserList(){
        $this->user_list_html='
                <p class="good_txt italyc">Управление пользователями:</p><br>
                <a class="user_add page_nums" href="edituser.php">Добавить пользователя</a><br><br>
                <p>Все пользователи:</p><br>id имя доступ<br>
            ';
        $this->user_list_html.=displayTable(
            queryMysql("SELECT `id`,`name`,`access` FROM `s_a`")
            );

        return $this->user_list_html;
    }
}
?>