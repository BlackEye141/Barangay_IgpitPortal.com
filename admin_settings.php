<?php
session_start();


if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';
$email = $_SESSION['user_email'];
$full_name = $_SESSION['full_name'];
$swal_msg = "";
$swal_type = "";


if (isset($_POST['update_pass'])) {
    $hashed_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    if ($conn->query("UPDATE users SET password = '$hashed_pass' WHERE email = '$email'")) {
        $swal_msg = "Administrator password has been updated successfully!";
        $swal_type = "success";
    }
}


if (isset($_POST['reset_system'])) {
    if ($conn->query("TRUNCATE TABLE document_requests")) {
        $swal_msg = "System Reset: All document requests have been cleared!";
        $swal_type = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-blue: #1e3799;
            --bg-light: #f4f7f6;
            --accent-blue: #3498DB;
            --danger-red: #e74c3c;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--bg-light); color: #2c3e50; }

        header { 
            background: #fff; padding: 10px 40px; display: flex; 
            justify-content: space-between; align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; 
            top: 0; z-index: 1000; border-bottom: 3px solid var(--sidebar-blue);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { height: 50px; }
        .logo-section h1 { font-size: 20px; margin: 0; color: var(--sidebar-blue); text-transform: uppercase; font-weight: 800; }

        .container { display: grid; grid-template-columns: 280px 1fr; padding: 30px; gap: 30px; }

       
        .sidebar { background: #2C3E50; border-radius: 20px; padding: 25px 15px; display: flex; flex-direction: column; gap: 8px; color: white; min-height: 70vh; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; color: #bdc3c7; font-size: 14px; margin-top: 0; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; transition: 0.3s; font-weight: 500; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: white; transform: translateX(5px); }
        .nav-link.active { background: var(--accent-blue); color: white; }

        
        .card { background: white; padding: 35px; border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); max-width: 600px; }
        .card h2 { margin-top: 0; color: var(--sidebar-blue); margin-bottom: 25px; font-size: 22px; display: flex; align-items: center; gap: 12px; }
        
        .input-group { position: relative; margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; font-weight: 700; font-size: 12px; color: #7f8c8d; text-transform: uppercase; }
        input { width: 100%; padding: 14px; padding-right: 45px; border-radius: 12px; border: 2px solid #f1f1f1; background: #f8f9fa; outline: none; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--sidebar-blue); background: #fff; box-shadow: 0 5px 15px rgba(30, 55, 153, 0.1); }
        
        .toggle-password { position: absolute; right: 15px; bottom: 14px; cursor: pointer; color: #bdc3c7; transition: 0.3s; }
        .toggle-password:hover { color: var(--sidebar-blue); }

        .btn-primary { background: var(--sidebar-blue); color: white; padding: 14px; border: none; border-radius: 12px; width: 100%; cursor: pointer; font-weight: 800; text-transform: uppercase; transition: 0.3s; }
        .btn-primary:hover { background: #152b7a; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(30, 55, 153, 0.2); }

        .btn-danger { background: #fff5f5; color: var(--danger-red); border: 2px solid #fed7d7; padding: 14px; border-radius: 12px; width: 100%; cursor: pointer; font-weight: 800; margin-top: 10px; transition: 0.3s; text-transform: uppercase; }
        .btn-danger:hover { background: var(--danger-red); color: white; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="removelogo.png" alt="Logo">
        <h1>Barangay Igpit Portal</h1>
    </div>
    <div class="user-info" style="text-align: right;">
        <strong style="display:block;"><?php echo htmlspecialchars($full_name); ?></strong>
        <small style="color:var(--accent-blue); font-weight:bold;"><i class="fa-solid fa-crown"></i> ADMINISTRATOR</small>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <h3>ADMIN MENU</h3>
        <a href="admin_dashboard.php" class="nav-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="admin_manage_users.php" class="nav-link"><i class="fa-solid fa-users-cog"></i> Manage Users</a>
        <a href="admin_all_requests.php" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> All Requests</a>
        <a href="admin_settings.php" class="nav-link active"><i class="fa-solid fa-gears"></i> Settings</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2><i class="fa-solid fa-user-lock"></i> Security Settings</h2>
            <form method="POST">
                <label>Change Administrator Password</label>
                <div class="input-group">
                    <input type="password" name="new_password" id="adminPass" placeholder="Enter new secure password" required minlength="6">
                    <i class="fa-solid fa-eye toggle-password" onclick="toggleVisibility()"></i>
                </div>
                <button type="submit" name="update_pass" class="btn-primary">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </form>

            <div style="margin-top: 40px; border-top: 2px dashed #f0f0f0; padding-top: 30px;">
                <h2 style="color: var(--danger-red);"><i class="fa-solid fa-radiation"></i> Danger Zone</h2>
                <p style="font-size: 13px; color: #7f8c8d; margin-bottom: 20px;">Use this action to clear all request records for a fresh school demonstration.</p>
                <form method="POST" id="resetForm">
                    <button type="button" onclick="confirmReset()" class="btn-danger">Clear Transaction History</button>
                    <input type="hidden" name="reset_system" value="1">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   
    function toggleVisibility() {
        const passInput = document.getElementById('adminPass');
        const icon = event.target;
        if (passInput.type === "password") {
            passInput.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passInput.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    
    function confirmReset() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete all transaction records!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#1e3799',
            confirmButtonText: 'Yes, clear all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('resetForm').submit();
            }
        })
    }

    
    <?php if($swal_msg != ""): ?>
        Swal.fire({
            icon: '<?php echo $swal_type; ?>',
            title: '<?php echo ($swal_type == "success") ? "Success!" : "System Reset!"; ?>',
            text: '<?php echo $swal_msg; ?>',
            confirmButtonColor: '#1e3799'
        });
    <?php endif; ?>
</script>

</body>
</html>