<?php

function SyncData()
{
    require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
    include 'functions.php';
    global $conn;
    global $subdomain;
    global $out;
    $subdomain = getenv('SUBDOMAIN');
    $servername = getenv('DB_HOST');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $conn = mysqli_connect($servername, $username, $password, $database);

    require($_SERVER['DOCUMENT_ROOT'] . '/auth.php');
    sleep(30);
    require($_SERVER['DOCUMENT_ROOT'] . '/contacts.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/buyers.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/CUSTOMFIELDS_BUYERS.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/leads.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/CUSTOMFIELDS_LEADS.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/pipeline.php');
    sleep(300);
    require($_SERVER['DOCUMENT_ROOT'] . '/transactions.php');
}
SyncData();