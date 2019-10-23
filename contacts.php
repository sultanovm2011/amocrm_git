<?php
/* Для начала нам необходимо инициализировать данные, необходимые для составления запроса. */
// чтение config.ini
$status_const = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini");
$servername = $status_const['servername'];
$database = $status_const['database'];
$username = $status_const['username'];
$password = $status_const['password'];
$subdomain = $status_const['subdomain'];
$status_const2 = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config_date_contacts.ini");
$dt = $status_const2['date'];
$dd = date(DATE_RFC2822, $dt);
$dm = $dt;
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    $content = "CONTACTS mysql Connection failed: ".date('h:i:s') . ' Код: '. mysqli_connect_error() . "\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);
//    die("CUSTOMFIELDS_LEADS Connection failed: " . mysqli_connect_error());
    sleep(14400);
    require($_SERVER['DOCUMENT_ROOT'] . '/index.php');}
else {
//write to log.txt 'BUYERS Connected successfully';
    $content = "CONTACTS mysql Connected successfully ".date('h:i:s') ."\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);}
//цикл для снятия ограничения в 500 строк
$c = 500;
while ($c > 499) {
#Формируем ссылку для запроса
    $link = "https://$subdomain.amocrm.ru/api/v2/contacts/?limit_rows=500";
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
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('IF-MODIFIED-SINCE: '.$dd));
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
        $content = "CONTACTS curl Connection failed: ".date('h:i:s') . ' error: '. $code . "\n";
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
            if($updated_at < $dm) continue;
            $psyholog_flag = 0;
            $id = $v['id'];
            $name = $v['name'] . PHP_EOL;
            $responsible_user_id = $v['responsible_user_id'];
            $custom_fields = $v['custom_fields'];
            foreach ($custom_fields as $i) {
                if (is_array($i)) {
                    $cust_name = $i['name'];
                    if ($cust_name == 'Психолог') {
                        $psyholog_flag_v = $i['values'];
                    }
//                    if ($cust_name == 'is_deleted') {
//                        $is_deleted_flag_v = $i['values'];
//                    }
                }
            }
//            foreach ($is_deleted_flag_v as $i) {
//                if (is_array($i)) {
//                    $is_deleted_flag = $i['value'];
//                }
//            }
            foreach ($psyholog_flag_v as $i) {
                if (is_array($i)) {
                    $psyholog_flag = $i['value'];
                }
            }
            if ($psyholog_flag == '') {
                $psyholog_flag = 0;
            }
//            if ($is_deleted_flag == '') {
//                $is_deleted_flag = 0;
//            }
//        проверка на существование id
            $sql = "SELECT name FROM CONTACTS WHERE id = $id";
            $res = mysqli_query($conn, $sql);
            if ($res->num_rows > 0) {
//                    Запись изменений в таблицу CONTACTS
                $sql1 = "UPDATE CONTACTS SET responsible_user_id='$responsible_user_id', name='$name',
                    psyholog= '$psyholog_flag'
                    WHERE id = $id";
                if (mysqli_query($conn, $sql1)) {
                    $content = "Запись изменений в таблицу CONTACTS удалась " . date('h:i:s') . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись изменений в таблицу CONTACTS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                }
            } else {
//        Запись новых данных в таблицу CONTACTS
                $sql2 = "INSERT INTO CONTACTS (id, responsible_user_id, name, psyholog)
                    VALUES ('$id', '$responsible_user_id', '$name', '$psyholog_flag')";

                if (mysqli_query($conn, $sql2)) {
                    $content = "Запись в таблицу CONTACTS удалась " . date('h:i:s') . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись в таблицу CONTACTS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
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
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/config_date_contacts.ini", "date = $dm");
}
mysqli_close($conn);




