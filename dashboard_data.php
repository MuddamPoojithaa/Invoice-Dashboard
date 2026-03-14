<?php
header('Content-Type: application/json');
include 'db.php';

$data = [];

/* ===== MONTHLY REVENUE (ALL 12 MONTHS) ===== */
$res = $conn->query("
    SELECT MONTH(created_at) as month, SUM(received) as total
    FROM invoices
    GROUP BY MONTH(created_at)
");

$revenueData = [];
while($row = $res->fetch_assoc()){
    $revenueData[$row['month']] = (float)$row['total'];
}

$data['revenue'] = [];
for($m=1; $m<=12; $m++){
    $data['revenue'][] = [
        "month" => $m,
        "total" => $revenueData[$m] ?? 0
    ];
}

/* ===== RECENT INVOICES ===== */
$data['recent'] = [];
$res = $conn->query("
    SELECT invoice_no, client_name, total, received, balance, status, created_at
    FROM invoices
    ORDER BY id DESC
    LIMIT 5
");
while($row = $res->fetch_assoc()){
    $data['recent'][] = $row;
}
$res = $conn->query("SELECT COUNT(*) as total FROM invoices");
$data['total_invoices'] = ($res->fetch_assoc()['total'] ?? 0);
/* ===== INVOICE COUNTS ===== */
$res = $conn->query("SELECT COUNT(*) as total FROM invoices WHERE status='paid'");
$data['paid_invoices'] = ($res->fetch_assoc()['total'] ?? 0);

$res = $conn->query("SELECT COUNT(*) as total FROM invoices WHERE status='pending'");
$data['pending_invoices'] = ($res->fetch_assoc()['total'] ?? 0);

$res = $conn->query("SELECT COUNT(*) as total FROM invoices WHERE status='partial'");
$data['partial_invoices'] = ($res->fetch_assoc()['total'] ?? 0);

/* ===== AMOUNTS ===== */
$res = $conn->query("SELECT SUM(received) as total FROM invoices");
$data['paid_amount'] = ($res->fetch_assoc()['total'] ?? 0);

$res = $conn->query("SELECT SUM(balance) as total FROM invoices WHERE status='pending'");
$data['pending_amount'] = ($res->fetch_assoc()['total'] ?? 0);

$res = $conn->query("SELECT SUM(balance) as total FROM invoices WHERE status='partial'");
$data['partial_amount'] = ($res->fetch_assoc()['total'] ?? 0);

echo json_encode($data);
?>