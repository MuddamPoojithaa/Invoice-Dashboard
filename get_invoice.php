<?php
header('Content-Type: application/json');
include 'db.php';

$id = intval($_GET['id']);

$res = $conn->query("SELECT * FROM invoices WHERE id=$id");
$invoice = $res->fetch_assoc();

$items_res = $conn->query("SELECT * FROM invoice_items WHERE invoice_id=$id");
$items = [];
while($r = $items_res->fetch_assoc()){
    $items[] = [
        'name'=>$r['item_name'],
        'qty'=>$r['qty'],
        'price'=>$r['price'],
        'amount'=>$r['amount']
    ];
}

$invoice['items'] = $items;
echo json_encode($invoice);
?>
