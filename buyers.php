<?php
/* Для начала нам необходимо инициализировать данные, необходимые для составления запроса. */
// чтение config.ini
$status_const = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini");
$servername = $status_const['servername'];
$database = $status_const['database'];
$username = $status_const['username'];
$password = $status_const['password'];
$subdomain = $status_const['subdomain'];
$status_const2 = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config_date_buyers.ini");
$dt = $status_const2['date'];
$dm = $dt;
$dd = date(DATE_RFC2822, $dt);
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    $content = "BUYERS mysql Connection failed: ".date('h:i:s') . ' Код: '. mysqli_connect_error() . "\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);
//    die("BUYERS mysql Connection failed: " . mysqli_connect_error());
    sleep(14400);
    require($_SERVER['DOCUMENT_ROOT'] . '/index.php');}
else {
//write to log.txt 'BUYERS Connected successfully';
    $content = "BUYERS mysql Connected successfully ".date('h:i:s') ."\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);}
//цикл для снятия ограничения в 500 строк
$c = 500;
while ($c > 499) {
    $link = "https://$subdomain.amocrm.ru/api/v2/customers/?limit_rows=500";
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
//    curl_setopt($curl, CURLOPT_HTTPHEADER, array('IF-MODIFIED-SINCE: '.$dd));
    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    /* Вы также можете передать дополнительный HTTP-заголовок IF-MODIFIED-SINCE, в котором указывается дата в формате D, d M Y
    H:i:s. При
    передаче этого заголовка будут возвращены контакты, изменённые позже этой даты. */
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;
    $errors = array(
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    );
    try {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        $content = "BUYERS curl Connection failed: ".date('h:i:s') . ' error: '. $code . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
        fwrite($fp, $content);
        fclose($fp);
        sleep(14400);
        require($_SERVER['DOCUMENT_ROOT'] . '/index.php');
//    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }

    /*
    Данные получаем в формате JSON, поэтому, для получения читаемых данных,
    нам придётся перевести ответ в формат, понятный PHP
     */
    $Response = json_decode($out, true);
    $Response = $Response['_embedded']['items'];
    $c = 0;
//Обработка ответа
    foreach ($Response as $v) {
        if (is_array($v)) {
            $updated_at = $v['updated_at'];
            if($updated_at < (time()-88000)) continue;
            $id = $v['id'];
            $is_deleted = $v['is_deleted'];
            $period_id = $v['period_id'];
            $created_at = $v['created_at'];
            $responsible_user_id = $v['responsible_user_id'];
            $next_date = $v['next_date'];
            $status_id = $v['status_id'];
            $ltv = $v['ltv'];
            $purchases_count = $v['purchases_count'];
            $average_check = $v['average_check'];
            $main_contact_id = $v['main_contact']['id'];
            $contacts_ids = $v['contacts']['id'];
            if ($is_deleted == '') {
                $is_deleted = 0;
            }
            $contacts_ids_str = implode(",", $contacts_ids);
//            проверка на существование id
            $sql = "SELECT responsible_user_id FROM BUYERS WHERE id = $id";
            $res = mysqli_query($conn, $sql);
            if ($res->num_rows > 0) {
    //                    Запись изменений в таблицу BUYERS
                $sql1 = "UPDATE BUYERS SET is_deleted='$is_deleted', period_id='$period_id', created_at='$created_at', responsible_user_id='$responsible_user_id', next_date='$next_date', status_id='$status_id', ltv='$ltv', purchases_count='$purchases_count', average_check='$average_check', main_contact_id='$main_contact_id', contacts_ids='$contacts_ids_str'  WHERE id = $id";

                if (mysqli_query($conn, $sql1)) {
                    $content = "Запись изменений в таблицу BUYERS удалась ".date('h:i:s') ."\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись изменений в таблицу BUYERS не удалась ".date('h:i:s') . ' Код: '. mysqli_error($conn) . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
                    fwrite($fp, $content);
                    fclose($fp);
                }
            } else {
//        Запись в таблицу BUYERS
                $sql2 = "INSERT INTO BUYERS (id, is_deleted, period_id, created_at, responsible_user_id, next_date, status_id, ltv, purchases_count, average_check, main_contact_id, contacts_ids)
                    VALUES ('$id', '$is_deleted', '$period_id', '$created_at', '$responsible_user_id', '$next_date', '$status_id', '$ltv', '$purchases_count', '$average_check', '$main_contact_id', '$contacts_ids_str')";

                if (mysqli_query($conn, $sql2)) {
                    $content = "Запись в таблицу BUYERS удалась " . date('h:i:s') . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись в таблицу BUYERS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                }
            }
            ++$c;
            if ($updated_at > $dt) {$dt = $updated_at;}
            if ($updated_at > $dm) {$dm = $updated_at;}
        }
    }
    $dd = date(DATE_RFC2822, $dt);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/config_date_buyers.ini", "date = $dm");
}
mysqli_close($conn);



