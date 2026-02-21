<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Barangay Igpit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); max-width: 400px; width: 90%; text-align: center; animation: fadeIn 0.6s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        input { width: 100%; padding: 15px; margin: 20px 0; border-radius: 12px; border: 2px solid #edf2f7; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: #1e3799; }
        .btn { background: #1e3799; color: white; padding: 15px; border: none; border-radius: 12px; width: 100%; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #152b7a; transform: translateY(-2px); }
        .back { display: block; margin-top: 20px; color: #636e72; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <i class="fa-solid fa-user-lock" style="font-size: 50px; color: #1e3799; margin-bottom: 20px;"></i>
        <h2>Forgot Password?</h2>
        <p style="color: #636e72; font-size: 14px;">I-verify nato imong email para ma-reset imong password, mego.</p>
        
        <form action="verify_email.php" method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <button type="submit" class="btn">Verify Email</button>
        </form>
        
        <a href="index.php" class="back">← Balik sa Login</a>
    </div>
</body>
</html>