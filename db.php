<?php
$host = "localhost";
$db = "invoice_db";
$user = "invoiceuser";
$pass = "Invoiceuser";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['status'=>'error','msg'=>'Database Connection Failed: '.$conn->connect_error]));
}
$conn->set_charset("utf8");
?>
