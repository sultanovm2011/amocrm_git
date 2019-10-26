<?php
include 'functions.php';
// read from .env
require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$subdomain = getenv('SUBDOMAIN');
$table_name = "customfields_leads";
$table_name2 = "last_update_cust_leads";
// Create connection mysql
// Check connection mysql
conn_DB($table_name);
$sql = "SELECT last_update_cust_leads FROM last_updates";
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
    $c = 0;
//Response processing
foreach ($Response as $v) {
    if (is_array($v)) {
        $updated_at = $v['updated_at'];
        if($updated_at < $dm) continue;
        $custom_fields = $v['custom_fields'];
        foreach ($custom_fields as $i) {
            if (is_array($i)) {
                $custom_fields_names = $i['name'];
                $custom_fields_ids = $i['id'];
                //     Writing Changes to a Table customfields_leads
                $sql = "SELECT name FROM customfields_leads WHERE id = $custom_fields_ids";
                $res = mysqli_query($conn, $sql);
                if ($res->num_rows > 0) {
//        Writing new data to a table customfields_leads
                    $sql1 = "UPDATE customfields_leads SET name='$custom_fields_names' WHERE id = $custom_fields_ids";
                    write_DB($conn, $sql1, $table_name);
                }   else {
                    $sql2 = "INSERT INTO customfields_leads (id, name)
                    VALUES ('$custom_fields_ids', '$custom_fields_names')";
                    write_DB($conn, $sql2, $table_name);
                }
            }
        }
        ++$c;
        if ($updated_at > $dt) {$dt = $updated_at;}
        if ($updated_at > $dm) {$dm = $updated_at;}
    }
}
    $dd = date(DATE_RFC2822, $dt);
    $sql3 = "UPDATE last_updates SET last_update_cust_leads = '$dm'";
    write_DB($conn, $sql3, $table_name2);}
mysqli_close($conn);





