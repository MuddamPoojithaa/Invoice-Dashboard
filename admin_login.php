<?php
session_start();

if(isset($_POST['login'])){

$username = $_POST['username'];
$password = $_POST['password'];

if($username=="sumahi" && $password=="@2026"){

$_SESSION['admin_logged_in']=true;
header("Location: dashboard.php");
exit;

}else{
$error="Invalid Username or Password";
}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Sumahi Invoice Admin Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
box-sizing:border-box;
margin:0;
padding:0;
font-family:'Poppins',sans-serif;
}

/* Background */

body{
background:linear-gradient(135deg,#1d2671,#c33764);
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
}

/* Login Box */

.login-box{
background:white;
padding:28px 26px;
width:380px;
max-width:90%;
border-radius:14px;
box-shadow:0 20px 45px rgba(0,0,0,0.25);
text-align:center;
animation:fadeIn 0.6s ease;
}

/* Animation */

@keyframes fadeIn{
from{
opacity:0;
transform:translateY(20px);
}
to{
opacity:1;
transform:translateY(0);
}
}

/* Logo */

.logo{
width:150px;
margin-bottom:10px;
display:block;
margin-left:auto;
margin-right:auto;
}

/* Title */

.login-box h2{
font-size:20px;
font-weight:600;
color:#222;
margin-bottom:15px;
}

/* Input Group */

.input-group{
position:relative;
margin-bottom:14px;
}

/* Inputs */

.input-group input{
width:100%;
padding:11px 42px;
border:1px solid #ddd;
border-radius:8px;
font-size:13px;
transition:0.3s;
}

.input-group input:focus{
border-color:#1d2671;
outline:none;
box-shadow:0 0 6px rgba(29,38,113,0.25);
}

/* Icons */

.input-group i{
position:absolute;
top:50%;
transform:translateY(-50%);
color:#777;
font-size:13px;
}

.fa-user,
.fa-lock{
left:12px;
}

.toggle{
right:12px;
cursor:pointer;
}

/* Button */

button{
width:100%;
padding:11px;
background:#1d2671;
border:none;
color:white;
font-size:14px;
border-radius:8px;
cursor:pointer;
transition:0.3s;
font-weight:500;
}

button:hover{
background:#0f1445;
transform:translateY(-1px);
}

/* Error */

.error{
color:red;
font-size:12px;
margin-bottom:10px;
}

/* Footer */

.footer{
margin-top:15px;
font-size:11px;
color:#777;
}

/* Mobile */

@media(max-width:500px){

.login-box{
padding:24px 20px;
}

.logo{
width:130px;
}

}

</style>

</head>

<body>

<div class="login-box">

<img src="logo.png" class="logo">

<h2>Admin Login</h2>

<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">

<div class="input-group">
<i class="fa fa-user"></i>
<input type="text" name="username" placeholder="Username" required>
</div>

<div class="input-group">
<i class="fa fa-lock"></i>
<input type="password" id="password" name="password" placeholder="Password" required>
<i class="fa fa-eye toggle" onclick="togglePassword()"></i>
</div>

<button type="submit" name="login">Login</button>

</form>

<div class="footer">
© 2026 Sumahi Media Pvt Ltd
</div>

</div>

<script>

function togglePassword(){

let pass=document.getElementById("password");
let icon=document.querySelector(".toggle");

if(pass.type==="password"){
pass.type="text";
icon.classList.remove("fa-eye");
icon.classList.add("fa-eye-slash");
}else{
pass.type="password";
icon.classList.remove("fa-eye-slash");
icon.classList.add("fa-eye");
}

}

</script>

</body>
</html> 