<?php 
include 'db.php'; 

$message = "";
$message_type = "";

if(isset($_POST['register'])){
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 

    // 1. PASSWORD HASHING (Mao ni ang gipangita sa imong instructor)
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    
    // 2. ALTERNATIVE LOGIC FOR SCHOOL DEMO (Email-based Roles)
    $role = 'resident'; 

    if (strpos($email, '@igpit.admin') !== false) {
        $role = 'admin';
    } 
    elseif (strpos($email, '@igpit.staff') !== false) {
        $role = 'staff';
    }

    // 3. Check if email already exists
    $check_email = $conn->query("SELECT id FROM users WHERE email='$email'");
    
    if($check_email->num_rows > 0){
        $message = "Email is already registered!";
        $message_type = "error-msg";
    } else {
        // 4. INSERT: Gamiton ang $hashed_pass para secure sa database
        $sql = "INSERT INTO users (full_name, email, password, role) 
                VALUES ('$full_name', '$email', '$hashed_pass', '$role')";
        
        if($conn->query($sql) === TRUE){
            $message = "Registration successful as " . strtoupper($role) . "! You can now login.";
            $message_type = "success-msg";
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "error-msg";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Inter', 'Segoe UI', sans-serif; overflow: hidden; }
        .container { display: flex; width: 1050px; height: 650px; background: #fff; border-radius: 30px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .image-side { flex: 1.3; background: linear-gradient(rgba(30, 55, 153, 0.2), rgba(30, 55, 153, 0.4)), url('bglogo.jpg') no-repeat center; background-size: cover; display: flex; align-items: flex-end; padding: 50px; }
        .image-overlay h2 { color: white; font-size: 38px; margin: 0; text-transform: uppercase; font-weight: 900; text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .image-overlay p { color: white; font-size: 20px; margin: 5px 0 0; opacity: 0.9; }
        .signup-side { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; background: white; }
        .logo-wrapper { text-align: center; margin-bottom: 20px; }
        .brand-logo { width: 100px; height: auto; filter: drop-shadow(0 8px 15px rgba(0,0,0,0.1)); }
        h2.welcome-text { text-align: center; color: #1e3799; margin: 0 0 20px; font-size: 22px; font-weight: 800; text-transform: uppercase; }
        .input-group { margin-bottom: 15px; position: relative; }
        .input-group label { display: block; font-size: 11px; font-weight: 700; color: #7f8c8d; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 1px; }
        input { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #f1f1f1; background: #f8f9fa; box-sizing: border-box; transition: all 0.3s ease; font-size: 14px; }
        input:focus { outline: none; border-color: #1e3799; background: #fff; box-shadow: 0 5px 15px rgba(30, 55, 153, 0.1); }
        
        /* Mata Icon Styling */
        .toggle-password { position: absolute; right: 15px; bottom: 12px; cursor: pointer; color: #bdc3c7; transition: 0.3s; }
        .toggle-password:hover { color: #1e3799; }

        .reg-btn { width: 100%; background: #1e3799; color: white; padding: 14px; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 10px; transition: 0.3s; font-size: 15px; text-transform: uppercase; }
        .reg-btn:hover { background: #152b7a; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(30, 55, 153, 0.3); }
        .login-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #7f8c8d; font-size: 13px; font-weight: 600; }
        .login-link b { color: #1e3799; }
        .error-msg, .success-msg { padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
        .error-msg { background: #fff5f5; color: #c0392b; border: 1px solid #feb2b2; }
        .success-msg { background: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }
        .demo-hint { font-size: 10px; color: #a0aec0; text-align: center; margin-top: 15px; font-style: italic; }
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
        <div class="signup-side">
            <div class="logo-wrapper">
                <img src="removelogo.png" alt="Logo" class="brand-logo">
            </div>
            <h2 class="welcome-text">Create Account</h2>
            <?php if($message != ""): ?>
                <div class="<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name"required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email"required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password"required minlength="6">
                    <i class="fa-solid fa-eye toggle-password" id="eyeIcon"></i>
                </div>
                <button type="submit" name="register" class="reg-btn">Register Account</button>
            </form>
            <a href="index.php" class="login-link">Already have an account? <b>Login here</b></a>
            <div class="demo-hint">Use @igpit.admin for Admin or @igpit.staff for Staff roles.</div>
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
    </script>
</body>
</html>