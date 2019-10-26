<?php
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$table_name = "transactions";
$table_name2 = "last_update_transactions";
$subdomain = getenv('SUBDOMAIN');
// Create connection mysql
// Check connection mysql
conn_DB($table_name);
$sql = "SELECT last_update_transactions FROM last_updates";
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
$link = "https://$subdomain.amocrm.ru/api/v2/transactions/?limit_rows=500";
    //   connect amocrm
    conn_amo($dd, $link, $table_name);
    // decode JSON and get response array
    $Response = json_decode($out, true);
    $Response = $Response['_embedded']['items'];
    $c = 0;
    //Response processing
    foreach ($Response as $v) {
    if (is_array($v)) {
        $id = $v['id'];
        $updated_at = $v['updated_at'];
        if($updated_at < (time()-88000)) continue;
        $created_at = $v['created_at'];
        $is_deleted = $v['is_deleted'];
        $date = $v['date'];
        $customer = $v['customer'];
        $customer_id = $customer['id'];
        $price = $v['price'];
        $comment = $v['comment'];
        if ($is_deleted == '') {
            $is_deleted = 0;
        }
        //        check is id exists
        $sql = "SELECT created_at FROM transactions WHERE id = $id";
        $res = mysqli_query($conn, $sql);
        if ($res->num_rows > 0) {
            //     Writing Changes to a Table customfields_buyers
            $sql1 = "UPDATE transactions SET created_at='$created_at', is_deleted='$is_deleted',
                    date= '$date', customer_id='$customer_id', price= '$price', comment='$comment'
                    WHERE id = $id";
            write_DB_changes($conn, $sql1, $table_name);
        } else {
            //        Writing new data to a table customfields_buyers
            $sql2 = "INSERT INTO transactions (id, created_at, is_deleted, date, customer_id, price, comment) 
                    VALUES ('$id', '$created_at', '$is_deleted', '$date', '$customer_id', '$price', '$comment')";
            write_DB($conn, $sql2, $table_name);
        }
        ++$c;
        if ($updated_at > $dt) {$dt = $updated_at;}
        if ($updated_at > $dm) {$dm = $updated_at;}
    }
}
    $dd = date(DATE_RFC2822, $dt);
    $sql3 = "UPDATE last_updates SET last_update_transactions = '$dm'";
    write_DB_changes($conn, $sql3, $table_name2);}
mysqli_close($conn);
