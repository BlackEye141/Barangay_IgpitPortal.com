<?php
session_start();

// 1. STRICT SECURITY CHECK
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

include 'db.php';
$full_name = $_SESSION['full_name'];

// FETCH ONLY PAID REQUESTS
$requests_query = $conn->query("SELECT * FROM document_requests WHERE payment_status = 'Paid' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Documents - Staff Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3799; /* Igpit Blue */
            --bg-light: #f4f7f6;
            --accent-blue: #3498DB;
            --text-dark: #2c3e50;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--bg-light); color: var(--text-dark); }
        
        /* HEADER - Synced with Admin Design (Added Logo) */
        header { 
            background: #fff; padding: 10px 40px; display: flex; 
            justify-content: space-between; align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; 
            top: 0; z-index: 1000; border-bottom: 3px solid var(--primary-blue);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { height: 50px; width: auto; } /* Added logo styling */
        .logo-section h1 { font-size: 20px; margin: 0; color: var(--primary-blue); text-transform: uppercase; font-weight: 800; }

        .user-nav { display: flex; align-items: center; gap: 20px; }
        .logout-btn { background: #e74c3c; color: white; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .logout-btn:hover { background: #c0392b; transform: scale(1.05); }

        .container { display: grid; grid-template-columns: 280px 1fr; padding: 30px; gap: 30px; }
        
        /* SIDEBAR */
        .sidebar { background: #2C3E50; border-radius: 20px; padding: 25px 15px; display: flex; flex-direction: column; gap: 8px; color: white; min-height: 70vh; }
        .sidebar h3 { text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; color: #bdc3c7; font-size: 14px; margin-top: 0; }
        .nav-link { color: #bdc3c7; text-decoration: none; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; transition: 0.3s; font-weight: 500; }
        .nav-link.active, .nav-link:hover { background: var(--accent-blue); color: white; }

        /* Main Content */
        .table-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table-card h2 { margin-top:0; color: var(--primary-blue); font-weight: 800; }
        
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table th { background: #f8f9fa; color: #7f8c8d; font-size: 11px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; text-align: left; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }

        .btn-print { background: var(--primary-blue); color: white; text-decoration: none; padding: 10px 18px; border-radius: 12px; font-weight: 800; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; }
        .btn-print:hover { background: #152b7a; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(30, 55, 153, 0.2); }
        
        .status-badge { background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="removelogo.png" alt="Igpit Logo"> 
        <h1>Igpit Staff Portal</h1>
    </div>
    <div class="user-nav">
        <div class="user-info" style="text-align: right;">
            <strong style="display:block;"><?php echo htmlspecialchars($full_name); ?></strong>
            <small style="color:var(--accent-blue); font-weight:bold;">BARANGAY STAFF</small>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <h3>STAFF MENU</h3>
        <a href="staff_dashboard.php" class="nav-link"><i class="fa-solid fa-list-check"></i> Manage Requests</a>
        <a href="staff_print_list.php" class="nav-link active"><i class="fa-solid fa-print"></i> Print Documents</a>
    </div>

    <div class="main-content">
        <div class="table-card">
            <h2><i class="fa-solid fa-print"></i> Ready to Print</h2>
            <p style="color:#7f8c8d; margin-bottom: 25px; font-size: 14px;">Documents listed below have been verified as "Paid".</p>
            
            <?php if($requests_query->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Trn ID</th>
                            <th>Resident Name</th>
                            <th>Document Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $requests_query->fetch_assoc()): ?>
                        <tr>
                            <td><b style="color:var(--accent-blue);"><?php echo htmlspecialchars($row['transaction_id']); ?></b></td>
                            <td><b><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['document_type']); ?></td>
                            <td><span class="status-badge"><?php echo htmlspecialchars($row['processing_status']); ?></span></td>
                            <td>
                                <a href="print_template.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-print">
                                    <i class="fa-solid fa-file-pdf"></i> Generate
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align:center; padding: 40px; color:#7f8c8d;">
                    <i class="fa-solid fa-file-circle-xmark" style="font-size: 30px; margin-bottom: 10px;"></i>
                    <p>No paid documents ready.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>