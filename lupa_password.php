<?php
session_start();
include 'koneksi.php';

$error = "";
$success = "";

if(isset($_POST['reset_password'])){
    $user = trim($_POST['username']);
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if(empty($user) || empty($new_pass) || empty($confirm_pass)){
        $error = "Semua bidang wajib diisi!";
    } elseif($new_pass !== $confirm_pass){
        $error = "Konfirmasi password tidak cocok!";
    } elseif(strlen($new_pass) < 4){
        $error = "Password minimal terdiri dari 4 karakter!";
    } else {
        $user_clean = mysqli_real_escape_string($conn, $user);
        $data = mysqli_query($conn, "SELECT id, username, role FROM users WHERE username='$user_clean'");
        
        if(mysqli_num_rows($data) > 0){
            $d = mysqli_fetch_assoc($data);
            $user_id = $d['id'];
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);

            $update = mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='$user_id'");
            if($update){
                $success = "Password untuk pengguna <strong>" . htmlspecialchars($d['username']) . "</strong> (" . strtoupper($d['role']) . ") berhasil diperbarui!";
            } else {
                $error = "Gagal memperbarui password. Silakan coba lagi!";
            }
        } else {
            $error = "Username tidak ditemukan dalam sistem!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Catering Ibu Iqbal</title>

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
            --gold-light: rgba(212, 175, 55, 0.12);
            --gold-glow: rgba(212, 175, 55, 0.3);
            --dark: #0F0F11;
            --white: #FFFFFF;
            --glass-bg: rgba(15, 15, 17, 0.78);
            --glass-border: rgba(212, 175, 55, 0.2);
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(rgba(15, 15, 17, 0.8), rgba(15, 15, 17, 0.95)), 
                        url('https://i.pinimg.com/736x/d1/1d/62/d11d62838e3facfa6e3c030fa975c6d0.jpg') center/cover fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* CARD GLASSMORPHISM */
        .login-box {
            width: 100%;
            max-width: 450px;
            padding: 45px 40px;
            border-radius: 24px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: var(--white);
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 
                        inset 0 0 0 1px rgba(255, 255, 255, 0.03);
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            width: 75px;
            height: 75px;
            background: var(--gold-light);
            border: 1px solid var(--gold);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.15);
            transition: var(--transition);
        }

        .logo-container:hover {
            transform: rotate(15deg) scale(1.05);
            background: rgba(212, 175, 55, 0.25);
        }

        .logo-container i {
            font-size: 30px;
            color: var(--gold);
        }

        .login-box h2 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 6px;
            font-weight: 700;
            font-size: 28px;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .subtitle {
            font-size: 0.82rem;
            color: #b0b0b5;
            margin-bottom: 30px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* INPUTS */
        .input-box {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-box i.input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8c8c93;
            font-size: 15px;
            transition: var(--transition);
        }

        .input-box input {
            width: 100%;
            padding: 14px 15px 14px 50px;
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            outline: none;
            background: rgba(0, 0, 0, 0.35);
            color: var(--white);
            font-size: 14px;
            transition: var(--transition);
        }

        .input-box input::placeholder {
            color: #6c6c73;
        }

        .input-box input:focus {
            border-color: var(--gold);
            background: rgba(0, 0, 0, 0.55);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);
        }

        .input-box:focus-within i.input-icon {
            color: var(--gold);
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
            transition: var(--transition);
            z-index: 10;
        }
        
        .show-pass:hover {
            color: var(--gold);
        }

        /* BUTTON */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f1e2c3 100%);
            color: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.35);
        }

        /* ALERTS */
        .error-alert {
            background: rgba(238, 77, 45, 0.1);
            border: 1px solid rgba(238, 77, 45, 0.3);
            color: #ff6b6b;
            padding: 12px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
        }

        .success-alert {
            background: rgba(55, 178, 77, 0.12);
            border: 1px solid rgba(55, 178, 77, 0.3);
            color: #51cf66;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            text-align: center;
            line-height: 1.5;
        }

        /* LINKS */
        .links-container {
            margin-top: 25px;
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
            padding-top: 18px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="logo-container">
        <i class="fas fa-key"></i>
    </div>

    <h2>Lupa Password</h2>
    <div class="subtitle">Reset Password Akun Anda</div>

    <?php if($error){ ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-circle"></i> 
            <span><?= $error; ?></span>
        </div>
    <?php } ?>

    <?php if($success){ ?>
        <div class="success-alert">
            <i class="fas fa-check-circle d-block mb-2" style="font-size: 1.8rem;"></i>
            <div><?= $success; ?></div>
            <a href="login.php" class="btn-login" style="display: block; text-decoration: none; margin-top: 15px; padding: 12px;">
                <i class="fas fa-sign-in-alt me-1"></i> Ke Halaman Login
            </a>
        </div>
    <?php } else { ?>

        <form method="POST" autocomplete="off">
            <div class="input-box">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" placeholder="Masukkan Username Anda" required autocomplete="off">
            </div>

            <div class="input-box">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="new_password" id="pass1" placeholder="Password Baru" required>
                <i class="fas fa-eye show-pass" onclick="togglePass('pass1', this)"></i>
            </div>

            <div class="input-box">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="confirm_password" id="pass2" placeholder="Konfirmasi Password Baru" required>
                <i class="fas fa-eye show-pass" onclick="togglePass('pass2', this)"></i>
            </div>

            <button type="submit" name="reset_password" class="btn-login">Simpan Password Baru</button>
        </form>

    <?php } ?>

    <div class="links-container">
        <p>Sudah ingat password? <a href="login.php">Kembali ke Login</a></p>
        <div class="back-link">
            <a href="index.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
        </div>
    </div>

</div>

<script>
function togglePass(id, icon){
    var x = document.getElementById(id);
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
