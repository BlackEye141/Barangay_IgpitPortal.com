<?php
session_start();


if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

$full_name = $_SESSION['full_name'];
$user_email = $_SESSION['user_email'];


$users_query = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$total_users = $users_query->fetch_assoc()['total_users'] ?? 0;

$requests_query = $conn->query("SELECT COUNT(*) as total_reqs FROM document_requests");
$total_requests = $requests_query->fetch_assoc()['total_reqs'] ?? 0;

$revenue_query = $conn->query("SELECT COUNT(*) as paid_reqs FROM document_requests WHERE payment_status = 'Paid'");
$paid_count = $revenue_query->fetch_assoc()['paid_reqs'] ?? 0;
$total_revenue = $paid_count * 50; 

$all_requests = $conn->query("SELECT * FROM document_requests ORDER BY id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #f4f7f6;
            --sidebar-color: #1e3799; /* Igpit Blue */
            --card-bg: #FFFFFF;
            --text-dark: #2c3e50;
            --accent-blue: #3498DB;
            --success-green: #27ae60;
        }

        body { font-family: 'Inter', 'Segoe UI', sans-serif; margin: 0; background-color: var(--primary-bg); color: var(--text-dark); }
        
        
        header { 
            background: #fff; padding: 10px 40px; display: flex; 
            justify-content: space-between; align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; 
            top: 0; z-index: 1000; border-bottom: 3px solid var(--sidebar-color);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { height: 50px; width: auto; }
        .logo-section h1 { font-size: 20px; margin: 0; color: var(--sidebar-color); text-transform: uppercase; font-weight: 800; letter-spacing: 1px; }
        
        .user-profile { display: flex; align-items: center; gap: 20px; }
        .user-info { text-align: right; line-height: 1.3; }
        .user-info strong { display: block; font-size: 15px; }
        .user-info span { font-size: 11px; color: #e74c3c; font-weight: bold; letter-spacing: 1px; }
        
        .logout-btn { 
            background: #ffeaa7; color: #d35400; padding: 8px 15px; 
            border-radius: 10px; text-decoration: none; font-weight: bold; 
            display: flex; align-items: center; gap: 8px; font-size: 14px; transition: 0.3s;
        }
        .logout-btn:hover { background: #fab1a0; transform: scale(1.05); }

        .container { display: grid; grid-template-columns: 280px 1fr; padding: 30px; gap: 30px; }
        
        
        .sidebar { background: #2C3E50; border-radius: 20px; padding: 25px 15px; display: flex; flex-direction: column; gap: 8px; color: white; height: fit-content; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .sidebar h3 { text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-top: 0; font-size: 16px; color: #bdc3c7; text-transform: uppercase; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; transition: 0.3s; font-weight: 500; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: white; transform: translateX(5px); }
        .nav-link.active { background: var(--accent-blue); color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4); }

        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; }
        .stat-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; transition: 0.3s; position: relative; overflow: hidden; }
        .stat-card::after { content: ""; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: var(--accent-blue); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .stat-card label { color: #95a5a6; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 32px; font-weight: 800; margin-top: 10px; color: var(--text-dark); }
        .stat-card .value.green { color: var(--success-green); }

        
        .table-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-top: 10px; }
        .table-card h3 { margin-top: 0; margin-bottom: 25px; color: var(--text-dark); font-size: 18px; display: flex; align-items: center; gap: 10px; }
        
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8f9fa; color: #7f8c8d; font-size: 12px; text-transform: uppercase; font-weight: 700; padding: 15px; border-bottom: 2px solid #eee; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background-color: #fafafa; }
        
        .status-badge { padding: 6px 14px; border-radius: 30px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .status-paid { background: #e8f5e9; color: #2e7d32; }
        .status-pending { background: #fff3e0; color: #ef6c00; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="removelogo.png" alt="Igpit Logo">
        <h1>Barangay Igpit Portal</h1>
    </div>
    <div class="user-profile">
        <div class="user-info">
            <strong><?php echo htmlspecialchars($full_name); ?></strong>
            <span><i class="fa-solid fa-crown"></i> ADMINISTRATOR</span>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-power-off"></i> Logout
        </a>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php" class="nav-link active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        <a href="admin_manage_users.php" class="nav-link"><i class="fa-solid fa-users-cog"></i> Manage Users</a>
        <a href="admin_all_requests.php" class="nav-link"><i class="fa-solid fa-clipboard-list"></i> All Requests</a>
        <a href="admin_settings.php" class="nav-link"><i class="fa-solid fa-gears"></i> Settings</a>
    </div>

    <div class="main-content">
        
        <div class="stats-grid">
            <div class="stat-card">
                <label><i class="fa-solid fa-people-roof"></i> Registered Residents</label>
                <span class="value"><?php echo $total_users; ?></span>
            </div>
            <div class="stat-card">
                <label><i class="fa-solid fa-folder-tree"></i> Total Requests</label>
                <span class="value"><?php echo $total_requests; ?></span>
            </div>
            <div class="stat-card">
                <label><i class="fa-solid fa-coins"></i> Total Revenue</label>
                <span class="value green">₱<?php echo number_format($total_revenue, 2); ?></span>
            </div>
        </div>

        <div class="table-card">
            <h3><i class="fa-solid fa-history"></i> Recent Transactions</h3>
            
            <?php if($all_requests->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Resident Email</th>
                            <th>Document</th>
                            <th>Date</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $all_requests->fetch_assoc()): ?>
                        <tr>
                            <td><b style="color:var(--accent-blue); font-family: monospace;"><?php echo htmlspecialchars($row['transaction_id']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['document_type']); ?></td>
                            <td><small><?php echo isset($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'Recently'; ?></small></td>
                            <td>
                                <?php 
                                    $status = $row['payment_status'] ?? 'Pending';
                                    $badge_class = ($status == 'Paid') ? 'status-paid' : 'status-pending';
                                ?>
                                <span class="status-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center; padding: 40px;">
                    <i class="fa-solid fa-folder-open" style="font-size: 40px; color: #ddd; margin-bottom: 15px;"></i>
                    <p style="color:#999;">NO REQUEST RIGHT NOW!.</p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

</body>
</html>