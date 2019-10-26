<?php
# Array with parameters to be passed using the POST method to the system API
$user = array(
    'USER_LOGIN' => 'zigmund.online@gmail.com', #Ваш логин (электронная почта)
    'USER_HASH' => '9f1091da7b8bf222c189b6a76ca05472bfa28ade', #Хэш для доступа к API (смотрите в профиле пользователя)
//    'USER_LOGIN' => 'c928251@urhen.com', #Ваш логин (электронная почта)
//    'USER_HASH' => 'd3a702849e7c59baf9746bc68cd50eac37f27436', #Хэш для доступа к API (смотрите в профиле пользователя)

);
// read from .env
//require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$dotenv = Dotenv\Dotenv::create(__DIR__);
//$dotenv->load();
//$subdomain = getenv('SUBDOMAIN');
#Формируем ссылку для запроса
$link = "https://$subdomain.amocrm.ru/private/api/auth.php?type=json";
/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Вы также
можете
использовать и кроссплатформенную программу cURL, если вы не программируете на PHP. */
$curl = curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_COOKIEFILE, dirname
    (__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl, CURLOPT_COOKIEJAR, dirname
    (__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
curl_close($curl); #Завершаем сеанс cURL
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
        throw new Exception(isset($errors[$code]) ? $errors[$code] : `Undescribed error`, $code);
    }

} catch (Exception $E) {
    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
}
/*
Данные получаем в формате JSON, поэтому, для получения читаемых данных,
нам придётся перевести ответ в формат, понятный PHP
 */
$Response = json_decode($out, true);
$Response = $Response['response'];

//write to log.txt 'Авторизация удалась';
if (isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
{
    $content = "Авторизация удалась ".date('h:i:s') ."\n";
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
    fwrite($fp, $content);
    fclose($fp);
}
else {
//write to log.txt 'Авторизация не удалась';
$content = "Авторизация не удалась ".date('h:i:s') . ' Код: '. $code. "\n";
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
fwrite($fp, $content);
fclose($fp);}

