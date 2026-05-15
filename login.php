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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Catering Ibu Iqbal | Premium Taste</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --gold: #D4AF37;
        --dark: #1A1A1D;
        --light: #F9F6F0;
        --white: #FFFFFF;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(rgba(26, 26, 29, 0.85), rgba(26, 26, 29, 0.95)), 
                    url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover fixed;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* CARD */
    .login-box {
        width: 100%;
        max-width: 420px;
        padding: 50px 40px;
        border-radius: 12px;
        background: rgba(26, 26, 29, 0.7);
        border: 1px solid rgba(212, 175, 55, 0.3);
        backdrop-filter: blur(15px);
        color: var(--white);
        text-align: center;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        animation: fadeUp 0.8s ease forwards;
    }

    /* ANIMASI */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .logo {
        font-size: 45px;
        margin-bottom: 15px;
        color: var(--gold);
    }

    .login-box h2 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 30px;
        font-weight: 600;
        font-size: 28px;
        letter-spacing: 1px;
    }

    /* INPUT */
    .input-box {
        position: relative;
        margin: 25px 0;
        text-align: left;
    }

    .input-box i.fa-user, .input-box i.fa-lock {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        font-size: 14px;
        transition: 0.3s;
    }

    .input-box:focus-within i.fa-user,
    .input-box:focus-within i.fa-lock {
        color: var(--gold);
    }

    .input-box input {
        width: 100%;
        padding: 14px 45px;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 8px;
        outline: none;
        background: rgba(0, 0, 0, 0.2);
        color: var(--white);
        font-size: 14px;
        transition: 0.3s;
    }

    .input-box input:focus {
        border-color: var(--gold);
        background: rgba(0, 0, 0, 0.4);
    }

    /* SHOW PASSWORD */
    .show-pass {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
        font-size: 14px;
        transition: 0.3s;
    }
    
    .show-pass:hover {
        color: var(--white);
    }

    /* BUTTON */
    button {
        width: 100%;
        padding: 14px;
        margin-top: 10px;
        border: 1px solid var(--gold);
        border-radius: 8px;
        background: var(--gold);
        color: var(--white);
        font-weight: 500;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s;
    }

    button:hover {
        background: transparent;
        color: var(--gold);
    }

    /* ERROR */
    .error {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #ff6b6b;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    /* LINK */
    .link {
        margin-top: 25px;
        font-size: 13px;
        color: #AAA;
    }

    .link a {
        color: var(--gold);
        text-decoration: none;
        transition: 0.3s;
        font-weight: 500;
    }

    .link a:hover {
        color: var(--white);
    }

    .back-link {
        margin-top: 20px;
        display: inline-block;
    }
</style>
</head>

<body>

<div class="login-box">

    <div class="logo">
        <i class="fas fa-utensils"></i>
    </div>

    <h2>Selamat Datang</h2>

    <?php if($error){ ?>
        <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error; ?></div>
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

        <button name="login">Masuk ke Akun</button>

    </form>

    <div class="link">
        <p>Belum memiliki akun? <a href="daftar.php">Daftar Sekarang</a></p>
    </div>
    
    <div class="link back-link">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
    </div>

</div>

<script>
function togglePass(){
    var x = document.getElementById("pass");
    var icon = document.querySelector(".show-pass");
    
    if(x.type === "password"){
        x.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        x.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>