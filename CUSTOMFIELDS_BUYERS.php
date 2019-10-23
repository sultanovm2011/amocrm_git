<?php
/* Для начала нам необходимо инициализировать данные, необходимые для составления запроса. */
// чтение config.ini
$status_const = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini");
$servername = $status_const['servername'];
$database = $status_const['database'];
$username = $status_const['username'];
$password = $status_const['password'];
$subdomain = $status_const['subdomain'];#Формируем ссылку для запроса
$status_const2 = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config_date_cust_buyers.ini");
$dt = $status_const2['date'];
$dd = date(DATE_RFC2822, $dt);
$dm = $dt;
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    $content = "CUSTOMFIELDS_BUYERS mysql Connection failed: ".date('h:i:s') . ' Код: '. mysqli_connect_error() . "\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);
//    die("CUSTOMFIELDS_LEADS Connection failed: " . mysqli_connect_error());
    sleep(14400);
    require($_SERVER['DOCUMENT_ROOT'] . '/index.php');
}
else {
//write to log.txt 'CUSTOMFIELDS_LEADS Connected successfully';
    $content = "CUSTOMFIELDS_BUYERS mysql Connected successfully ".date('h:i:s') ."\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);}
//цикл для снятия ограничения в 500 строк
$c = 500;
while ($c > 499) {
$link = "https://$subdomain.amocrm.ru/api/v2/customers/?limit_rows=500";
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
$code = (int) $code;
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
try
{
    #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
    if ($code != 200 && $code != 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : ' error', $code);
    }
} catch (Exception $E) {
    $content = "CUSTOMFIELDS_BUYERS curl Connection failed: ".date('h:i:s') . ' error: '. $code . "\n";
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
        $custom_fields = $v['custom_fields'];
        foreach ($custom_fields as $i) {
            if (is_array($i)) {
                $custom_fields_names = $i['name'];
                $custom_fields_ids = $i['id'];
                //        проверка на существование id
                $sql = "SELECT name FROM CUSTOMFIELDS_BUYERS WHERE id = $custom_fields_ids";
                $res = mysqli_query($conn, $sql);
                if ($res->num_rows > 0) {
//                    Запись изменений в таблицу CUSTOMFIELDS_LEADS
                    $sql1 = "UPDATE CUSTOMFIELDS_BUYERS SET name='$custom_fields_names' WHERE id = $custom_fields_ids";

                    if (mysqli_query($conn, $sql1)) {
                        $content = "Запись изменений в таблицу CUSTOMFIELDS_BUYERS удалась " . date('h:i:s') . "\n";
                        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                        fwrite($fp, $content);
                        fclose($fp);
                    } else {
                        $content = "Запись изменений в таблицу CUSTOMFIELDS_BUYERS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                        fwrite($fp, $content);
                        fclose($fp);
                    }
                }   else {
////            sleep(0.1);
                    $sql2 = "INSERT INTO CUSTOMFIELDS_BUYERS (id, name)
                    VALUES ('$custom_fields_ids', '$custom_fields_names')";
                    if (mysqli_query($conn, $sql2)) {
                        $content = "Запись в таблицу CUSTOMFIELDS_BUYERS удалась " . date('h:i:s') . "\n";
                        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                        fwrite($fp, $content);
                        fclose($fp);
                    } else {
                        $content = "Запись в таблицу CUSTOMFIELDS_BUYERS не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
                        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
                        fwrite($fp, $content);
                        fclose($fp);
                    }
                }
            }
        }
    }
    ++$c;
    if ($updated_at > $dt) {$dt = $updated_at;}
    if ($updated_at > $dm) {$dm = $updated_at;}
}
    $dd = date(DATE_RFC2822, $dt);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/config_date_cust_buyers.ini", "date = $dm");
}
mysqli_close($conn);






