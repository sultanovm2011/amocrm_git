<?php
$out = $_POST;
$Response = $out['contacts']['delete'];
$out = $_POST;
$Response = $out['contacts']['update'];
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$table_name = "buyers_delete";
// Create connection mysql
// Check connection mysql
conn_DB_hook($table_name);
//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $is_deleted = 1;
        $sql = "UPDATE buyers SET is_deleted='$is_deleted'  WHERE id = $id";
        write_DB($conn, $sql, $table_name);
    }
}
mysqli_close($conn);
