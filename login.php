<?php
session_start();
include 'koneksi.php';

$error = "";

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $data = mysqli_query($conn,"SELECT * FROM users WHERE username='$user'");
    $cek = mysqli_num_rows($data);

    if($cek > 0){
        $d = mysqli_fetch_assoc($data);
        
        // Memeriksa password yang di-hash atau password plaintext lama
        if(password_verify($pass, $d['password']) || $d['password'] === $pass) {
            
            // Jika password masih plaintext, lakukan pembaruan menjadi hash
            if($d['password'] === $pass) {
                $new_hash = password_hash($pass, PASSWORD_DEFAULT);
                $id_user = $d['id'];
                mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='$id_user'");
            }

            $_SESSION['login'] = true;
            $_SESSION['role'] = $d['role'];
            $_SESSION['id'] = $d['id'];

            if($d['role']=="admin"){
                header("location:admin/index.php");
            }elseif($d['role']=="petugas"){
                header("location:petugas/index.php");
            }else{
                header("location:pengunjung/index.php");
            }
        }else{
            $error = "Username atau Password salah!";
        }
    }else{
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Catering Ibu Iqbal</title>

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
.login-box{
    width:360px;
    padding:40px;
    border-radius:25px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(20px);
    color:white;
    text-align:center;
    box-shadow:0 0 40px rgba(255,165,0,0.3);
    animation:fade 1s ease;
}

/* ANIMASI */
@keyframes fade{
    from{opacity:0; transform:translateY(40px);}
    to{opacity:1; transform:translateY(0);}
}

.login-box h2{
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
    left:10px;
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
    right:10px;
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
    background:linear-gradient(45deg,orange,red);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
    box-shadow:0 0 15px orange;
}

/* ERROR */
.error{
    background:rgba(255,0,0,0.7);
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

/* TITLE ICON */
.logo{
    font-size:40px;
    margin-bottom:10px;
    color:orange;
}
</style>
</head>

<body>

<div class="login-box">

    <div class="logo">
        <i class="fas fa-utensils"></i>
    </div>

    <h2>Login</h2>

    <?php if($error){ ?>
        <div class="error"><?= $error; ?></div>
    <?php } ?>

    <form method="POST">

        <div class="input-box">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-box">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="pass" placeholder="Password" required>
            <i class="fas fa-eye show-pass" onclick="togglePass()"></i>
        </div>

        <button name="login">Masuk</button>

    </form>

    <div class="link">
        <p><a href="index.php">← Kembali ke Beranda</a></p>
    </div>

    <div class="link">
        <p>Belum punya akun? <a href="daftar.php">Daftar</a></p>
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