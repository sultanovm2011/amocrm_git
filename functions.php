<?php

//function changeEnvironmentVariable($k,$uniqid)
//{
//    require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//    $dotenv = Dotenv\Dotenv::create(__DIR__);
//    $dotenv->load();
//    $old = getenv($k);
//    $path = '.env';
//    file_put_contents($path, str_replace(
//        "$k=".$old, "$k=".$uniqid, file_get_contents($path)
//    ));
//}

//write to DB
function write_DB($con, $sq, $name1)
{
    if (mysqli_query($con, $sq)) {
        $content = "Запись в таблицу $name1 удалась " . date('h:i:s') . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
        fwrite($fp, $content);
        fclose($fp);
    } else {
        $content = "Запись в таблицу $name1 не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
        fwrite($fp, $content);
        fclose($fp);
    }
}
//write changes to DB
function write_DB_changes($con, $sq, $name1)
{
    if (mysqli_query($con, $sq)) {
        $content = "Запись изменений в таблицу $name1 удалась " . date('h:i:s') . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
        fwrite($fp, $content);
        fclose($fp);
    } else {
        $content = "Запись изменений в таблицу $name1 не удалась " . date('h:i:s') . ' Код: ' . mysqli_error($conn) . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
        fwrite($fp, $content);
        fclose($fp);
    }
}
// Create connection mysql
function conn_DB($name1)
{
    global $conn;
    $servername = getenv('DB_HOST');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        $content = "$name1 mysql Connection failed: ".date('h:i:s') . ' Код: '. mysqli_connect_error() . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
        fwrite($fp, $content);
        fclose($fp);
//    die(" Connection failed: " . mysqli_connect_error());
        sleep(14400);
        require($_SERVER['DOCUMENT_ROOT'] . '/index.php');}
    else {
//write to log.txt 'Connected successfully';
        $content = "$name1 mysql Connected successfully ".date('h:i:s') ."\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
        fwrite($fp, $content);
        fclose($fp);}
//    return $conn;
}
// Create connection mysql for hook
function conn_DB_hook($name1)
{
    global $conn;
    $servername = getenv('DB_HOST');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        $content = "$name1 mysql Connection failed: " . date('h:i:s') . ' Код: ' . mysqli_connect_error() . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt", "a");
        fwrite($fp, $content);
        fclose($fp);
        die("$name1 mysql Connection failed: " . mysqli_connect_error());
    } else {
//write to log.txt 'Connected successfully';
        $content = "$name1 mysql Connected successfully ".date('h:i:s') ."\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
        fwrite($fp, $content);
        fclose($fp);}
}

//   connect amocrm
function conn_amo($d, $l, $name2)
{
    global $out;
    $curl = curl_init(); #Save the session descriptor cURL
#Set the necessary options for the cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $l);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('IF-MODIFIED-SINCE: '.$d));
    $out = curl_exec($curl); #initiate an API request and save the response in a variable
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
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
        #If the response code is not 200 or 204, we return an error message
        if ($code != 200 && $code != 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        }
    } catch (Exception $E) {
        $content = "$name2 curl Connection failed: ".date('h:i:s') . ' error: '. $code . "\n";
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log.txt","a");
        fwrite($fp, $content);
        fclose($fp);
        sleep(14400);
        require($_SERVER['DOCUMENT_ROOT'] . '/index.php');
//    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
}

