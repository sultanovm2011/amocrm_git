<?php
$out = $_POST;
$Response = $out['leads']['add'];
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$status_id_first_lesson = getenv('STATUS_ID_FIRST_LESSON');
$status_id_is_paid = getenv('STATUS_ID_IS_PAID');
$table_name = "leads_add";
$table_name2 = "pipeline_add";
// Create connection mysql
// Check connection mysql
conn_DB($table_name);

//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $created_at = $v['created_at'];
        $closed_at = $v['closed_at'];
        $responsible_user_id = $v['responsible_user_id'];
        $main_contact_id = $v['main_contact']['id'];
        $contacts_ids = $v['contacts']['id'];
        $sale = $v['sale'];
        $is_deleted = $v['is_deleted'];
        $custom_fields = $v['custom_fields'];
        $status_id = $v['status_id'];
        $old_status_id = $v['old_status_id '];
        $last_modified = $v['last_modified'];
        if ($status_id == $status_id_first_lesson and $status_id != $old_status_id) {
            $first_lesson_date = $last_modified;
        }
        if ($status_id == $status_id_is_paid and $status_id != $old_status_id) {
            $is_paid_date = $last_modified;
        }
        $account_id = $v['account_id'];
        $pipeline_id = $v['pipeline_id'];
        foreach ($custom_fields as $i) {
            if (is_array($i)) {
                $custom_fields_names = $i['name'];
                $custom_fields_ids = $i['id'];
                if ($custom_fields_names == 'Канал') {
                    $custom_fields_values = $i['values'];
                    foreach ($custom_fields_values as $k) {
                        if (is_array($k)) {
                            $chanel_value = $k['value'];
                            $chanel_enum = $k['enum'];
                        }
                    }
                }
                if ($custom_fields_names == 'Источник') {
                    $custom_fields_values = $i['values'];
                    foreach ($custom_fields_values as $k) {
                        if (is_array($k)) {
                            $source_value = $k['value'];
                            $source_enum = $k['enum'];
                        }
                    }
                }
                if ($custom_fields_names == 'Причина отказа') {
                    $custom_fields_values = $i['values'];
                    foreach ($custom_fields_values as $k) {
                        if (is_array($k)) {
                            $reason_value = $k['value'];
                            $reason_enum = $k['enum'];
                        }
                    }
                }

                if ($custom_fields_names == 'Оплата') {
                    $custom_fields_values = $i['values'];
                    foreach ($custom_fields_values as $k) {
                        if (is_array($k)) {
                            $is_paid = $k['value'];
                            $is_paid_enum = $k['enum'];
                        }
                    }
                }
            }
        }
        if ($is_deleted == '') {
            $is_deleted = 0;
        }
        if ($is_paid == '' and $is_paid_enum != NULL) {
            $is_paid = 0;
        }
        if ($first_lesson_date == '') {
            $first_lesson_date = 0;
        }
        if ($is_paid_date == '') {
            $is_paid_date = 0;
        }
        $contacts_ids_str = implode(",", $contacts_ids);
        
        $sql1 = "INSERT INTO leads (id, created_at, closed_at, responsible_user_id, main_contact_id, contacts_ids, sale, is_deleted, chanel_value, source_value, reason_value, is_paid, chanel_enum, source_enum, reason_enum, first_lesson_date, is_paid_date)
                    VALUES ('$id', '$created_at', '$closed_at', '$responsible_user_id',
                            '$main_contact_id', '$contacts_ids_str', '$sale', '$is_deleted',
                            '$chanel_value', '$source_value', '$reason_value', '$is_paid',
                            '$chanel_enum', '$source_enum', '$reason_enum', '$first_lesson_date', '$is_paid_date')";
        $sql2 = "INSERT INTO pipeline (account_id, pipeline_id, status_id)
                    VALUES ('$account_id', '$pipeline_id', '$status_id')";
        write_DB($conn, $sql1, $table_name);

        write_DB($conn, $sql2, $table_name2);
    }
}
mysqli_close($conn);
