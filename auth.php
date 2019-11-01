<?php
# Array with parameters to be passed using the POST method to the system API
//$user = array(
//    'USER_LOGIN' => 'zigmund.online@gmail.com', #Your login(email)
//    'USER_HASH' => '9f1091da7b8bf222c189b6a76ca05472bfa28ade', #Hash for accessing the API (see user profile)
//);
//require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$dotenv = Dotenv\Dotenv::create(__DIR__);
//$dotenv->load();
$USER_LOGIN = getenv('USER_LOGIN');
$USER_HASH = getenv('USER_HASH');
$user = array(
    'USER_LOGIN' => $USER_LOGIN, #Your login(email)
    'USER_HASH' => $USER_HASH, #Hash for accessing the API (see user profile)
);
// read from .env
//require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$dotenv = Dotenv\Dotenv::create(__DIR__);
//$dotenv->load();
//$subdomain = getenv('SUBDOMAIN');
#We form a link for the request
$link = "https://$subdomain.amocrm.ru/private/api/auth.php?type=json";
// initiate server request
$curl = curl_init(); #Save the cURL session descriptor
#Set the necessary options for the cURL session
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
$out = curl_exec($curl); #We initiate an API request and save the response in a variable
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Get the HTTP response code of the server
curl_close($curl);
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
    if ($code != 200 && $code != 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : `Undescribed error`, $code);
    }

} catch (Exception $E) {
    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
}

$Response = json_decode($out, true);
$Response = $Response['response'];

//write to log.txt 'Авторизация удалась';
if (isset($Response['auth']))
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

