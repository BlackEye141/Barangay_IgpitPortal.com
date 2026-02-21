<?php
session_start();

// 1. STRICT SECURITY CHECK
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

include 'db.php';

$full_name = $_SESSION['full_name'];
$success_msg = "";

// 2. HANDLE STATUS UPDATES
if (isset($_POST['update_status'])) {
    $request_id = intval($_POST['request_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['processing_status']);
    
    /** * MAPPING LOGIC: 
     * Kinahanglan mo-match ang string ngadto sa ENUM values sa imong database:
     * 'Pending', 'Processing', 'Ready', 'Complete'
     */
    $db_status = 'Pending'; // Default
    if ($new_status == 'Processing') { 
        $db_status = 'Processing'; 
    } elseif ($new_status == 'Ready for Pickup') { 
        $db_status = 'Ready'; 
    } elseif ($new_status == 'Completed') { 
        $db_status = 'Complete'; 
    }

    // I-update ang duha ka columns aron sync ang Staff view ug Resident tracker
    $update_sql = "UPDATE document_requests SET 
                   processing_status = '$new_status', 
                   status = '$db_status' 
                   WHERE id = $request_id";
    
    if ($conn->query($update_sql)) {
        $success_msg = "Status successfully updated to <b>$new_status</b>!";
    }
}

// 3. FETCH DATA
$requests_query = $conn->query("SELECT * FROM document_requests ORDER BY id DESC");

// Task counter logic
$pending_query = $conn->query("SELECT COUNT(*) as pending_tasks FROM document_requests WHERE status != 'Complete'");
$pending_tasks = $pending_query->fetch_assoc()['pending_tasks'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3799; 
            --bg-light: #f4f7f6;
            --accent-blue: #3498DB;
            --text-dark: #2c3e50;
        }
        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--bg-light); color: var(--text-dark); }
        
        header { 
            background: #fff; padding: 10px 40px; display: flex; 
            justify-content: space-between; align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; 
            top: 0; z-index: 1000; border-bottom: 3px solid var(--primary-blue);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { height: 50px; width: auto; }
        .logo-section h1 { font-size: 20px; margin: 0; color: var(--primary-blue); text-transform: uppercase; font-weight: 800; }

        .user-nav { display: flex; align-items: center; gap: 20px; }
        .logout-btn { background: #e74c3c; color: white; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 8px; transition: 0.3s; }

        .container { display: grid; grid-template-columns: 280px 1fr; padding: 30px; gap: 30px; }
        
        .sidebar { background: #2C3E50; border-radius: 20px; padding: 25px 15px; display: flex; flex-direction: column; gap: 8px; color: white; min-height: 75vh; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; color: #bdc3c7; font-size: 12px; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; transition: 0.3s; font-weight: 600; }
        .nav-link.active, .nav-link:hover { background: var(--accent-blue); color: white; }

        .top-banner { background: white; padding: 25px 30px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .task-counter { background: #e74c3c; color: white; padding: 10px 20px; border-radius: 30px; font-weight: 800; }

        .table-card { background: white; padding: 20px; border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8f9fa; color: #7f8c8d; font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; }
        .data-table td { padding: 18px 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }

        .status-select { padding: 10px; border-radius: 10px; border: 2px solid #dfe6e9; background: #fff; outline: none; font-weight: 600; }
        .btn-update { background: var(--primary-blue); color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-weight: 800; transition: 0.3s; }
        .btn-update:hover { background: #152b7a; transform: translateY(-2px); }

        .badge-paid { background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; }
        .badge-pending { background: #fff3e0; color: #ef6c00; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="removelogo.png" alt="Logo">
        <h1>Igpit Staff Portal</h1>
    </div>
    <div class="user-nav">
        <div style="text-align: right;">
            <strong><?php echo htmlspecialchars($full_name); ?></strong><br>
            <small style="color:var(--accent-blue);">STAFF ACCOUNT</small>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fa-solid fa-power-off"></i></a>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <h3>MAIN MENU</h3>
        <a href="staff_dashboard.php" class="nav-link active"><i class="fa-solid fa-list-check"></i> Manage Requests</a>
        <a href="staff_print_list.php" class="nav-link"><i class="fa-solid fa-print"></i> Print List</a>
    </div>

    <div class="main-content">
        <?php if($success_msg != ""): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 15px; margin-bottom: 25px; border-left: 5px solid #28a745;">
                <i class="fa-solid fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <div class="top-banner">
            <h2 style="margin:0;">Document Processing</h2>
            <div class="task-counter">
                <i class="fa-solid fa-hourglass-half"></i> <?php echo $pending_tasks; ?> Tasks Remaining
            </div>
        </div>

        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Resident Name</th>
                        <th>Document</th>
                        <th>Payment</th>
                        <th>Set Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $requests_query->fetch_assoc()): ?>
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: var(--primary-blue);">
                            #<?php echo $row['transaction_id']; ?>
                        </td>
                        <td>
                            <b><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></b>
                        </td>
                        <td><?php echo htmlspecialchars($row['document_type']); ?></td>
                        <td>
                            <?php 
                                $is_paid = ($row['payment_status'] == 'Paid');
                                $badge_class = $is_paid ? 'badge-paid' : 'badge-pending';
                            ?>
                            <span class="<?php echo $badge_class; ?>">
                                <i class="fa-solid <?php echo $is_paid ? 'fa-check-circle' : 'fa-clock'; ?>"></i> 
                                <?php echo $row['payment_status']; ?>
                            </span>
                        </td>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <td>
                                <select name="processing_status" class="status-select">
                                    <option value="Pending" <?php if($row['processing_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processing" <?php if($row['processing_status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Ready for Pickup" <?php if($row['processing_status'] == 'Ready for Pickup') echo 'selected'; ?>>Ready for Pickup</option>
                                    <option value="Completed" <?php if($row['processing_status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update_status" class="btn-update">Update</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>