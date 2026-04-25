<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="0.5;url=login.php">
<style>
body{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    background:#141e30;
    color:white;
    font-family:Poppins;
}
.loader{
    width:40px;
    height:40px;
    border:4px solid #ccc;
    border-top:4px solid #00c6ff;
    border-radius:50%;
    animation:spin 0.7s linear infinite;
}
@keyframes spin{
    100%{transform:rotate(360deg);}
}
</style>
</head>

<body>
<div class="loader"></div>
</body>
</html>