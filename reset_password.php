<?php 
session_start(); 
if(!isset($_SESSION['reset_email'])) { 
    header("Location: index.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Barangay Igpit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3799;
            --bg-color: #f4f7f6;
            --text-dark: #2d3436;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-color); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }

        .card { 
            background: white; 
            padding: 50px 40px; 
            border-radius: 30px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.05); 
            max-width: 420px; 
            width: 90%; 
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 { 
            color: var(--primary-blue); 
            font-weight: 800; 
            margin-top: 0;
            letter-spacing: -0.5px;
        }

        p { 
            color: #636e72; 
            font-size: 14px; 
            margin-bottom: 30px; 
        }

        .input-group {
            text-align: left;
            margin-bottom: 15px;
            position: relative; /* Added for icon positioning */
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #b2bec3;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        input { 
            width: 100%; 
            padding: 15px; 
            padding-right: 45px; /* Space for the eye icon */
            border-radius: 12px; 
            border: 2px solid #edf2f7; 
            background: #fdfdfd;
            box-sizing: border-box; 
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus { 
            outline: none; 
            border-color: var(--primary-blue); 
            background: #fff;
            box-shadow: 0 5px 15px rgba(30, 55, 153, 0.1);
        }

        /* Eye Icon Style */
        .toggle-password {
            position: absolute;
            right: 15px;
            bottom: 15px;
            cursor: pointer;
            color: #b2bec3;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: var(--primary-blue);
        }

        .btn { 
            background: var(--primary-blue); 
            color: white; 
            padding: 16px; 
            border: none; 
            border-radius: 15px; 
            width: 100%; 
            font-weight: 800; 
            cursor: pointer; 
            margin-top: 15px; 
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(30, 55, 153, 0.2);
        }

        .btn:hover { 
            background: #152b7a; 
            transform: translateY(-3px); 
            box-shadow: 0 15px 25px rgba(30, 55, 153, 0.3);
        }

        .icon-header {
            font-size: 50px;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-header">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2>New Password</h2>
        <p>Set a strong password to keep your account secure!</p>
        
        <form action="update_password.php" method="POST">
            <div class="input-group">
                <label>Enter New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="••••••••" required minlength="6">
                <i class="fa-solid fa-eye toggle-password" onclick="togglePass('new_password', this)"></i>
            </div>
            
            <div class="input-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" required minlength="6">
                <i class="fa-solid fa-eye toggle-password" onclick="togglePass('confirm_password', this)"></i>
            </div>
            
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>

    <script>
    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text"; // Makita na ang password
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash"); // Naay kuros pasabot "visible"
        } else {
            input.type = "password"; // Mahimong dots balik
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye"); // Mobalik sa normal nga mata
        }
    }
</script>
</body>
</html>