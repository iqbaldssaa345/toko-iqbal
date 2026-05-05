<?php
include 'koneksi.php';

$notif = "";

if(isset($_POST['daftar'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // CEK USER SUDAH ADA
    $cek = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0){
        $notif = "<div class='error'>Username sudah digunakan!</div>";
    } else {
        mysqli_query($conn,"INSERT INTO users (username,password,role) VALUES ('$username','$password','pengunjung')");
        $notif = "<div class='success'>Berhasil daftar! <a href='login.php'>Login sekarang</a></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Daftar - Toko Ibu Iqbal</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/4adad1b6d6.js" crossorigin="anonymous"></script>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins;
}

body{
    height:100vh;
    background:linear-gradient(135deg,#000000cc,#000000cc),
    url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.box{
    width:370px;
    padding:40px;
    border-radius:25px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(20px);
    color:white;
    text-align:center;
    box-shadow:0 0 40px rgba(0,255,150,0.3);
    animation:fade 1s ease;
}

/* ANIMASI */
@keyframes fade{
    from{opacity:0; transform:translateY(40px);}
    to{opacity:1; transform:translateY(0);}
}

.box h2{
    margin-bottom:20px;
    font-weight:600;
}

/* INPUT */
.input-box{
    position:relative;
    margin:20px 0;
}

.input-box i{
    position:absolute;
    left:12px;
    top:50%;
    transform:translateY(-50%);
    color:#aaa;
}

.input-box input{
    width:100%;
    padding:12px 40px;
    border:none;
    border-radius:30px;
    outline:none;
    background:rgba(255,255,255,0.1);
    color:white;
}

/* SHOW PASSWORD */
.show-pass{
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:30px;
    background:linear-gradient(45deg,#00ff99,#00ccff);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
    box-shadow:0 0 15px #00ffcc;
}

/* NOTIF */
.error{
    background:rgba(255,0,0,0.7);
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}

.success{
    background:rgba(0,255,100,0.7);
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}

/* LINK */
.link{
    margin-top:15px;
}

.link a{
    color:#ffd;
    text-decoration:none;
}

/* ICON ATAS */
.logo{
    font-size:40px;
    margin-bottom:10px;
    color:#00ffcc;
}
</style>
</head>

<body>

<div class="box">

    <div class="logo">
        <i class="fas fa-user-plus"></i>
    </div>

    <h2>Daftar Akun</h2>

    <?= $notif; ?>

    <form method="POST">

        <div class="input-box">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-box">
            <i class="fas fa-lock"></i>
            <input type="password" id="pass" name="password" placeholder="Password" required>
            <i class="fas fa-eye show-pass" onclick="togglePass()"></i>
        </div>

        <button name="daftar">Daftar Sekarang</button>

    </form>
     <div class="link">
        <p><a href="index.php">← Kembali ke Beranda</a></p>
    </div>


    <div class="link">
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>

</div>

<script>
function togglePass(){
    var x = document.getElementById("pass");
    if(x.type === "password"){
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>

</body>
</html>