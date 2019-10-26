<?php
//include 'functions.php';
//// read from .env
//require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
//$dotenv = Dotenv\Dotenv::create(__DIR__);
//$dotenv->load();
//$subdomain = getenv('SUBDOMAIN');
$table_name = "buyers";
$table_name2 = "last_update_buyers";
// Create connection mysql
// Check connection mysql
conn_DB($table_name);
$sql = "SELECT last_update_buyers FROM last_updates";
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
    $link = "https://$subdomain.amocrm.ru/api/v2/customers/?limit_rows=500";
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
            if($updated_at < (time()-88000)) continue;
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
//        check is id exists
            $sql = "SELECT responsible_user_id FROM buyers WHERE id = $id";
            $res = mysqli_query($conn, $sql);
            if ($res->num_rows > 0) {
                //     Writing Changes to a Table BUYERS
                $sql1 = "UPDATE buyers SET is_deleted='$is_deleted', period_id='$period_id', created_at='$created_at', responsible_user_id='$responsible_user_id', next_date='$next_date', status_id='$status_id', ltv='$ltv', purchases_count='$purchases_count', average_check='$average_check', main_contact_id='$main_contact_id', contacts_ids='$contacts_ids_str'  WHERE id = $id";
                write_DB_changes($conn, $sql1, $table_name);
            } else {
//        Writing new data to a table BUYERS
                $sql2 = "INSERT INTO buyers (id, is_deleted, period_id, created_at, responsible_user_id, next_date, status_id, ltv, purchases_count, average_check, main_contact_id, contacts_ids)
                    VALUES ('$id', '$is_deleted', '$period_id', '$created_at', '$responsible_user_id', '$next_date', '$status_id', '$ltv', '$purchases_count', '$average_check', '$main_contact_id', '$contacts_ids_str')";
                write_DB($conn, $sql2, $table_name);
            }
            ++$c;
            if ($updated_at > $dt) {$dt = $updated_at;}
            if ($updated_at > $dm) {$dm = $updated_at;}
        }
    }
    $dd = date(DATE_RFC2822, $dt);
    $sql3 = "UPDATE last_updates SET last_update_buyers = '$dm'";
    write_DB_changes($conn, $sql3, $table_name2);
}
mysqli_close($conn);



