<?php
header('Content-Type: application/json');
include 'db.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM invoice_items WHERE invoice_id=$id");
$conn->query("DELETE FROM invoices WHERE id=$id");

echo json_encode(['status'=>'success']);
?>
