<?php
session_start();

if(!isset($_SESSION['admin_logged_in'])){
header("Location: admin_login.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
*{box-sizing:border-box}
body{
  margin:0;
  font-family:"Segoe UI",Arial,sans-serif;
  background:#f4f6f8;
  padding:20px;
}

/* DASHBOARD */
.dashboard{display:flex; min-height:100vh}

/* SIDEBAR */
.sidebar{
  width:230px;
  background:#0b78a4;
  color:#fff;
  padding:20px;
}
.sidebar h2{margin:0 0 20px}
.sidebar button{
  width:100%;
  background:none;
  border:none;
  color:#fff;
  text-align:left;
  padding:10px;
  margin-bottom:6px;
  cursor:pointer;
  border-radius:4px;
}
.sidebar button.active,
.sidebar button:hover{
  background:rgba(255,255,255,0.2);
}

/* CONTENT */
.content{flex:1; padding:20px}
.section{display:none}
.section.active{display:block}

/* FORM */
.form{
  background:#fff;
  padding:20px;
  max-width:1200px;
  border-radius:6px;
  box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.form input,.form textarea{
  width:100%;
  padding:10px;
  margin-bottom:10px;
  box-sizing:border-box;
}

/* BUTTON */
button{
  background:#0b78a4;
  color:#fff;
  border:none;
  padding:8px 14px;
  cursor:pointer;
  border-radius:4px;
}

/* TABLE */
table{width:100%; border-collapse:collapse; margin-top:15px}
th,td{border:1px solid #555; padding:8px; text-align:center}
th{background:#0b78a4; color:#fff}
th:nth-child(2),td:nth-child(2){text-align:left}
.history-table button{font-size:12px; padding:4px 8px}

/* INVOICE */
#invoice{
  background:#fff;
  max-width:1200px;
  margin:30px auto;
  padding:20px;
  box-sizing:border-box;
}
.line{ height:4px; background:#0b78a4; margin:10px 0; }
h1{ text-align:center; margin:10px 0; letter-spacing:1px; }
.top{ display:flex; justify-content:space-between; margin-top:20px; }
.top div{ width:48%; line-height:1.6; }
.invoice-box{ border:1px solid #0b78a4; padding:10px; box-sizing:border-box; }
.bottom{ display:flex; gap:15px; margin-top:20px; }
.box{ flex:1; border:1px solid #0b78a4; box-sizing:border-box; }
.box h4{ background:#0b78a4; color:#fff; margin:0; padding:6px; }
.amount-row{ display:flex; justify-content:space-between; padding:8px 10px; border-bottom:1px solid #ccc; }
.amount-row.total{ font-weight:bold; background:#f2f7fb; border-top:2px solid #555; }
.no-print{ margin-top:10px; }

@media print {
  body {margin:0; -webkit-print-color-adjust: exact; print-color-adjust: exact;}
  .sidebar,
  .form,
  .no-print,
  #history,
  #create{
    display:none !important;
  }
  #invoice { margin:0; width:100%; background:#fff !important; }
  table, th, td {border:1px solid #555 !important;}
  th{ background:#0b78a4 !important; color:#fff !important; }
  .line{ background:#0b78a4 !important; }
  .box, .invoice-box { border:1px solid #0b78a4 !important; }
  .box h4{ background:#0b78a4 !important; color:#fff !important; }
}
.invoice-search{
  display:flex;
  flex-wrap:wrap;
  gap:15px;
  margin-bottom:20px;
  align-items:center;
  background:#ffffff;
  padding:15px 20px;
  border-radius:12px;
  box-shadow:0 6px 15px rgba(0,0,0,0.08);
  transition:0.3s;
}

.invoice-search:hover{
  box-shadow:0 8px 20px rgba(0,0,0,0.12);
}

.invoice-search input,
.invoice-search select{
  padding:10px 14px;
  border:1px solid #dcdcdc;
  border-radius:8px;
  font-size:14px;
  outline:none;
  transition:0.3s;
  min-width:180px;
}

.invoice-search input:focus,
.invoice-search select:focus{
  border-color:#0b78a4;
  box-shadow:0 0 8px rgba(11,120,164,0.25);
}

.invoice-search select{
  appearance:none;
  -webkit-appearance:none;
  -moz-appearance:none;
  background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg width='14' height='14' viewBox='0 0 20 20' fill='gray' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 7L10 12L15 7H5Z'/%3E%3C/svg%3E");
  background-repeat:no-repeat;
  background-position:right 12px center;
  background-size:14px;
  cursor:pointer;
}
.history-table{
  width:100%;
  border-collapse:collapse;
  background:#fff;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.history-table th,
.history-table td{
  padding:12px 15px;
  text-align:center;
  font-size:14px;
  transition:0.3s;
}

.history-table th{
  background:#0b78a4;
  color:#fff;
  text-transform:uppercase;
  font-weight:600;
  letter-spacing:0.5px;
}

.history-table tbody tr{
  border-bottom:1px solid #eee;
  transition:0.3s;
}

.history-table tbody tr:hover{
  background:#f2f9ff;
}

.history-table td button{
  font-size:12px;
  padding:5px 10px;
  border:none;
  border-radius:6px;
  cursor:pointer;
  transition:0.3s;
}

.history-table td button:hover{
  opacity:0.85;
}

.history-table td button:nth-child(1){ /* Edit */
  background:#0b78a4;
  color:#fff;
}

.history-table td button:nth-child(2){ /* Delete */
  background:#0b78a4;
  color:#fff;
}



</style>
</head>
<body>

<div class="dashboard">

  <!-- SIDEBAR -->
  <div class="sidebar">
      <div class="invoice-header">
  <img src="logo.png" alt="SumaHi Media Logo" class="company-logo">


</div>
    <h2>Invoice Panel</h2>
    <button class="active" onclick="showSection('create',this)"> Create Invoice</button>
    <button onclick="showSection('history',this)">Invoice History</button>
 
   <button onclick="showSection('revenue',this)">Monthly Revenue</button>
  </div>

  <!-- CONTENT -->
  <div class="content">
<div id="revenue" class="section">

<div class="revenue-header">
<h2>Revenue Dashboard</h2>
<p>Invoice and payment analytics</p>
</div>

<!-- STATS -->
<div class="stats-container">
<!-- TOTAL INVOICES CARD -->
<div class="stat-card totalInvoices">
  <h3>Total Invoices</h3>
  <p id="totalInvoicesCount">0</p>
  <canvas id="totalInvoicesSparkline" height="40"></canvas>
  <span id="totalInvoicesChange" style="font-size:12px; color:green;">↑ 0%</span>
</div>
<div class="stat-card pending">
<h3>Pending Invoices</h3>
<p id="pendingInvoices">0</p>
</div>

<div class="stat-card paid">
<h3>Paid Invoices</h3>
<p id="paidInvoices">0</p>
</div>
  <div class="stat-card partial">
    <h3>Partial Invoices</h3>
    <p id="partialInvoices">0</p>
  </div>

<div class="stat-card pendingAmount">
<h3>Pending Amount</h3>
<p id="pendingAmount">₹0</p>
</div>

<div class="stat-card paidAmount">
<h3>Paid Amount</h3>
<p id="paidAmount">₹0</p>
</div>

</div>

<!-- CHART ROW -->
<div class="chart-grid">

<div class="chart-box">
<h3>Monthly Revenue</h3>
<canvas id="revenueChart"></canvas>
</div>

<div class="chart-box">
<h3>Invoice Status</h3>
<canvas id="statusChart"></canvas>
</div>

</div>

<!-- RECENT INVOICES -->
<div class="recent-box">
<h3>Recent Invoices</h3>

<table class="recent-table">
<thead>
<tr>
<th>Invoice</th>
<th>Client</th>
<th>Date</th>
<th>Total</th>
<th>Status</th>
</tr>
</thead>

<tbody id="recentInvoices"></tbody>

</table>

</div>

</div>
<style>.revenue-header{
margin-bottom:20px;
}

.revenue-header h2{
margin:0;
color:#0b78a4;
font-size:26px;
}

.revenue-header p{
color:#777;
}

/* STATS */

.stats-container{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:30px;
}

.stat-card{
background:#fff;
padding:25px;
border-radius:12px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
transition:.3s;
}

.stat-card:hover{
transform:translateY(-4px);
}

.stat-card h3{
font-size:15px;
color:#666;
margin:0;
}

.stat-card p{
font-size:20px;
font-weight:bold;
margin-top:10px;
}

/* COLORS */

.pending{border-left:6px solid #ff9800;}
.paid{border-left:6px solid #4caf50;}
.pendingAmount{border-left:6px solid #f44336;}
.paidAmount{border-left:6px solid #2196f3;}

/* CHART GRID */

.chart-grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
margin-bottom:30px;
}
/* STAT CARD STYLING */
.stat-card.totalInvoices {
  border-left: 6px solid #2196f3;
  position: relative;
}

.stat-card.totalInvoices p {
  font-size: 28px;
  font-weight: bold;
  margin: 8px 0;
}

.stat-card.totalInvoices canvas {
  width: 100% !important;
  margin-top: 5px;
}
.chart-box{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* RECENT INVOICES */

.recent-box{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.recent-table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

.recent-table th,
.recent-table td{
padding:10px;
border-bottom:1px solid #eee;
text-align:left;
}

.recent-table th{
color:white;
}

.status-paid{
color:#4caf50;
font-weight:bold;
}

.status-pending{
color:#ff9800;
font-weight:bold;
}
/* STAT CARDS */
.stat-card {
  background: linear-gradient(145deg, #ffffff, #f0f3f7);
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.4s ease;
  position: relative;
  overflow: hidden;
  cursor: pointer;
}

.stat-card::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(11, 120, 164, 0.05);
  top: -100%;
  left: 0;
  transition: all 0.5s ease;
}

.stat-card:hover::before {
  top: 0;
}

.stat-card h3 {
  font-size: 14px;
  color: #555;
  margin: 0 0 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-card p {
  font-size: 24px;
  font-weight: 700;
  margin: 0;
  color: #0b78a4;
}

/* STATUS COLORS WITH ICONS */
.stat-card.pending {
  border-left: 6px solid #ff9800;
}
.stat-card.paid {
  border-left: 6px solid #4caf50;
}
.stat-card.partial {
  border-left: 6px solid #ffc107;
}
.stat-card.pendingAmount {
  border-left: 6px solid #f44336;
}
.stat-card.paidAmount {
  border-left: 6px solid #2196f3;
}

.stat-card i {
  position: absolute;
  top: 20px;
  right: 20px;
  font-size: 36px;
  color: rgba(0, 0, 0, 0.08);
}
.recent-table th {
  background: linear-gradient(90deg, #0b78a4, #0370a3);
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

.recent-table tbody tr:hover {
  background: #e8f4fd;
  transition: background 0.3s;
}

.recent-table td button {
  background: linear-gradient(90deg, #0b78a4, #0370a3);
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  transition: 0.3s;
}

.recent-table td button:hover {
  opacity: 0.85;
}
.chart-box {
  background: linear-gradient(145deg, #ffffff, #f7f9fc);
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.chart-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
}

.chart-box h3 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #0b78a4;
}

</style>
    <!-- CREATE -->
    <div id="create" class="section active">
   <div class="form">
  <h3>Manage Client Invoices</h3>

  <input id="clientName" placeholder="Business Name">
  <textarea id="clientAddress" placeholder="Address"></textarea>
  <input id="clientPhone" placeholder="Contact No">
<input id="clientEmail" type="email" placeholder="Email Address">
  <!-- SERVICES LIST -->
<datalist id="servicesList">
  <option value="Street View 360°">
  <option value="QR Code Setup">
  <option value="Mobile Number Update">
  <option value="New Business Listing">
  <option value="Location Update">
  <option value="Business Verification">
  <option value="Profile Reinstatement">
  <option value="Google Maps SEO">
  <option value="Social Media Optimization">
  <option value="Digital Marketing">
  <option value="NFC QR Cards">
  <option value="QR Stand">
  <option value="Magic QR">
  <option value="Smart Business Card">
  <option value="Website Development">
  <option value="Meta Ads">
</datalist>
  <table id="itemTable">
    <tr>
      <th>Item</th>
      <th>Qty</th>
      <th>Price</th>
      <th>Amount</th>
      <th></th>
    </tr>
  </table>

  <button onclick="addItem()">+ Add Item</button>
<br><br>
  <input id="receivedInput" type="number" placeholder="Amount Received">

  
 <div class="select-box">
  <select id="paymentMode">
    <option value="">Mode of Payment</option>
    <option value="Cash">Cash</option>
    <option value="UPI">UPI</option>
    <option value="Bank Transfer">Bank Transfer</option>
  
    <option value="Cheque">Cheque</option>
  </select>
</div>
  <br><br>
  <button onclick="generateInvoice()">Save / Update Invoice</button>
  <button type="button" onclick="generateAndSendInvoice()">Generate & Send Invoice</button>
</div>
      
      
      <!-- INVOICE -->
<div id="invoice">
  <!-- HEADER -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <div>
    <img src="images/Sumahi-Logo-png.-1.png" alt="Company Logo" style="max-height:80px; height:auto;">
  </div>
  <div style="text-align:right;">
    <h2 style="margin:0; color:#0b78a4;">Sumahi Media Pvt Ltd</h2>
    <p style="margin:2px 0;">Karkhana, Secunderabad, Telangana 500015</p>
    <p style="margin:2px 0;">Phone no.: 8074251396 Email: sumahimedia@gmail.com</p>
  </div>
</div>
  <div id="invoiceContent">
    <div class="line"></div>
    <h1>Invoice</h1>
    <div class="line"></div>

    <div class="top">
      <div class="invoice-box">
        <b>Bill To</b><br><br>
        <span id="iName"></span><br>
        <span id="iAddress"></span><br>
        Contact : <span id="iPhone"></span><br>
        Email : <span id="iEmail"></span>
      </div>
      <div class="invoice-box" style="text-align:right">
        <b>Invoice Details</b><br><br>
        Invoice No : <b id="iNo"></b><br>
        Date : <b id="iDate"></b>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Item name</th>
          <th>Qty</th>
          <th>Price / unit</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody id="invoiceItems"></tbody>
      <tr>
        <td colspan="4"><b>Total</b></td>
        <td><b>₹ <span id="grandTotal"></span></b></td>
      </tr>
    </table>

    <div class="bottom">
      <div class="box">
        <h4>Invoice Amount In Words</h4>
        <div id="amountWords" style="padding:10px"></div>
      </div>
      <div class="box">
        <h4>Amounts</h4>
        <div class="amount-row"><span>Sub Total</span><span>₹ <span id="subTotal"></span></span></div>
        <div class="amount-row"><span>Received</span><span>₹ <span id="receivedShow"></span></span></div>
        <div class="amount-row total"><span>Balance</span><span>₹ <span id="balance"></span></span></div>
      </div>
    </div>
  
  </div>

<div class="bottom">
      <div class="box">
        <h4>Bank Details:</h4>
        
  <div style="display:flex; justify-content:flex-start; align-items:center; margin-top:10px; gap:20px; padding-left: 12px;">
  <!-- Scanner Image -->
  <div>
    <img src="images/r.jpeg" alt="Scanner" style="max-width:120px; height:auto;">
  </div>
          <!-- Bank Details -->
  <div style="line-height:1.6;">
    <b>Bank Details:</b><br>
    Name: CENTRAL BANK OF INDIA, SECUNDERABAD<br>
    Account No.: 3668632156<br>
    IFSC Code: CBIN0280814<br>
    Account Holder's Name: Sumahi Media Pvt Ltd
  </div>
      </div>
      
      
    </div>
  



  <div style="display:flex; justify-content:flex-end; margin-top:30px; align-items:center; flex-direction:column; width:50%;">
  <span>For: Sumahi Media Pvt Ltd</span>
  <img src="images/Screenshot 2026-02-03 at 12-40-05 Kodali Guest Suites Nov Invoice.pdf.png" alt="Authorized Signatory" style="max-width:150px; height:auto; margin:5px 0;">
  <span>Authorized Signatory</span>
</div>

</div>


<button class="no-print" onclick="window.print()">Print / Save PDF</button>

</div>
    </div>

    <!-- HISTORY -->
    <div id="history" class="section">
      <h3>Invoice History</h3>
      <div class="invoice-search">

<input type="text" id="searchInvoice" placeholder="Search Invoice / Client">
<select id="monthFilter">
<option value="">All Months</option>
<option value="01">Jan</option>
<option value="02">Feb</option>
<option value="03">Mar</option>
<option value="04">Apr</option>
<option value="05">May</option>
<option value="06">Jun</option>
<option value="07">Jul</option>
<option value="08">Aug</option>
<option value="09">Sep</option>
<option value="10">Oct</option>
<option value="11">Nov</option>
<option value="12">Dec</option>
</select>

<select id="yearFilter">
<option value="">All Years</option>
<option value="2026">2026</option>
<option value="2025">2025</option>
<option value="2024">2024</option>
</select>
<select id="statusFilter">
  <option value="">All Status</option>
  <option value="paid">Paid</option>
  <option value="pending">Pending</option>
  <option value="partial">Partial</option>
</select>

</div>
      <table class="history-table">
        <thead>
          <tr>
            <th>Invoice No</th>
            <th>Client</th>
            <th>Date</th>
            <th>Total</th>
            <th>Received</th>
            <th>Balance</th>
            <th>Edit</th>
            <th>Delete</th>
            
          </tr>
        </thead>
        <tbody id="historyBody"></tbody>
      </table>
    </div>
<style>.invoice-search{
display:flex;
flex-wrap:wrap;
gap:12px;
margin-bottom:15px;
align-items:center;
background:#fff;
padding:12px 15px;
border-radius:8px;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

/* Search Input */

.invoice-search input{
padding:9px 12px;
border:1px solid #dcdcdc;
border-radius:6px;
width:230px;
font-size:14px;
outline:none;
transition:0.3s;
}

.invoice-search input:focus{
border-color:#0b78a4;
box-shadow:0 0 6px rgba(11,120,164,0.25);
}
#paymentModePreview{
font-weight:600;
color:#0b78a4;
margin-left:5px;
}
/* Dropdown Filters */

.invoice-search select{
padding:9px 35px 9px 12px; /* RIGHT SIDE SPACE FOR ARROW */
border:1px solid #dcdcdc;
border-radius:6px;
font-size:14px;
background:#fff;
cursor:pointer;
outline:none;
transition:0.3s;
appearance:none;
-webkit-appearance:none;
-moz-appearance:none;

/* Custom arrow */

background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg width='14' height='14' viewBox='0 0 20 20' fill='gray' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 7L10 12L15 7H5Z'/%3E%3C/svg%3E");
background-repeat:no-repeat;
background-position:right 10px center;
background-size:14px;
}

.invoice-search select:focus{
border-color:#0b78a4;
box-shadow:0 0 6px rgba(11,120,164,0.25);
}
.select-box{
position:relative;
width:100%;
}
.invoice-box{
font-size:14px;
line-height:1.6;
}

.invoice-box b{
font-size:15px;

}

#iEmail{
display:block;
color:#444;
margin-top:3px;
}
.select-box select{
width:100%;
padding:12px;
padding-right:35px;
border:1px solid #ccc;
border-radius:8px;
font-size:14px;
background:#fff;
cursor:pointer;
appearance:none;
-webkit-appearance:none;
-moz-appearance:none;
}

/* Custom arrow */
.select-box::after{
content:"▼";
position:absolute;
right:12px;
top:50%;
transform:translateY(-50%);
font-size:12px;
color:#555;
pointer-events:none;
}
/* Mobile */

@media (max-width:768px){

.invoice-search{
flex-direction:column;
align-items:stretch;
}

.invoice-search input,
.invoice-search select{
width:100%;
}

}</style>
    <!-- INVOICE PREVIEW -->
<div id="invoicePreview" class="section">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
      <img src="images/Sumahi-Logo-png.-1.png" alt="Company Logo" style="max-height:80px;">
    </div>
    <div style="text-align:right;">
      <h2 style="margin:0; color:#0b78a4;">Sumahi Media Pvt Ltd</h2>
      <p style="margin:2px 0;">Karkhana, Secunderabad, Telangana 500015</p>
      <p style="margin:2px 0;">Phone: 8074251396 Email: sumahimedia@gmail.com</p>
    </div>
  </div>

  <div id="invoiceContentPreview">
    <div class="line"></div>
    <h1>Invoice</h1>
    <div class="line"></div>

    <div class="top">
      <div class="invoice-box">
        <b>Bill To</b><br><br>
        <span id="iNamePreview"></span><br>
        <span id="iAddressPreview"></span><br>
        Contact : <span id="iPhonePreview"></span><br>
       Email : <span id="iEmailPreview"></span>
      </div>
      <div class="invoice-box" style="text-align:right">
        <b>Invoice Details</b><br><br>
        Invoice No : <b id="iNoPreview"></b><br>
        Date : <b id="iDatePreview"></b>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Item name</th>
          <th>Qty</th>
          <th>Price / unit</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody id="invoiceItemsPreview"></tbody>
      <tr>
        <td colspan="4"><b>Total</b></td>
        <td><b>₹ <span id="grandTotalPreview"></span></b></td>
      </tr>
    </table>

    <div class="bottom">
     <div class="box">
  <h4>Invoice Amount In Words</h4>

  <div id="amountWordsPreview" style="padding:10px"></div>

  <!-- Payment Mode -->
  <div style="margin-top:10px; padding:10px; border-top:1px solid black;">
    <b>Mode of Payment :</b>
    <span id="paymentModePreview"></span>
  </div>

</div>
      <div class="box">
        <h4>Amounts</h4>
        <div class="amount-row"><span>Sub Total</span><span>₹ <span id="subTotalPreview"></span></span></div>
        <div class="amount-row"><span>Received</span><span>₹ <span id="receivedShowPreview"></span></span></div>
        <div class="amount-row total"><span>Balance</span><span>₹ <span id="balancePreview"></span></span></div>
      </div>
    </div>
<!-- inside invoicePreview -->
<div class="bottom">
  <div class="box">
    <h4>Bank Details:</h4>
    <div style="display:flex; justify-content:flex-start; align-items:center; margin-top:10px; gap:20px; padding-left:12px;">
      <!-- Scanner Image -->
      <div>
        <img src="images/r.jpeg" alt="Scanner" style="max-width:120px; height:auto;">
      </div>
      <!-- Bank Details -->
      <div style="line-height:1.6;">
        <b>Bank Details:</b><br>
        Name: CENTRAL BANK OF INDIA, SECUNDERABAD<br>
        Account No.: 3668632156<br>
        IFSC Code: CBIN0280814<br>
        Account Holder's Name: Sumahi Media Pvt Ltd
      </div>
    </div>
  </div>

  <div style="display:flex; justify-content:flex-end; margin-top:30px; align-items:center; flex-direction:column; width:50%;">
    <span>For: Sumahi Media Pvt Ltd</span>
    <img src="images/Screenshot 2026-02-03 at 12-40-05 Kodali Guest Suites Nov Invoice.pdf.png" alt="Authorized Signatory" style="max-width:150px; height:auto; margin:5px 0;">
    <span>Authorized Signatory</span>
  </div>
</div>

    <button class="no-print" onclick="window.print()">Print / Save PDF</button>
  </div>
</div>


  </div>
</div>

<script>
let editIndex = null;

/* NAVIGATION */
function showSection(id,btn){

document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));

document.getElementById(id).classList.add('active');

document.querySelectorAll('.sidebar button').forEach(b=>b.classList.remove('active'));

btn.classList.add('active');

if(id==="history") loadInvoices();

if(id==="revenue") loadRevenue();

}

function addItem(){

  const table = document.getElementById("itemTable");
  const row = table.insertRow();

  row.innerHTML = `
    <td>
      <input list="servicesList" placeholder="Select or type service">
    </td>

    <td>
      <input type="number" value="1" min="1" oninput="calcRow(this)">
    </td>

    <td>
      <input type="number" value="0" oninput="calcRow(this)">
    </td>

    <td class="rowAmount">0</td>

    <td>
      <button onclick="this.closest('tr').remove(); calcTotal();">✖</button>
    </td>
  `;

}

function calcRow(el){

  const row = el.closest("tr");

  const qty = row.children[1].querySelector("input").value || 0;
  const price = row.children[2].querySelector("input").value || 0;

  const amount = qty * price;

  row.querySelector(".rowAmount").innerText = amount;

  calcTotal();
}
function calcTotal(){

  let total = 0;

  document.querySelectorAll(".rowAmount").forEach(r=>{
    total += parseFloat(r.innerText) || 0;
  });

  console.log("Total:", total);
}
function safeSetText(id, text) {
  const el = document.getElementById(id);
  if (!el) {
    console.warn("Missing element:", id);
    return;
  }
  el.innerText = text ?? '';
}
  function renderInvoice(inv) {

  // MAIN INVOICE
  safeSetText("iName", inv.clientName);
  safeSetText("iAddress", inv.clientAddress);
  safeSetText("iPhone", inv.clientPhone);
  safeSetText("iNo", inv.invoice_no);
    safeSetText("iEmail", inv.clientEmail);
  safeSetText("iDate", inv.created_at ? inv.created_at.split(' ')[0] : '');

  // ITEMS
  let html = "";
  if (inv.items && inv.items.length) {
    inv.items.forEach((it, i) => {
      html += `<tr>
        <td>${i + 1}</td>
        <td>${it.name}</td>
        <td>${it.qty}</td>
        <td>₹ ${it.price}</td>
        <td>₹ ${it.amount}</td>
      </tr>`;
    });
  }

  const invoiceItemsEl = document.getElementById("invoiceItems");
  if (invoiceItemsEl) invoiceItemsEl.innerHTML = html;

  // TOTALS
  safeSetText("subTotal", inv.total);
  safeSetText("grandTotal", inv.total);
  safeSetText("receivedShow", inv.received);
  safeSetText("balance", inv.balance);
  safeSetText("amountWords", numberToWords(inv.total) + " Rupees only");
  safeSetText("paymentModePreview", inv.paymentMode);

  /* ========= PREVIEW ========= */
  safeSetText("iNamePreview", inv.clientName);
  safeSetText("iAddressPreview", inv.clientAddress);
  safeSetText("iPhonePreview", inv.clientPhone);
  safeSetText("iNoPreview", inv.invoice_no);
  safeSetText("iEmailPreview", inv.clientEmail);
  safeSetText("iDatePreview", inv.created_at ? inv.created_at.split(' ')[0] : '');

  const invoiceItemsPreviewEl = document.getElementById("invoiceItemsPreview");
  if (invoiceItemsPreviewEl) invoiceItemsPreviewEl.innerHTML = html;

  safeSetText("subTotalPreview", inv.total);
  safeSetText("grandTotalPreview", inv.total);
  safeSetText("receivedShowPreview", inv.received);
  safeSetText("balancePreview", inv.balance);
  safeSetText("amountWordsPreview", numberToWords(inv.total) + " Rupees only");
}

async function generateInvoice() {
  // 1️⃣ Gather invoice items and totals
  const items = [];
  let total = 0;
  const tableRows = [...document.getElementById("itemTable").rows].slice(1);

  tableRows.forEach(r => {
    const i = r.querySelectorAll("input");
    const qty = Number(i[1].value || 0);
    const price = Number(i[2].value || 0);
    const amt = qty * price;
    total += amt;
    items.push({
      name: i[0].value,
      qty,
      price,
      amount: amt
    });
  });

  const received = Number(document.getElementById("receivedInput").value || 0);
  const balance = total - received;

  // 2️⃣ Prepare invoice data
 const invoiceData = {
  id: editIndex,
  clientName: document.getElementById("clientName").value,
  clientAddress: document.getElementById("clientAddress").value,
  clientPhone: document.getElementById("clientPhone").value,
  paymentMode: document.getElementById("paymentMode").value,
    clientEmail: document.getElementById("clientEmail").value, // ✅ ADD THIS
  items,
  total,
  received,
  balance
};

  try {
    // 3️⃣ Save / Update invoice
    const res = await fetch('save_invoice.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(invoiceData)
    });
    const result = await res.json();

   if (result.status === "success") {
    showToast(
      editIndex 
        ? "Invoice updated! Invoice No: " + result.invoice_no
        : "Invoice saved! Invoice No: " + result.invoice_no
    );


      // 4️⃣ Add invoice_no and created_at from server
      const fullInvoice = {
        ...invoiceData,
        invoice_no: result.invoice_no,
        created_at: result.created_at
      };

      // 5️⃣ Show invoice preview section
      showSection('invoicePreview', document.querySelectorAll('.sidebar button')[0]);

      // 6️⃣ Safely render invoice
      renderInvoice(fullInvoice);

      // 7️⃣ Reset form & history
      editIndex = null;
     loadInvoices();
     loadRevenue();
      resetForm();

    } else {
      alert("Error: " + result.msg);
    }
  } catch (err) {
    
  }
}

/* ===== SAFE RENDER FUNCTION ===== */
/* ===== SAFE RENDER FUNCTION ===== */
function renderInvoice(inv) {

  function safeSetText(id, text) {
    const el = document.getElementById(id);
    if (!el) {
      console.warn("Missing element:", id);
      return;
    }
    el.innerText = text ?? '';
  }

  /* MAIN INVOICE */

  safeSetText("iName", inv.clientName);
  safeSetText("iAddress", inv.clientAddress);
  safeSetText("iPhone", inv.clientPhone);
    safeSetText("iEmail", inv.clientEmail);
  safeSetText("iNo", inv.invoice_no);
  safeSetText("iDate", inv.created_at ? inv.created_at.split(' ')[0] : '');

  let html = "";

  if (inv.items && inv.items.length) {
    inv.items.forEach((it, i) => {
      html += `<tr>
        <td>${i + 1}</td>
        <td>${it.name}</td>
        <td>${it.qty}</td>
        <td>₹ ${it.price}</td>
        <td>₹ ${it.amount}</td>
      </tr>`;
    });
  }

  const invoiceItemsEl = document.getElementById("invoiceItems");
  if (invoiceItemsEl) invoiceItemsEl.innerHTML = html;

  safeSetText("subTotal", inv.total);
  safeSetText("grandTotal", inv.total);
  safeSetText("receivedShow", inv.received);
  safeSetText("balance", inv.balance);
  safeSetText("iEmailPreview", inv.clientEmail);
  safeSetText("amountWords", numberToWords(inv.total) + " Rupees only");

  /* ========= PREVIEW ========= */

  safeSetText("iNamePreview", inv.clientName);
  safeSetText("iAddressPreview", inv.clientAddress);
  safeSetText("iPhonePreview", inv.clientPhone);
  safeSetText("iNoPreview", inv.invoice_no);
  safeSetText("iEmailPreview", inv.clientEmail);
  safeSetText("iDatePreview", inv.created_at ? inv.created_at.split(' ')[0] : '');

  const invoiceItemsPreviewEl = document.getElementById("invoiceItemsPreview");
  if (invoiceItemsPreviewEl) invoiceItemsPreviewEl.innerHTML = html;

  safeSetText("subTotalPreview", inv.total);
  safeSetText("grandTotalPreview", inv.total);
  safeSetText("receivedShowPreview", inv.received);
  safeSetText("balancePreview", inv.balance);
  safeSetText("amountWordsPreview", numberToWords(inv.total) + " Rupees only");

  /* PAYMENT MODE */

  safeSetText("paymentModePreview", inv.paymentMode);

}


/* RESET FORM */
function resetForm(){
  document.getElementById("clientName").value="";
  document.getElementById("clientAddress").value="";
  document.getElementById("clientPhone").value="";
  document.getElementById("receivedInput").value="";
  document.getElementById("itemTable").innerHTML=`
    <tr>
      <th>Item</th><th>Qty</th><th>Price</th><th>Amount</th><th></th>
    </tr>`;
}

async function loadInvoices() {
  try {
    const res = await fetch('get_invoices.php');
    const data = await res.json();

    if (!Array.isArray(data)) {
      console.error("Invalid data:", data);
      return;
    }

    const body = document.getElementById("historyBody");
    body.innerHTML = "";

    // Dashboard counters
    let pendingCount = 0, paidCount = 0, partialCount = 0;
    let pendingAmount = 0, paidAmount = 0;

    data.forEach(i => {
      const date = i.created_at ? i.created_at.split(' ')[0] : '';

      // Determine status
      let status = '';
      if (i.received == 0) status = 'pending';
      else if (i.balance == 0) status = 'paid';
      else status = 'partial';

      // Table row
      body.innerHTML += `
<tr data-status="${status}">
<td>${i.invoice_no || ''}</td>
<td>${i.client_name || ''}</td>
<td>${date}</td>
<td>₹ ${i.total || 0}</td>
<td>₹ ${i.received || 0}</td>
<td>₹ ${i.balance || 0}</td>
<td><button onclick="editInvoice(${i.id})">Edit</button></td>
<td><button onclick="deleteInvoice(${i.id})">Delete</button></td>
</tr>`;

      // Update counters
      if (status === 'pending') {
        pendingCount++;
        pendingAmount += parseFloat(i.balance) || 0;
      } else if (status === 'paid') {
        paidCount++;
        paidAmount += parseFloat(i.total) || 0;
      } else if (status === 'partial') {
        partialCount++;
        pendingAmount += parseFloat(i.balance) || 0;
        paidAmount += parseFloat(i.received) || 0;
      }
    });

    // Update dashboard cards
    document.getElementById("pendingInvoices").innerText = pendingCount;
    document.getElementById("paidInvoices").innerText = paidCount;
    document.getElementById("partialInvoices").innerText = partialCount;
    document.getElementById("pendingAmount").innerText = `₹${pendingAmount.toFixed(2)}`;
    document.getElementById("paidAmount").innerText = `₹${paidAmount.toFixed(2)}`;

  } catch (err) {
    console.error("Invoice load error:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadInvoices();
});
// Convert number to words (Indian Rupees style)
function numberToWords(num) {
    const a = ['','One ','Two ','Three ','Four ','Five ','Six ','Seven ','Eight ','Nine ','Ten ',
               'Eleven ','Twelve ','Thirteen ','Fourteen ','Fifteen ','Sixteen ','Seventeen ','Eighteen ','Nineteen '];
    const b = ['','', 'Twenty ','Thirty ','Forty ','Fifty ','Sixty ','Seventy ','Eighty ','Ninety '];

    if ((num = num.toString()).length > 9) return 'Overflow';
    let n = ('000000000' + num).substr(-9).match(/.{1,2}/g); // split into crores, lakhs, thousands, hundreds, tens
    let str = '';
    str += (n[0] != 0) ? (a[Number(n[0])] || (b[n[0][0]] + a[n[0][1]])) + 'Crore ' : '';
    str += (n[1] != 0) ? (a[Number(n[1])] || (b[n[1][0]] + a[n[1][1]])) + 'Lakh ' : '';
    str += (n[2] != 0) ? (a[Number(n[2])] || (b[n[2][0]] + a[n[2][1]])) + 'Thousand ' : '';
    str += (n[3] != 0) ? (a[Number(n[3])] || (b[n[3][0]] + a[n[3][1]])) + 'Hundred ' : '';
    str += (n[4] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[4])] || (b[n[4][0]] + a[n[4][1]])) : '';
    return str.trim();
}

async function deleteInvoice(id){
  if(!confirm("Delete this invoice?")) return;

  try {
    await fetch('delete_invoice.php?id=' + id);
    
    // refresh table instantly
    loadInvoices();

  } catch(err){
    console.error(err);
  }
}
/* EDIT */
async function editInvoice(id){
  try{
    const res = await fetch('get_invoice.php?id='+id);
    const i = await res.json();

    showSection('create', document.querySelectorAll('.sidebar button')[0]);
    editIndex = i.id;
    document.getElementById("clientName").value = i.client_name;
    document.getElementById("clientAddress").value = i.client_address;
    document.getElementById("clientPhone").value = i.client_phone;
    document.getElementById("receivedInput").value = i.received;

    const table = document.getElementById("itemTable");
    table.innerHTML=`<tr>
      <th>Item</th><th>Qty</th><th>Price</th><th>Amount</th><th></th>
    </tr>`;
    i.items.forEach(it=>{
      const r = table.insertRow();
      r.innerHTML=`
        <td><input value="${it.name}"></td>
        <td><input type="number" value="${it.qty}" oninput="calcRow(this)"></td>
        <td><input type="number" value="${it.price}" oninput="calcRow(this)"></td>
        <td class="rowAmount">${it.amount}</td>
        <td><button onclick="this.closest('tr').remove()">X</button></td>`;
    });

  }catch(err){ console.error(err); }
}

</script>
<script>

let revenueChart;
let statusChart;

function loadRevenue(){

fetch('dashboard_data.php')
.then(res=>res.json())
.then(data=>{

const monthNames=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

const months=data.revenue.map(r=>monthNames[r.month-1]);
const revenue=data.revenue.map(r=>r.total);

/* DESTROY OLD CHARTS */

if(revenueChart){
revenueChart.destroy();
}

if(statusChart){
statusChart.destroy();
}

/* BAR CHART */

revenueChart = new Chart(document.getElementById('revenueChart'),{
type:'bar',
data:{
labels:months,
datasets:[{
label:'Monthly Revenue',
data:revenue,
backgroundColor:'#2196f3',
borderRadius:10,
barThickness:40,
hoverBackgroundColor:'#0b78a4'
}]
},
options:{
responsive:true,
animation:{
duration:1200,
easing:'easeOutQuart'
},
plugins:{
legend:{
display:false
},
tooltip:{
callbacks:{
label:function(context){
return "₹ " + Number(context.raw).toLocaleString();
}
}
}
},
scales:{
x:{
grid:{
display:false
}
},
y:{
beginAtZero:true,
ticks:{
callback:function(value){
return "₹ " + value;
}
}
}
}
}
});

/* PIE CHART */

statusChart = new Chart(document.getElementById('statusChart'),{
type:'pie',
data:{
labels:["Paid","Pending"],
datasets:[{
data:[data.paid_invoices,data.pending_invoices],
backgroundColor:["#4caf50","#ff9800"]
}]
}
});

/* DASHBOARD CARDS */
document.getElementById("totalInvoicesCount").innerText = data.total_invoices;
document.getElementById("pendingInvoices").innerText=data.pending_invoices;
document.getElementById("paidInvoices").innerText=data.paid_invoices;

document.getElementById("pendingAmount").innerText="₹"+data.pending_amount;
document.getElementById("paidAmount").innerText="₹"+data.paid_amount;

/* RECENT INVOICES */

let html="";

data.recent.forEach(inv=>{

html+=`
<tr>
<td>${inv.invoice_no}</td>
<td>${inv.client_name}</td>
<td>${inv.created_at.split(' ')[0]}</td>
<td>₹${inv.total}</td>
<td class="${inv.status==='paid'?'status-paid':'status-pending'}">${inv.status}</td>
</tr>
`;

});

document.getElementById("recentInvoices").innerHTML=html;

});

}

</script>
<script>
document.addEventListener("DOMContentLoaded", function(){

const searchInput = document.getElementById("searchInvoice");
const statusFilter = document.getElementById("statusFilter");
const monthFilter = document.getElementById("monthFilter");
const yearFilter = document.getElementById("yearFilter");

if(searchInput){
searchInput.addEventListener("keyup", filterInvoices);
}

if(statusFilter){
statusFilter.addEventListener("change", filterInvoices);
}

if(monthFilter){
monthFilter.addEventListener("change", filterInvoices);
}

if(yearFilter){
yearFilter.addEventListener("change", filterInvoices);
}

});

function filterInvoices(){

let search = document.getElementById("searchInvoice").value.toLowerCase();
let status = document.getElementById("statusFilter").value;
let month = document.getElementById("monthFilter") ? document.getElementById("monthFilter").value : "";
let year = document.getElementById("yearFilter") ? document.getElementById("yearFilter").value : "";

let rows = document.querySelectorAll("#historyBody tr");

rows.forEach(row=>{

let text = row.innerText.toLowerCase();
let rowStatus = row.getAttribute("data-status");

/* DATE COLUMN */
let date = row.cells[2].innerText;
let rowYear = "";
let rowMonth = "";

if(date){
let parts = date.split("-");
rowYear = parts[0];
rowMonth = parts[1];
}

let matchSearch = text.includes(search);
let matchStatus = status=="" || rowStatus==status;
let matchMonth = month=="" || rowMonth==month;
let matchYear = year=="" || rowYear==year;

row.style.display = (matchSearch && matchStatus && matchMonth && matchYear) ? "" : "none";

});

}
</script>
<style>.cards{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:#fff;
padding:20px;
border-radius:8px;
box-shadow:0 2px 8px rgba(0,0,0,0.1);
text-align:center;
}

.card h4{
margin:0;
font-size:16px;
}

.card span{
font-size:28px;
font-weight:bold;
display:block;
margin-top:10px;
}

.pending{border-top:5px solid #ff9800;}
.paid{border-top:5px solid #4caf50;}
.partial{border-top:5px solid #2196f3;}
.overdue{border-top:5px solid #f44336;}

/* Company Logo */
.company-logo {
    width: 200px;              /* Adjust size as needed */
    height: auto;              /* Maintain aspect ratio */
    display: block;            /* Remove inline spacing */
    background-color: #fff;    /* White background for clarity */
    padding: 10px;             /* Adds some space around logo */
    margin: 0 auto 20px auto;  /* Center horizontally, 20px space below */
    object-fit: contain;       /* Ensure logo fits within bounds */
    border-radius: 8px;        /* Slightly rounded corners */
    box-shadow: 0 4px 12px rgba(0,0,0,0.1); /* Subtle shadow for depth */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
}
.status-partial{
color:#2196f3;
font-weight:bold;
}
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #fff;
  padding: 14px 20px;
  border-radius: 10px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 260px;
  font-family: "Segoe UI", sans-serif;
  color: #333;
  opacity: 0;
  transform: translateX(120%);
  transition: all 0.4s ease;
  z-index: 9999;
}

.toast.show {
  opacity: 1;
  transform: translateX(0);
}

.toast-icon {
  font-size: 22px;
  color: #4caf50;
  font-weight: bold;
}

.toast-title {
  font-weight: 600;
  font-size: 16px;
}

.toast-subtitle {
  font-size: 13px;
  color: #666;
  margin-top: 2px;
}
/* ITEM DROPDOWN FIELD */

#itemTable td:first-child input{
width:100%;
padding:9px 12px;
border:1px solid #dcdcdc;
border-radius:6px;
font-size:14px;
background:#fff;
transition:all .25s ease;
}

/* hover effect */

#itemTable td:first-child input:hover{
border-color:#2196f3;
}

/* focus effect */

#itemTable td:first-child input:focus{
border-color:#2196f3;
box-shadow:0 0 6px rgba(33,150,243,0.25);
outline:none;
}

/* placeholder style */

#itemTable td:first-child input::placeholder{
color:#999;
font-size:13px;
}
#iNamePreview{
font-weight:bold;
}
#paymentMode{
width:100%;
padding:12px 14px;
margin-top:10px;
border:1px solid #dcdcdc;
border-radius:8px;
background:#ffffff;
font-size:14px;
color:#333;
cursor:pointer;
transition:all .25s ease;
box-shadow:0 2px 4px rgba(0,0,0,0.05);
}

/* hover */
#paymentMode:hover{
border-color:#2196f3;
box-shadow:0 3px 6px rgba(0,0,0,0.08);
}

/* focus */
#paymentMode:focus{
outline:none;
border-color:#2196f3;
box-shadow:0 0 0 2px rgba(33,150,243,0.15);
}

/* option */
#paymentMode option{
padding:10px;
font-size:14px;
}
</style>
<div id="invoiceToast" class="toast">
  <div class="toast-icon">✔</div>
  <div class="toast-content">
    <div class="toast-title"></div>
    <div class="toast-subtitle">Your invoice has been processed successfully.</div>
  </div>
</div>
<script>function showToast(title, subtitle = "Your invoice has been processed successfully.", duration = 3000) {
    const toast = document.getElementById("invoiceToast");
    toast.querySelector(".toast-title").innerText = title;
    toast.querySelector(".toast-subtitle").innerText = subtitle;

    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, duration);
}</script>
<script>// Example: Dynamic Total Invoices Card
async function loadTotalInvoicesCard() {
  const res = await fetch('get_invoices.php'); // your endpoint
  const invoices = await res.json();
  
  const total = invoices.length || 0;
  document.getElementById('totalInvoicesCount').innerText = total;

  // Simple sparkline for last 7 days invoices
  const dailyCounts = []; // e.g., count invoices per day
  for(let i=6;i>=0;i--){
    const day = new Date();
    day.setDate(day.getDate() - i);
    const count = invoices.filter(inv=>{
      const d = new Date(inv.created_at);
      return d.toDateString() === day.toDateString();
    }).length;
    dailyCounts.push(count);
  }

  const ctx = document.getElementById('totalInvoicesSparkline').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['6d','5d','4d','3d','2d','1d','Today'],
      datasets: [{
        data: dailyCounts,
        borderColor: '#2196f3',
        fill: true,
        backgroundColor: 'rgba(33,150,243,0.1)',
        tension: 0.3,
      }]
    },
    options: {
      plugins:{legend:{display:false}},
      scales:{x:{display:false},y:{display:false}}
    }
  });

  // % Change vs previous 7 days
  const prevWeek = invoices.filter(inv=>{
    const d = new Date(inv.created_at);
    return d >= new Date(Date.now()-14*24*60*60*1000) && d < new Date(Date.now()-7*24*60*60*1000);
  }).length;
  
  const change = prevWeek ? (((total - prevWeek)/prevWeek)*100).toFixed(1) : 100;
  const el = document.getElementById('totalInvoicesChange');
  el.innerText = change>0 ? `↑ ${change}%` : `↓ ${Math.abs(change)}%`;
  el.style.color = change>=0 ? 'green' : 'red';
}

// Call on page load
document.addEventListener("DOMContentLoaded", loadTotalInvoicesCard);</script>
<div id="invoiceContent" style="display:none;">
  <h2>Invoice</h2>
  <p><b>Business Name:</b> <span id="iName"></span></p>
  <p><b>Address:</b> <span id="iAddress"></span></p>
  <p><b>Contact:</b> <span id="iPhone"></span></p>
  <p><b>Email:</b> <span id="iEmail"></span></p>
  <!-- Add your invoice table/details here -->
</div>
<script>function generateAndSendInvoice() {
    // Get values from your inputs
    const name = document.getElementById('clientName').value;
    const address = document.getElementById('clientAddress').value;
    const phone = document.getElementById('clientPhone').value;
    const email = document.getElementById('clientEmail').value;

    // Fill invoice container
    document.getElementById('iName').innerText = name;
    document.getElementById('iAddress').innerText = address;
    document.getElementById('iPhone').innerText = phone;
    document.getElementById('iEmail').innerText = email;

    // Show invoice (optional)
    document.getElementById('invoiceContent').style.display = 'block';

    // Send invoice content to PHP for PDF generation and emailing
    const invoiceHTML = document.getElementById('invoiceContent').innerHTML;
    const formData = new FormData();
    formData.append('invoiceHTML', invoiceHTML);
    formData.append('clientEmail', email);
    formData.append('clientName', name);

    fetch('send_invoice_email.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => alert(response))
    .catch(err => console.error(err));
}</script>
</body>
</html>  