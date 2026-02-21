<?php
session_start();


if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

$full_name = $_SESSION['full_name'];
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}


$query = "SELECT * FROM document_requests WHERE 
          transaction_id LIKE '%$search%' OR 
          last_name LIKE '%$search%' OR 
          first_name LIKE '%$search%' OR
          email LIKE '%$search%' 
          ORDER BY id DESC";

$all_requests = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masterlist - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-blue: #1e3799;
            --bg-light: #f4f7f6;
            --accent-blue: #3498DB;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--bg-light); }

        
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
        .sidebar h3 { text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; color: #bdc3c7; font-size: 14px; margin-top:0; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; transition: 0.3s; font-weight: 500; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-link.active { background: var(--accent-blue); color: white; }

        
        .table-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .search-container { margin-bottom: 25px; display: flex; gap: 10px; background: #f8f9fa; padding: 15px; border-radius: 15px; }
        .search-input { flex: 1; padding: 12px 20px; border-radius: 10px; border: 1px solid #ddd; outline: none; }
        .search-btn { padding: 10px 25px; background: var(--sidebar-blue); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }

        
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .fa-spin-slow { animation: spin-slow 2s linear infinite; display: inline-block; color: #f39c12; }
        
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8f9fa; color: #7f8c8d; font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .paid { background: #e8f5e9; color: #2e7d32; }
        .unpaid { background: #fff3e0; color: #ef6c00; }
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
        <a href="admin_all_requests.php" class="nav-link active"><i class="fa-solid fa-clipboard-list"></i> All Requests</a>
        <a href="admin_settings.php" class="nav-link"><i class="fa-solid fa-gears"></i> Settings</a>
    </div>

    <div class="main-content">
        <div class="table-card">
            <h2 style="margin-top:0;"><i class="fa-solid fa-database"></i> Master Transaction List</h2>
            
            <form class="search-container" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Search Trn ID, Resident, or Email..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </form>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Trn ID</th>
                        <th>Resident Name</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($all_requests->num_rows > 0): ?>
                        <?php while($row = $all_requests->fetch_assoc()): ?>
                        <tr>
                            <td><b style="color:var(--accent-blue);"><?php echo htmlspecialchars($row['transaction_id']); ?></b></td>
                            <td>
                                <b><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></b><br>
                                <small style="color: #999;"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['document_type']); ?></td>
                            <td>
                                <?php 
                                    $p_status = $row['processing_status'] ?? 'Pending';
                                    $is_active = ($p_status == 'Pending' || $p_status == 'Processing');
                                ?>
                                <i class="fa-solid <?php echo $is_active ? 'fa-spinner fa-spin-slow' : 'fa-check-circle'; ?>" 
                                   style="color: <?php echo $is_active ? '#f39c12' : '#27ae60'; ?>;"></i> 
                                <?php echo htmlspecialchars($p_status); ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo ($row['payment_status'] == 'Paid') ? 'paid' : 'unpaid'; ?>">
                                    <?php echo htmlspecialchars($row['payment_status'] ?? 'Unpaid'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 40px; color: #999;">No requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>