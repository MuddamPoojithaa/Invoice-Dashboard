<?php
header('Content-Type: application/json');
include 'db.php';

// Fetch all invoices
$result = $conn->query("SELECT * FROM invoices ORDER BY id DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id'         => $row['id'],
        'invoice_no' => $row['invoice_no'],
        'client_name'=> $row['client_name'],
        'total'      => $row['total'],
        'received'   => $row['received'],
        'balance'    => $row['balance'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];
}

// Output JSON
echo json_encode($data);
?>