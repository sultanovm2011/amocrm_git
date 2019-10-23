<?php
/* Для начала нам необходимо инициализировать данные, необходимые для составления запроса. */
// чтение config.ini
$status_const = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini");
$servername = $status_const['servername'];
$database = $status_const['database'];
$username = $status_const['username'];
$password = $status_const['password'];
$subdomain = $status_const['subdomain'];
$status_id_first_lesson = $status_const['status_id_first_lesson'];
$status_id_is_paid = $status_const['status_id_is_paid'];
$status_const2 = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config_date_leads.ini");
$dt = $status_const2['date'];
$dd = date(DATE_RFC2822, $dt);
$dm = $dt;
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    $content = "LEADS mysql Connection failed: ".date('h:i:s') . ' Код: '. mysqli_connect_error() . "\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);
//    die("CUSTOMFIELDS_LEADS Connection failed: " . mysqli_connect_error());
    sleep(14400);
    require($_SERVER['DOCUMENT_ROOT'] . '/index.php');}
else {
//write to log.txt 'LEADS Connected successfully';
    $content = "LEADS mysql Connected successfully ".date('h:i:s') ."\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);}

$c = 500;
while ($c > 499) {
#Формируем ссылку для запроса
    $link = "https://$subdomain.amocrm.ru/api/v2/leads/?limit_rows=500";
    /* Заметим, что в ссылке можно передавать и другие параметры, которые влияют на выходной результат (смотрите
    документацию).
    Следовательно, мы можем заменить ссылку, приведённую выше на одну из следующих, либо скомбинировать параметры так, как Вам
    необходимо. */
//$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts/';
//$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts/?limit_rows=15';
//$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/contacts/?limit_rows=15&limit_offset=2';
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
        $content = "LEADS curl Connection failed: " . date('h:i:s') . ' error: ' . $code . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
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

//Обработка ответа
    $c = 0;
    foreach ($Response as $v) {
        if (is_array($v)) {
            $updated_at = $v['updated_at'];
            if($updated_at < $dm) continue;
            $id = $v['id'];
            $created_at = $v['created_at'];
            $closed_at = $v['closed_at'];
            $responsible_user_id = $v['responsible_user_id'];
            $main_contact_id = $v['main_contact']['id'];
            $contacts_ids = $v['contacts']['id'];
            $sale = $v['sale'];
            $is_deleted = $v['is_deleted'];
            $custom_fields = $v['custom_fields'];
            $status_id = $v['status_id'];
            foreach ($custom_fields as $i) {
                if (is_array($i)) {
                    $custom_fields_names = $i['name'];
                    $custom_fields_ids = $i['id'];
                    if ($custom_fields_names == 'Канал') {
                        $custom_fields_values = $i['values'];
                        foreach ($custom_fields_values as $k) {
                            if (is_array($k)) {
                                $chanel_value = $k['value'];
                                $chanel_enum = $k['enum'];
                            }
                        }
                    }

                    else {
                            $chanel_value = NULL;
                            $chanel_enum = NULL;
                        }
                    }
                }

                    if ($custom_fields_names == 'Источник') {
                        $custom_fields_values = $i['values'];
                        foreach ($custom_fields_values as $k) {
                            if (is_array($k)) {
                                $source_value = $k['value'];
                                $source_enum = $k['enum'];
                            }
                        }
                    }
                    else {
                        $source_value = NULL;
                        $source_enum = NULL;
                    }

                    if ($custom_fields_names == 'Причина отказа') {
                        $custom_fields_values = $i['values'];
                        foreach ($custom_fields_values as $k) {
                            if (is_array($k)) {
                                $reason_value = $k['value'];
                                $reason_enum = $k['enum'];
                            }
                        }
                    }
                    else {
                        $reason_value = NULL;
                        $reason_enum = NULL;
                    }

                    if ($custom_fields_names == 'Оплата') {
                        $custom_fields_values = $i['values'];
                        foreach ($custom_fields_values as $k) {
                            if (is_array($k)) {
                                $is_paid = $k['value'];
                                $is_paid_enum = $k['enum'];
                            }
                        }
                    }
                    else {
                        $is_paid = 0;
                        $is_paid_enum = NULL;
                    }

            if ($is_deleted == '') {
                $is_deleted = 0;
            }

            $contacts_ids_str = implode(",", $contacts_ids);
//            $contacts_ids_str = '111';

            //        проверка на существование id
            $sql = "SELECT status_id FROM LEADS WHERE id = $id";
            $res = mysqli_query($conn, $sql);

            if ($res->num_rows > 0) {
                $arr = mysqli_fetch_assoc($res);
                $old_status_id = $arr['status_id'];
//                $last_modified = $arr['updated_at'];

                if ($status_id == $status_id_first_lesson and $status_id != $old_status_id) {
                $first_lesson_date = $updated_at;
                    } else {
                        $first_lesson_date = NULL;
                    }

                if ($status_id == $status_id_is_paid and $status_id != $old_status_id) {
                    $is_paid_date = $updated_at;
                    } else {
                        $is_paid_date = NULL;
                    }

//                    Запись изменений в таблицу LEADS
                $sql1 = "UPDATE LEADS SET created_at='$created_at', closed_at='$closed_at', 
                 responsible_user_id='$responsible_user_id', main_contact_id='$main_contact_id', 
                 contacts_ids='$contacts_ids_str', sale='$sale', is_deleted='$is_deleted', 
                 chanel_value='$chanel_value', source_value='$source_value', reason_value='$reason_value', 
                 is_paid='$is_paid', chanel_enum = '$chanel_enum', source_enum = '$source_enum', 
                 reason_enum = '$reason_enum', status_id = '$status_id', first_lesson_date = '$first_lesson_date', 
                 is_paid_date = '$is_paid_date' WHERE id = $id";

//                , first_lesson_date = '$first_lesson_date', is_paid_date = '$is_paid_date'

                if (mysqli_query($conn, $sql1)) {
                    $content = "Запись изменений в таблицу LEADS удалась " . date('h:i:s') . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись изменений в таблицу LEADS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                }
            } else {
                if ($status_id == $status_id_first_lesson) {
                    $first_lesson_date = $updated_at;
                } else {
                    $first_lesson_date = NULL;
                }

                if ($status_id == $status_id_is_paid) {
                    $is_paid_date = $updated_at;
                } else {
                    $is_paid_date = NULL;
                }
//            sleep(0.1);
                $sql2 = "INSERT INTO LEADS (id, created_at, closed_at, responsible_user_id, 
                   main_contact_id, contacts_ids, sale, is_deleted, chanel_value, 
                   source_value, reason_value, is_paid, chanel_enum, source_enum, 
                   reason_enum, status_id, first_lesson_date, is_paid_date)
                    VALUES ('$id', '$created_at', '$closed_at', '$responsible_user_id',
                            '$main_contact_id', '$contacts_ids_str', '$sale', '$is_deleted',
                            '$chanel_value', '$source_value', '$reason_value', '$is_paid',
                            '$chanel_enum', '$source_enum', '$reason_enum', $status_id, 
                            '$first_lesson_date', '$is_paid_date')";
//                , '$first_lesson_date', '$is_paid_date' , first_lesson_date, is_paid_date

                if (mysqli_query($conn, $sql2)) {
                    $content = "Запись в таблицу LEADS удалась " . date('h:i:s') . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                } else {
                    $content = "Запись в таблицу LEADS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                    fwrite($fp, $content);
                    fclose($fp);
                }
            }
            ++$c;
        }
    }
$dt = $updated_at;
$dd = date(DATE_RFC2822, $dt);
if ($updated_at > $dm) {$dm = $updated_at;}
}
mysqli_close($conn);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/config_date_leads.ini", "date = $dm");





