<?php
$out = $_POST;
$Response = $out['contacts']['update'];
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$table_name = "buyers_update";
// Create connection mysql
// Check connection mysql
conn_DB_hook($table_name);
//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $is_deleted = $v['is_deleted'];
        $period_id = $v['period_id'];
        $created_at = $v['created_at'];
        $responsible_user_id = $v['responsible_user_id'];
        $next_date = $v['next_date'];
        $status_id = $v['status_id'];
        $ltv = $v['ltv'];
        $purchases_count = $v['purchases_count'];
        $average_check = $v['average_check'];
        $main_contact_id = $v['main_contact']['id'];
        $contacts_ids = $v['contacts']['id'];

        if ($is_deleted == '') {
            $is_deleted = 0;
        }
        $contacts_ids_str = implode(",", $contacts_ids);
        $sql = "UPDATE buyers SET is_deleted='$is_deleted', period_id='$period_id', created_at='$created_at', responsible_user_id='$responsible_user_id', next_date='$next_date', status_id='$status_id', ltv='$ltv', purchases_count='$purchases_count', average_check='$average_check', main_contact_id='$main_contact_id', contacts_ids='$contacts_ids_str'  WHERE id = $id";
        write_DB($conn, $sql, $table_name);
    }
}
mysqli_close($conn);
