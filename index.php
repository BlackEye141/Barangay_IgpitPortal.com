<?php 
session_start(); 
include 'db.php'; 

// Redirect base sa role kung naka-login na
if(isset($_SESSION['user_email'])){
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['role'] == 'staff') {
        header("Location: staff_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$error = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; // Ayaw i-escape ang password direkta para dili ma-alter ang string sa verify
    
    // 1. Fetch the user based on email only
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    
    if($result->num_rows > 0){
        $user_data = $result->fetch_assoc();
        
        // 2. I-verify ang gi-type nga password batok sa hashed password sa database
        if (password_verify($pass, $user_data['password'])) {
            // SUCCESSFUL LOGIN
            $_SESSION['user_email'] = $user_data['email'];
            $_SESSION['full_name'] = $user_data['full_name'];
            $_SESSION['role'] = $user_data['role']; 
            
            if ($user_data['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user_data['role'] == 'staff') {
                header("Location: staff_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            // Password did not match
            $error = "The password is wrong! Please check back.";
        }
    } else {
        // Email does not exist
        $error = "We don't even have an email on our record!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .container { display: flex; width: 1050px; height: 650px; background: #fff; border-radius: 30px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .image-side { flex: 1.3; background: linear-gradient(rgba(30, 55, 153, 0.2), rgba(30, 55, 153, 0.4)), url('bglogo.jpg') no-repeat center; background-size: cover; display: flex; align-items: flex-end; padding: 50px; }
        .image-overlay h2 { color: white; font-size: 38px; margin: 0; text-transform: uppercase; font-weight: 900; text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .image-overlay p { color: white; font-size: 20px; margin: 5px 0 0; opacity: 0.9; }
        .login-side { flex: 1; padding: 60px; display: flex; flex-direction: column; justify-content: center; background: white; }
        .logo-wrapper { text-align: center; margin-bottom: 20px; }
        .brand-logo { width: 110px; height: auto; filter: drop-shadow(0 8px 15px rgba(0,0,0,0.1)); }
        h2.welcome-text { text-align: center; color: #1e3799; margin: 10px 0 25px; font-size: 20px; font-weight: 800; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 11px; font-weight: 700; color: #7f8c8d; text-transform: uppercase; margin-bottom: 6px; }
        input { width: 100%; padding: 14px; border-radius: 12px; border: 2px solid #f1f1f1; background: #f8f9fa; box-sizing: border-box; transition: 0.3s; font-size: 14px; }
        input:focus { outline: none; border-color: #1e3799; background: #fff; box-shadow: 0 5px 15px rgba(30, 55, 153, 0.1); }
        .password-container { position: relative; }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #bdc3c7; transition: 0.3s; }
        .login-btn { width: 100%; background: #1e3799; color: white; padding: 16px; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 10px; font-size: 15px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .login-btn:hover { background: #152b7a; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(30, 55, 153, 0.3); }
        .error-msg { background: #fff5f5; color: #c0392b; padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-size: 13px; font-weight: 600; border: 1px solid #feb2b2; animation: shake 0.4s ease; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-side">
            <div class="image-overlay">
                <h2>Barangay Igpit</h2>
                <p>Opol, Misamis Oriental</p>
            </div>
        </div>
        <div class="login-side">
            <div class="logo-wrapper">
                <img src="removelogo.png" alt="removelogo" class="brand-logo">
            </div>
            <h2 class="welcome-text">RESIDENT PORTAL</h2>
            <?php if($error != ""): ?>
                <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" placeholder="••••••••" required>
                        <i class="fa-solid fa-eye toggle-password" id="eyeIcon"></i>
                    </div>
                    <a href="forgot_password.php" style="display:block; text-align:right; margin-top:8px; font-size:12px; color:#1e3799; text-decoration:none; font-weight:700;">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="login-btn" id="loginBtn">Sign In</button>
            </form>
            <a href="signup.php" style="display:block; text-align:center; margin-top:25px; text-decoration:none; color:#7f8c8d; font-size:13px; font-weight:600;">New resident? <b style="color:#1e3799;">Create an Account</b></a>
        </div>
    </div>
    <script>
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        eyeIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('#loginBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>