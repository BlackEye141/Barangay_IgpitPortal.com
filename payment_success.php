<?php 
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

if(isset($_POST['id'])){
    $id = intval($_POST['id']);
    // Update the status to Paid
    $conn->query("UPDATE document_requests SET payment_status='Paid' WHERE id=$id");
    
    // Fetch the updated row to show the details
    $row = $conn->query("SELECT * FROM document_requests WHERE id=$id")->fetch_assoc();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Barangay Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --success-green: #2ecc71;
            --primary-blue: #457B9D;
            --bg-color: #f4f7f6;
            --text-dark: #2d3436;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--bg-color); 
            margin: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--text-dark);
        }

        @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .success-card { 
            background: white; 
            padding: 40px; 
            border-radius: 30px; 
            max-width: 450px; 
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            animation: scaleUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        /* Decorative top bar */
        .success-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: var(--success-green);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(46, 204, 113, 0.1);
            color: var(--success-green);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            margin: 0 auto 20px;
            animation: rotateCheck 0.6s ease-out 0.3s both;
        }

        @keyframes rotateCheck {
            from { transform: rotate(-45deg) scale(0); opacity: 0; }
            to { transform: rotate(0) scale(1); opacity: 1; }
        }

        h1 { margin: 0; font-size: 24px; letter-spacing: -0.5px; }
        p { color: #636e72; margin: 10px 0 30px; line-height: 1.5; }

        .receipt-info {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 20px;
            text-align: left;
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .receipt-row span { color: #b2bec3; }
        .receipt-row b { color: var(--text-dark); }

        .trn-badge {
            display: block;
            margin-top: 15px;
            padding: 10px;
            background: white;
            border: 1px dashed var(--primary-blue);
            border-radius: 10px;
            color: var(--primary-blue);
            font-family: monospace;
            font-size: 16px;
            text-align: center;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
            border: none;
            font-size: 15px;
        }

        .btn-main {
            background: var(--primary-blue);
            color: white;
            box-shadow: 0 10px 20px rgba(69, 123, 157, 0.2);
        }
        .btn-main:hover { background: #1d3557; transform: translateY(-3px); }

        .btn-print {
            background: #fff;
            color: #636e72;
            border: 1px solid #ddd;
        }
        .btn-print:hover { background: #f8f9fa; }

        @media print {
            .btn-group, h1, p, .icon-circle { display: none; }
            body { background: white; padding: 0; }
            .success-card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon-circle">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h1>Payment Successful!</h1>
        <p>Gidawat na namo ang imong bayad, mego. Sugdan na nato ang pag-process sa imong request.</p>
        
        <div class="receipt-info">
            <div class="receipt-row">
                <span>Document:</span>
                <b><?php echo htmlspecialchars($row['document_type']); ?></b>
            </div>
            <div class="receipt-row">
                <span>Amount Paid:</span>
                <b>₱50.00</b>
            </div>
            <div class="receipt-row">
                <span>Status:</span>
                <b style="color:var(--success-green);">Verified</b>
            </div>
            <div class="trn-badge">
                <small style="display:block; font-size:10px; text-transform:uppercase; color:#b2bec3;">Transaction ID</small>
                <b><?php echo htmlspecialchars($row['transaction_id']); ?></b>
            </div>
        </div>

        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fa-solid fa-print"></i> Print Receipt
            </button>
            <a href="dashboard.php" class="btn btn-main">Go to Dashboard</a>
        </div>
    </div>

</body>
</html>