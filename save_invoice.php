    <?php
    date_default_timezone_set('Asia/Kolkata');
    header('Content-Type: application/json');
    include 'db.php';
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(!$data){
        echo json_encode(["status"=>"error","msg"=>"Invalid input"]);
        exit;
    }
    
    // Basic invoice info
    $clientName    = $data['clientName'] ?? '';
    $clientAddress = $data['clientAddress'] ?? '';
    $clientPhone   = $data['clientPhone'] ?? '';
    
    $total    = floatval($data['total'] ?? 0);
    $received = floatval($data['received'] ?? 0);
    $items    = $data['items'] ?? [];
    $id       = $data['id'] ?? null;
    
    // Calculate balance & status
    $balance = max(0, $total - $received);
    
    if($received == 0){
        $status = "Pending";
    } elseif($received < $total){
        $status = "Partial";
    } else{
        $status = "Paid";
        $balance = 0;
    }
    
    /* ==============================
       UPDATE EXISTING INVOICE
    ============================== */
    if($id){
    
        // Update invoice
        $stmt = $conn->prepare("
            UPDATE invoices SET
            client_name=?, client_address=?, client_phone=?,
            total=?, received=?, balance=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param("sssdddsi", 
            $clientName, $clientAddress, $clientPhone,
            $total, $received, $balance, $status, $id
        );
        $stmt->execute();
        $stmt->close();
    
        // Remove old items
        $conn->query("DELETE FROM invoice_items WHERE invoice_id=$id");
    
        // Insert new items
        $item_stmt = $conn->prepare("
            INSERT INTO invoice_items
            (invoice_id, item_name, qty, price, amount)
            VALUES (?, ?, ?, ?, ?)
        ");
        foreach($items as $item){
            $name   = $item['name'] ?? '';
            $qty    = floatval($item['qty'] ?? 0);
            $price  = floatval($item['price'] ?? 0);
            $amount = floatval($item['amount'] ?? 0);
            $item_stmt->bind_param("isddd", $id, $name, $qty, $price, $amount);
            $item_stmt->execute();
        }
        $item_stmt->close();
    
        // Return updated invoice
        $res = $conn->query("SELECT invoice_no, created_at FROM invoices WHERE id=$id");
        $row = $res->fetch_assoc();
    
        echo json_encode([
            "status"=>"success",
            "msg"=>"Invoice updated successfully",
            "invoice_no"=>$row['invoice_no'],
            "created_at"=>$row['created_at']
        ]);
    
    }
    
    /* ==============================
       CREATE NEW INVOICE
    ============================== */
    else {
    
        // Generate new invoice number
        $res = $conn->query("SELECT invoice_no FROM invoices ORDER BY id DESC LIMIT 1");
        $row = $res->fetch_assoc();
    
        if($row && $row['invoice_no']){
            $num = intval(substr($row['invoice_no'], 2));
            $newInvoiceNo = "SM".str_pad($num+1, 8, "0", STR_PAD_LEFT);
        } else {
            $newInvoiceNo = "SM00000001";
        }
    
        // Insert new invoice
        $stmt = $conn->prepare("
            INSERT INTO invoices
            (invoice_no, client_name, client_address, client_phone, total, received, balance, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssdddds",
            $newInvoiceNo, $clientName, $clientAddress, $clientPhone,
            $total, $received, $balance, $status
        );
        $stmt->execute();
        $invoice_id = $stmt->insert_id;
        $stmt->close();
    
        // Insert items
        $item_stmt = $conn->prepare("
            INSERT INTO invoice_items
            (invoice_id, item_name, qty, price, amount)
            VALUES (?, ?, ?, ?, ?)
        ");
        foreach($items as $item){
            $name   = $item['name'] ?? '';
            $qty    = floatval($item['qty'] ?? 0);
            $price  = floatval($item['price'] ?? 0);
            $amount = floatval($item['amount'] ?? 0);
            $item_stmt->bind_param("isddd", $invoice_id, $name, $qty, $price, $amount);
            $item_stmt->execute();
        }
        $item_stmt->close();
    
        // Return invoice info
        $res = $conn->query("SELECT created_at FROM invoices WHERE id=$invoice_id");
        $row = $res->fetch_assoc();
    
        echo json_encode([
            "status"=>"success",
            "msg"=>"Invoice saved successfully",
            "invoice_no"=>$newInvoiceNo,
            "created_at"=>$row['created_at']
        ]);
    }
    
    ?>