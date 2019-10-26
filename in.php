<?php


function changeEnvironmentVariable($k,$uniqid)
{
    require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
    $old = getenv($k);
    $path = '.env';
    file_put_contents($path, str_replace(
        "$k=".$old, "$k=".$uniqid, file_get_contents($path)
    ));
}
$uniqid1  = 111;
$k1 = "last_date_contacts";
changeEnvironmentVariable($k1,$uniqid1);
//$dotenv->load();
//$old = getenv($k);
//$path = '.env';
//file_put_contents($path, str_replace(
//        "$k=".$old, "$k=".$uniqid, file_get_contents($path)
//    ));
echo getenv('last_date_contacts');

//include 'index.php';
//SyncData();