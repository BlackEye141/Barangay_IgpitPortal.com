<?php
session_start();


if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

$full_name = $_SESSION['full_name'];
$success_msg = "";
$msg_type = "alert-success";


if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $check_admin = $conn->query("SELECT email FROM users WHERE id = $user_id")->fetch_assoc();
    
    if ($check_admin['email'] == $_SESSION['user_email']) {
        $success_msg = "Warning: You cannot change your own role!";
        $msg_type = "alert-warning";
    } else {
        if ($conn->query("UPDATE users SET role = '$new_role' WHERE id = $user_id")) {
            $success_msg = "User role updated successfully!";
            $msg_type = "alert-success";
        }
    }
}


if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $check_user = $conn->query("SELECT email FROM users WHERE id = $delete_id")->fetch_assoc();
    
    if ($check_user['email'] !== $_SESSION['user_email']) {
        $conn->query("DELETE FROM users WHERE id = $delete_id");
        $success_msg = "User has been removed from the system.";
        $msg_type = "alert-danger";
    }
}


$users_query = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .table-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .alert { padding: 15px; border-radius: 12px; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8f9fa; color: #7f8c8d; font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        .role-badge { padding: 6px 14px; border-radius: 30px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .role-admin { background: #ffebee; color: #c62828; }
        .role-staff { background: #e0f7fa; color: #00838f; }
        .btn-save { background: var(--sidebar-blue); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: bold; }
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
        <a href="admin_manage_users.php" class="nav-link active"><i class="fa-solid fa-users-cog"></i> Manage Users</a>
        <a href="admin_all_requests.php" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> All Requests</a>
        <a href="admin_settings.php" class="nav-link"><i class="fa-solid fa-gears"></i> Settings</a>
    </div>

    <div class="main-content">
        <?php if($success_msg != ""): ?>
            <div class="alert <?php echo $msg_type; ?>"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <div class="table-card">
            <h2><i class="fa-solid fa-address-book"></i> User Directory</h2>
            <table class="data-table">
                <thead>
                    <tr><th>User</th><th>Role</th><th>Update</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $users_query->fetch_assoc()): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($row['full_name']); ?></b><br><small><?php echo htmlspecialchars($row['email']); ?></small></td>
                        <td><span class="role-badge role-user"><?php echo htmlspecialchars($row['role']); ?></span></td>
                        <td>
                            <?php if ($row['email'] != $_SESSION['user_email']): ?>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <select name="role" style="padding:5px; border-radius:5px;">
                                    <option value="resident">Resident</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn-save">Save</button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['email'] != $_SESSION['user_email']): ?>
                                <a href="?delete=<?php echo $row['id']; ?>" style="color:red;" onclick="return confirm('Mego, sure ka?')"><i class="fa-solid fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>