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
                // Pengunjung: Cek redirect parameter
                if(isset($_POST['redirect']) && !empty($_POST['redirect'])){
                    $redirect_url = $_POST['redirect'];
                    // Konversi redirect jika merujuk ke beli.php (checkout tamu)
                    if(strpos($redirect_url, 'beli.php') !== false){
                        $redirect_url = str_replace('beli.php', 'pengunjung/pesan.php', $redirect_url);
                    }
                    header("location:" . $redirect_url);
                } else {
                    header("location:pengunjung/index.php");
                }
            }
            exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --gold: #D4AF37;
            --gold-dark: #AA8C2C;
            --gold-light: rgba(212, 175, 55, 0.2);
            --dark: #111112;
            --white: #FFFFFF;
            --glass-bg: rgba(17, 17, 18, 0.7);
            --glass-border: rgba(212, 175, 55, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(rgba(17, 17, 18, 0.82), rgba(17, 17, 18, 0.94)), 
                        url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* CARD GLASSMORPHISM */
        .login-box {
            width: 100%;
            max-width: 440px;
            padding: 55px 45px;
            border-radius: 24px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: var(--white);
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 
                        inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            animation: fadeUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
        }

        /* ANIMASI */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: var(--gold-light);
            border: 1px solid var(--gold);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2);
            transition: all 0.4s ease;
        }

        .logo-container:hover {
            transform: rotate(15deg) scale(1.05);
            background: rgba(212, 175, 55, 0.3);
        }

        .logo-container i {
            font-size: 32px;
            color: var(--gold);
        }

        .login-box h2 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 30px;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .subtitle {
            font-size: 0.85rem;
            color: #b0b0b5;
            margin-bottom: 35px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* INPUTS */
        .input-box {
            position: relative;
            margin-bottom: 22px;
            text-align: left;
        }

        .input-box i.input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8c8c93;
            font-size: 15px;
            transition: 0.3s ease;
        }

        .input-box input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            outline: none;
            background: rgba(0, 0, 0, 0.35);
            color: var(--white);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-box input::placeholder {
            color: #6c6c73;
        }

        .input-box input:focus {
            border-color: var(--gold);
            background: rgba(0, 0, 0, 0.55);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.15);
        }

        .input-box:focus-within i.input-icon {
            color: var(--gold);
        }

        /* Autofill Overrides */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px rgba(17, 17, 18, 0.95) inset !important;
            -webkit-text-fill-color: #ffffff !important;
            border: 1.5px solid rgba(212, 175, 55, 0.3) !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* SHOW PASSWORD */
        .show-pass {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8c8c93;
            font-size: 15px;
            transition: 0.3s ease;
            z-index: 10;
        }
        
        .show-pass:hover {
            color: var(--gold);
        }

        /* BUTTON */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 15px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--white) 0%, #e0e0e0 100%);
            color: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.15);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ERROR */
        .error-alert {
            background: rgba(238, 77, 45, 0.1);
            border: 1px solid rgba(238, 77, 45, 0.3);
            color: #ff6b6b;
            padding: 14px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* LINKS */
        .links-container {
            margin-top: 35px;
            font-size: 0.85rem;
            color: #a0a0a5;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .links-container a {
            color: var(--gold);
            text-decoration: none;
            transition: 0.3s ease;
            font-weight: 600;
        }

        .links-container a:hover {
            color: var(--white);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }

        .back-link {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="logo-container">
        <i class="fas fa-utensils"></i>
    </div>

    <h2>Selamat Datang</h2>
    <div class="subtitle">Ibu Iqbal Catering</div>

    <?php if($error){ ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i> 
            <span><?= $error; ?></span>
        </div>
    <?php } ?>

    <form method="POST" autocomplete="off">
        <!-- Dummy inputs to prevent browser autofill/autocomplete on load -->
        <input type="text" name="prevent_autofill_user" style="position: absolute; top: -9999px; left: -9999px;" aria-hidden="true" tabindex="-1">
        <input type="password" name="prevent_autofill_pass" style="position: absolute; top: -9999px; left: -9999px;" aria-hidden="true" tabindex="-1">

        <!-- Input hidden to pass redirect target url -->
        <?php if(isset($_GET['redirect'])){ ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']); ?>">
        <?php } elseif(isset($_POST['redirect'])) { ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_POST['redirect']); ?>">
        <?php } ?>

        <div class="input-box">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
        </div>

        <div class="input-box">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="password" id="pass" placeholder="Password" required>
            <i class="fas fa-eye show-pass" onclick="togglePass()"></i>
        </div>

        <button type="submit" name="login" class="btn-login">Masuk ke Akun</button>
    </form>

    <div class="links-container">
        <p>Belum memiliki akun? <a href="daftar.php">Daftar Sekarang</a></p>
        <div class="back-link">
            <a href="index.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
        </div>
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