<?php
//include 'functions.php';
//// read from .env
//require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$dotenv = Dotenv\Dotenv::create(__DIR__);
//$dotenv->load();
//$subdomain = getenv('SUBDOMAIN');
$status_id_first_lesson = getenv('STATUS_ID_FIRST_LESSON');
$status_id_is_paid = getenv('STATUS_ID_IS_PAID');
$table_name = "leads";
$table_name2 = "last_update_leads";
// Create connection mysql
// Check connection mysql
conn_DB($table_name);
$sql = "SELECT last_update_leads FROM last_updates";
$res = mysqli_query($conn, $sql);
while ($rslt = mysqli_fetch_assoc($res)) {
    foreach($rslt as $v) {
        $dt =  $v;
    }
}
$dd = date(DATE_RFC2822, $dt);
$dm = $dt;
//loop to remove the limit of 500 lines
$c = 500;
while ($c > 499) {
    $link = "https://$subdomain.amocrm.ru/api/v2/leads/?limit_rows=500";
    //   connect amocrm
    conn_amo($dd, $link, $table_name);
// decode JSON and get response array
    $Response = json_decode($out, true);
    $Response = $Response['_embedded']['items'];
//Response processing
    $c = 0;
    foreach ($Response as $v) {
        if (is_array($v)) {
            $updated_at = $v['updated_at'];
            if($updated_at < $dm) continue;
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

                    else {
                            $chanel_value = NULL;
                            $chanel_enum = NULL;
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
                    else {
                        $source_value = NULL;
                        $source_enum = NULL;
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
                    else {
                        $reason_value = NULL;
                        $reason_enum = NULL;
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
                    else {
                        $is_paid = 0;
                        $is_paid_enum = NULL;
                    }
            if ($is_deleted == '') {
                $is_deleted = 0;
            }
            $contacts_ids_str = implode(",", $contacts_ids);
//        check is id exists
            $sql = "SELECT status_id FROM leads WHERE id = $id";
            $res = mysqli_query($conn, $sql);

            if ($res->num_rows > 0) {
                $arr = mysqli_fetch_assoc($res);
                $old_status_id = $arr['status_id'];
                if ($status_id == $status_id_first_lesson and $status_id != $old_status_id) {
                $first_lesson_date = $updated_at;
                    } else {
                        $first_lesson_date = NULL;
                    }

                if ($status_id == $status_id_is_paid and $status_id != $old_status_id) {
                    $is_paid_date = $updated_at;
                    } else {
                        $is_paid_date = NULL;
                    }

                //     Writing Changes to a Table customfields_leads
                $sql1 = "UPDATE leads SET created_at='$created_at', closed_at='$closed_at', 
                 responsible_user_id='$responsible_user_id', main_contact_id='$main_contact_id', 
                 contacts_ids='$contacts_ids_str', sale='$sale', is_deleted='$is_deleted', 
                 chanel_value='$chanel_value', source_value='$source_value', reason_value='$reason_value', 
                 is_paid='$is_paid', chanel_enum = '$chanel_enum', source_enum = '$source_enum', 
                 reason_enum = '$reason_enum', status_id = '$status_id', first_lesson_date = '$first_lesson_date', 
                 is_paid_date = '$is_paid_date' WHERE id = $id";
                write_DB_changes($conn, $sql1, $table_name);
            } else {
                if ($status_id == $status_id_first_lesson) {
                    $first_lesson_date = $updated_at;
                } else {
                    $first_lesson_date = NULL;
                }

                if ($status_id == $status_id_is_paid) {
                    $is_paid_date = $updated_at;
                } else {
                    $is_paid_date = NULL;
                }
                //        Writing new data to a table leads
                $sql2 = "INSERT INTO leads (id, created_at, closed_at, responsible_user_id, 
                   main_contact_id, contacts_ids, sale, is_deleted, chanel_value, 
                   source_value, reason_value, is_paid, chanel_enum, source_enum, 
                   reason_enum, status_id, first_lesson_date, is_paid_date)
                    VALUES ('$id', '$created_at', '$closed_at', '$responsible_user_id',
                            '$main_contact_id', '$contacts_ids_str', '$sale', '$is_deleted',
                            '$chanel_value', '$source_value', '$reason_value', '$is_paid',
                            '$chanel_enum', '$source_enum', '$reason_enum', $status_id, 
                            '$first_lesson_date', '$is_paid_date')";
                write_DB($conn, $sql2, $table_name);
            }
            ++$c;
            if ($updated_at > $dt) {$dt = $updated_at;}
            if ($updated_at > $dm) {$dm = $updated_at;}
        }
    }
    $dd = date(DATE_RFC2822, $dt);
    $sql3 = "UPDATE last_updates SET last_update_leads = '$dm'";
    write_DB_changes($conn, $sql3, $table_name2);}
mysqli_close($conn);





