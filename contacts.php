<?php
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$subdomain = getenv('SUBDOMAIN');
$table_name = "contacts";
$table_name2 = "last_update_contacts";
// Create connection mysql
// Check connection mysql
conn_DB($table_name);
$sql = "SELECT last_update_contacts FROM last_updates";
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
#link for request
    $link = "https://$subdomain.amocrm.ru/api/v2/contacts/?limit_rows=500";
//   connect amocrm
    conn_amo($dd, $link, $table_name);
// decode JSON and get response array
    $Response = json_decode($out, true);
    $Response = $Response['_embedded']['items'];
    $c = 0;
//Response processing
    foreach ($Response as $v) {
        if (is_array($v)) {
            $updated_at = $v['updated_at'];
            if($updated_at < $dm) continue;
            $psyholog_flag = 0;
            $id = $v['id'];
            $name = $v['name'] . PHP_EOL;
            $responsible_user_id = $v['responsible_user_id'];
            $custom_fields = $v['custom_fields'];
            foreach ($custom_fields as $i) {
                if (is_array($i)) {
                    $cust_name = $i['name'];
                    if ($cust_name == 'Психолог') {
                        $psyholog_flag_v = $i['values'];
                    }
//                    if ($cust_name == 'is_deleted') {
//                        $is_deleted_flag_v = $i['values'];
//                    }
                }
            }
//            foreach ($is_deleted_flag_v as $i) {
//                if (is_array($i)) {
//                    $is_deleted_flag = $i['value'];
//                }
//            }
            foreach ($psyholog_flag_v as $i) {
                if (is_array($i)) {
                    $psyholog_flag = $i['value'];
                }
            }
            if ($psyholog_flag == '') {
                $psyholog_flag = 0;
            }
//            if ($is_deleted_flag == '') {
//                $is_deleted_flag = 0;
//            }
//        check is id exists
            $sql = "SELECT name FROM contacts WHERE id = $id";
            $res = mysqli_query($conn, $sql);
            if ($res->num_rows > 0) {
//                    Writing Changes to a Table CONTACTS
                $sql1 = "UPDATE contacts SET responsible_user_id='$responsible_user_id', name='$name',
                    psyholog= '$psyholog_flag'
                    WHERE id = $id";
                write_DB_changes($conn, $sql1, $table_name);
            } else {
//        Writing new data to a table CONTACTS
                $sql2 = "INSERT INTO contacts (id, responsible_user_id, name, psyholog)
                    VALUES ('$id', '$responsible_user_id', '$name', '$psyholog_flag')";
                write_DB($conn, $sql2, $table_name);
            }
            ++$c;
            if ($updated_at > $dt) {$dt = $updated_at;}
            if ($updated_at > $dm) {$dm = $updated_at;}
        }
    }
    $dd = date(DATE_RFC2822, $dt);
    $sql3 = "UPDATE last_updates SET last_update_contacts = '$dm'";
    write_DB_changes($conn, $sql3, $table_name2);
}
mysqli_close($conn);




