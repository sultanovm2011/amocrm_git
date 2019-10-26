<?php
$out = $_POST;
$Response = $out['contacts']['add'];
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$table_name = "contacts_add";
// Create connection mysql
// Check connection mysql
conn_DB_hook($table_name);
//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $name = $v['name'] . PHP_EOL;
        $responsible_user_id = $v['responsible_user_id'];
        $custom_fields = $v['custom_fields'];
        $psyholog_flag = 0;
        $is_deleted_flag = 0;

        foreach ($custom_fields as $i) {
            if (is_array($i)) {
                $psyholog = $i['name'];
                if ($psyholog == 'Психолог') {
                    $psyholog_flag_v = $i['values'];
                }
            }
        }

        foreach ($psyholog_flag_v as $i) {
            if (is_array($i)) {
                $psyholog_flag = $i['value'];
            }
        }
        if ($psyholog_flag == '') {
            $psyholog_flag = 0;
        }
        foreach ($custom_fields as $i) {
            if (is_array($i)) {
                $is_deleted = $i['name'];
                if ($is_deleted == 'is_deleted') {
                    $is_deleted_flag_v = $i['values'];
                }
            }
        }

        foreach ($is_deleted_flag_v as $i) {
            if (is_array($i)) {
                $is_deleted_flag = $i['value'];
            }
        }
        if ($is_deleted_flag == '') {
            $is_deleted_flag = 0;
        }
        $sql = "INSERT INTO contacts (id, responsible_user_id, name, psyholog, is_deleted) 
                VALUES ('$id', '$responsible_user_id', '$name', '$psyholog_flag', '$is_deleted_flag')";
        write_DB($conn, $sql, $table_name);
    }
}
mysqli_close($conn);



