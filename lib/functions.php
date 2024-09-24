<?php
    $dbhost = 'localhost'; // Эта строка вряд ли нуждается в изменении
    $dbname = 'trzbd_practicum'; // А значения этих переменных
    $dbuser = 'admin'; // поменяйте на те, что соответствуют
    $dbpass = '*******'; // вашим настройкам
$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error) die($connection->connect_error);

const SITE_DOMAIN='http://domain.com/masterula';

function queryMysql($query)
{
    global $connection;
    $result = $connection->query($query);
    if (!$result) die($connection->error.'<br>Query:<br>'. $query);
    return $result;
}

function destroySession()
{
    $_SESSION=array();
    if (session_id() != "" || isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time()-2592000, '/');
    session_destroy();
}

function sanitizeString($var)
{
    global $connection;
    $var =trim($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
}

function displayTable($result,$c=''){
    if($c!==''){
        switch($c[0]){
            case '.':
                $c=substr($c,1);
                $c="class='$c'";
                break;
            case '#':
            $c=substr($c,1);
                $c="id='$c'";
                break;
            default:
                $c="style='$c'";
                break;
        }
    }
    $html_txt='<div  '.$c.'>';

    for($i=0;$i<$result->num_rows;$i++ ){
        $rec=$result->fetch_array(MYSQLI_NUM);
        $html_txt.='<hr><br><p>';
         for($j=0;$j<count($rec);$j++){
            $html_txt.=' '.$rec[$j];
         }
         $html_txt.='</p><a class="page_nums_rev" href="edituser.php?edit_type=1&user_id='.$rec[0].'">Изменить данные пользователя</a><a class="rounded_red_rev" href="deluser.php?user_id='.$rec[0].'">Удалить пользователя</a><br><br>';
       
    }
    $html_txt.='</div>';
    return  $html_txt;
}
/**модуль загрузка фото и файлов */
function reArrayFiles($file)
{
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);
   
    for($i=0;$i<$file_count;$i++)
    {
        foreach($file_key as $val)
        {
            $file_ary[$i][$val] = $file[$val][$i];
        }
    }
    return $file_ary;
}
/**модуль отбр записей и сетки расп */
function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}
//Возврат массива с запросами на поиск
function getSearchList($user_search=''){
    $search=sanitizeString($user_search);
    $search= preg_replace("/-{2,}/"," ",$search);
    $search= preg_replace("/\s{2,}/"," ",$search);
    
    $search=preg_replace("/, /",' ',$search);
    return explode(' ',$search);
}

/*
    ==============================
    ----Функция подготовленого запроса - php > 5.0---
    Входные параметры: запрос, параметры array("bind_patern",переменые..), строка, которая вернется в случае удачного выполнения
    Выходные параметры: html разметка ошибки или переданная строка, которая вернется в случае удачного выполнения
    ==============================
*/
function preparedQuery($q,$params,$out=""){
    global $connection;
    $stmt =  $connection->prepare($q);
    try {
        if (!$stmt) {
            throw new Exception('<p><br>Произошла ошибка, свяжитесь с адмиснистратором! <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>
            '.$q.'<br>Не удалось подготовить запрос: ('.$connection->errno.') '.$connection->error);
        }
        if (!call_user_func_array(array($stmt, 'bind_param'), refValues($params))) {
            throw new Exception('<p><br>Произошла ошибка, свяжитесь с адмиснистратором! <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>
            '.$q.'<br>Не удалось привязать параметры: (' . $stmt->errno . ") " . $stmt->error);
        }
        if (!$stmt->execute()) {
            throw new Exception('<p><br>Произошла ошибка, свяжитесь с адмиснистратором! <a class="page_nums" href="profile.php">Назад к профилю</a><a class="page_nums" href="blog.php">На главную</a></p>
            '.$q.'<br>Не удалось выполнить запрос: (' . $stmt->errno . ") " . $stmt->error);
        }
        
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    $stmt->close();
    return $out;
}

?>