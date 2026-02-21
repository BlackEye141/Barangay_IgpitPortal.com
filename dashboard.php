<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$user_email = $_SESSION['user_email'];
$full_name = $_SESSION['full_name'];


$stats_query = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN payment_status != 'Paid' THEN 1 ELSE 0 END) as pending_pay,
    SUM(CASE WHEN status = 'Ready' THEN 1 ELSE 0 END) as ready_docs
    FROM document_requests WHERE email = '$user_email'");

$stats = $stats_query->fetch_assoc();
$total_requests = $stats['total'] ?? 0; 
$pending_payments = $stats['pending_pay'] ?? 0;
$ready_count = $stats['ready_docs'] ?? 0;


$requests_query = $conn->query("SELECT * FROM document_requests WHERE email = '$user_email' ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1e3799;
            --accent-blue: #3498DB;
            --bg-light: #f4f7f6;
            --card-white: #ffffff;
            --ready-gold: #f39c12;
            --success-green: #27ae60;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-light); 
            margin: 0; 
            color: #2c3e50;
        }

        
        @keyframes pulse-ready {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(243, 156, 18, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(243, 156, 18, 0); }
        }

        header { 
            background: white; padding: 10px 40px; display: flex; 
            justify-content: space-between; align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-bottom: 3px solid var(--primary-blue);
            position: sticky; top: 0; z-index: 100;
        }

        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section h1 { font-size: 20px; color: var(--primary-blue); text-transform: uppercase; font-weight: 800; margin: 0; }

        .container { display: grid; grid-template-columns: 320px 1fr; padding: 30px; gap: 30px; max-width: 1400px; margin: auto; }

        
        .sidebar { display: flex; flex-direction: column; gap: 20px; }
        .step-header { 
            background: var(--primary-blue); color: white; padding: 20px; border-radius: 20px; 
            font-weight: 800; box-shadow: 0 10px 20px rgba(30, 55, 153, 0.2);
        }
        .step-card { 
            background: white; padding: 15px; border-radius: 15px; font-size: 13px; 
            border-left: 5px solid var(--accent-blue); box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .stat-box { background: white; padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .stat-box label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #7f8c8d; }
        .stat-box .count { font-size: 28px; font-weight: 800; display: block; margin-top: 5px; }

        
        .action-buttons { display: flex; gap: 20px; margin-bottom: 30px; }
        .btn { 
            flex: 1; padding: 25px; border-radius: 20px; border: none; cursor: pointer;
            transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px; font-weight: 800;
        }
        .btn-request { background: var(--primary-blue); color: white; }
        .btn-track { background: white; color: var(--primary-blue); border: 2px solid var(--primary-blue); }
        .btn:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .recent-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        
        .status-pill {
            padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; 
            text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px;
        }
        .status-ready { 
            background: rgba(243, 156, 18, 0.15); color: var(--ready-gold); 
            border: 1px solid var(--ready-gold); animation: pulse-ready 2s infinite;
        }
        .status-processing { background: rgba(52, 152, 219, 0.15); color: var(--accent-blue); border: 1px solid var(--accent-blue); }
        .status-complete { background: rgba(39, 174, 96, 0.15); color: var(--success-green); border: 1px solid var(--success-green); }
        .status-pending { background: #f1f1f1; color: #7f8c8d; }

        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .data-table td { padding: 15px; background: #fcfcfc; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
        .data-table tr td:first-child { border-left: 1px solid #eee; border-radius: 12px 0 0 12px; }
        .data-table tr td:last-child { border-right: 1px solid #eee; border-radius: 0 12px 12px 0; }

        .logout-btn { background: #e74c3c; color: white; padding: 8px 15px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 12px; }
    </style>
</head>
<body>

<header>
    <div class="logo-section">
        <img src="removelogo.png" alt="Logo" style="height: 40px;">
        <h1>Igpit Portal</h1>
    </div>
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="text-align: right;">
            <strong style="display: block;"><?php echo htmlspecialchars($full_name); ?></strong>
            <small style="color: var(--accent-blue); font-weight: 700;">RESIDENT</small>
        </div>
        <a href="logout.php" class="logout-btn"><i class="fa-solid fa-power-off"></i></a>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <div class="step-header">
            DOCUMENT STEPS <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <div class="step-card"><b>1. Request</b> - Fill up form</div>
        <div class="step-card"><b>2. Payment</b> - Pay at office/online</div>
        <div class="step-card"><b>3. Track</b> - Monitor status here</div>
        <div class="step-card"><b>4. Pickup</b> - Get your document</div>

        <div class="stat-box" style="border-bottom: 4px solid var(--primary-blue);">
            <label>Total Requests</label>
            <span class="count"><?php echo $total_requests; ?></span>
        </div>
        
        <?php if($ready_count > 0): ?>
        <div class="stat-box" style="border-bottom: 4px solid var(--ready-gold); background: #fffdf5;">
            <label style="color: var(--ready-gold);">Ready for Pickup</label>
            <span class="count" style="color: var(--ready-gold);"><?php echo $ready_count; ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <div class="action-buttons">
            <button class="btn btn-request" onclick="window.location.href='request_form.php'">
                <i class="fa-solid fa-file-circle-plus" style="font-size: 24px;"></i>
                NEW REQUEST
            </button>
            <button class="btn btn-track" onclick="window.location.href='track_documents.php'">
                <i class="fa-solid fa-magnifying-glass-location" style="font-size: 24px;"></i>
                TRACK STATUS
            </button>
        </div>

        <div class="recent-card">
            <h3 style="margin-top:0;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-blue);"></i> My Recent Requests</h3>
            
            <?php if($requests_query->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr style="text-align: left; color: #bdc3c7; font-size: 11px; text-transform: uppercase;">
                            <th style="padding: 10px;">Document Type</th>
                            <th>Transaction ID</th>
                            <th>Current Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $requests_query->fetch_assoc()): ?>
                        <tr>
                            <td><i class="fa-regular fa-file-pdf" style="color: var(--primary-blue);"></i> <b><?php echo $row['document_type']; ?></b></td>
                            <td style="font-family: monospace; font-weight: 700;">#<?php echo $row['transaction_id']; ?></td>
                            <td>
                                <?php 
                                    $s = $row['status'] ?? 'Pending';
                                    $class = "status-pending";
                                    $icon = "fa-clock";

                                    if($s == 'Processing') { $class = "status-processing"; $icon = "fa-spinner fa-spin"; }
                                    if($s == 'Ready') { $class = "status-ready"; $icon = "fa-bell"; }
                                    if($s == 'Complete') { $class = "status-complete"; $icon = "fa-check-double"; }
                                ?>
                                <span class="status-pill <?php echo $class; ?>">
                                    <i class="fa-solid <?php echo $icon; ?>"></i> <?php echo $s; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fa-solid fa-inbox" style="font-size: 40px; color: #eee;"></i>
                    <p style="color: #95a5a6;">No requests found. Start by clicking New Request.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>