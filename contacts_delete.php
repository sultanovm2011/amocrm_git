<?php
$out = $_POST;
$Response = $out['contacts']['delete'];
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$table_name = "contacts_delete";
// Create connection mysql
// Check connection mysql
conn_DB_hook($table_name);
//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $is_deleted_flag = 1;

        $sql = "UPDATE contacts SET is_deleted='$is_deleted_flag' WHERE id = $id";
        write_DB($conn, $sql, $table_name);
    }
}
mysqli_close($conn);
