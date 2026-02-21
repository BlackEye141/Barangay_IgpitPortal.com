<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$user_email = $_SESSION['user_email'];

// Handle notification messages
$msg = $_GET['msg'] ?? '';

// Fetch ALL requests for this user, newest first
$query = "SELECT * FROM document_requests WHERE email = '$user_email' ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track My Documents - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f0f2f5;
            --glass-white: rgba(255, 255, 255, 0.95);
            --accent-color: #1e3799;
            --danger-red: #e74c3c;
            --text-main: #2d3436;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background-color: var(--bg-color); 
            margin: 0; padding: 40px 20px;
            color: var(--text-main);
        }

        .container { 
            background: var(--glass-white); 
            backdrop-filter: blur(10px);
            padding: 40px; border-radius: 30px; 
            max-width: 1200px; margin: auto; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        .back-btn { 
            text-decoration: none; color: #636e72; display: inline-flex; 
            align-items: center; gap: 10px; font-weight: 600; 
            background: #fff; padding: 10px 20px; border-radius: 12px;
        }

        .alert-del { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: bold; border-left: 5px solid #ef4444; }

        .tracker-table { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
        .tracker-table th { padding: 10px 20px; text-align: left; color: #b2bec3; font-size: 12px; text-transform: uppercase; }
        .tracker-table tr td { padding: 20px; background: white; border-top: 1px solid #f1f1f1; border-bottom: 1px solid #f1f1f1; }
        
        .tracker-table tr td:first-child { border-radius: 15px 0 0 15px; border-left: 1px solid #f1f1f1; }
        .tracker-table tr td:last-child { border-radius: 0 15px 15px 0; border-right: 1px solid #f1f1f1; text-align: center; }

        .status-badge { padding: 8px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
        .status-paid { background: rgba(46, 204, 113, 0.15); color: #27ae60; }
        .status-unpaid { background: rgba(231, 76, 60, 0.15); color: #c0392b; }

        .btn-pay { background: var(--accent-color); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 700; transition: 0.3s; text-decoration: none; }
        .btn-delete { color: var(--danger-red); text-decoration: none; font-size: 20px; transition: 0.3s; border:none; background:none; cursor:pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <h2><i class="fa-solid fa-satellite-dish" style="color: var(--accent-color); margin-right: 10px;"></i> Document Status Tracker</h2>
    <p style="color: #636e72; margin-bottom: 30px;">Track the real-time progress of your barangay document applications.</p>

    <?php if($msg == 'deleted'): ?>
        <div class="alert-del"><i class="fa-solid fa-trash-can"></i> Request successfully deleted.</div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table class="tracker-table">
            <thead>
                <tr>
                    <th>TRN ID</th>
                    <th>Document</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td style="font-family: monospace; color: var(--accent-color);">
                            <b>#<?php echo htmlspecialchars($row['transaction_id']); ?></b>
                        </td>
                        <td>
                            <div style="font-weight: 700;"><?php echo htmlspecialchars($row['document_type']); ?></div>
                        </td>
                        <td>
                            <?php 
                                $pay_status = $row['payment_status'] ?? 'Unpaid';
                                $pay_class = ($pay_status == 'Paid') ? 'status-paid' : 'status-unpaid';
                            ?>
                            <span class="status-badge <?php echo $pay_class; ?>">
                                <i class="fa-solid <?php echo ($pay_status == 'Paid') ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                                <?php echo htmlspecialchars($pay_status); ?>
                            </span>
                        </td>
                        <td>
    <?php if ($row['payment_status'] != 'Paid'): ?>
        <form action="payment.php" method="POST" style="margin:0;">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn-pay">Pay Now</button>
        </form>
    <?php else: ?>
        <span class="status-badge" style="background: #e3f2fd; color: #1976d2; border: 1px solid #bbdefb;">
            <i class="fa-solid fa-circle-info"></i> 
            <?php echo htmlspecialchars($row['status']); ?>
        </span>
    <?php endif; ?>
</td>
                        <td>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                                <?php if ($pay_status == 'Unpaid'): ?>
                                    <a href="payment.php?id=<?php echo $row['id']; ?>" class="btn-pay">Pay</a>
                                    
                                    <a href="delete_request.php?id=<?php echo $row['id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Gusto nimo papason kini nga request?')"
                                       title="Delete Request">
                                       <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #bdc3c7; font-size: 11px;"><i class="fa-solid fa-lock"></i> Official</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; padding: 40px; color:#999;">No requests found.</p>
    <?php endif; ?>
</div>

</body>
</html>