<?php
session_start();
include 'db.php';

// Security check
if (!isset($_SESSION['user_email']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: index.php");
    exit();
}

// Fetch the specific document data using the ID passed in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $conn->query("SELECT * FROM document_requests WHERE id = $id");
    if ($query->num_rows == 0) {
        die("Document not found.");
    }
    $data = $query->fetch_assoc();
} else {
    die("No document ID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print - <?php echo htmlspecialchars($data['document_type']); ?></title>
    <style>
        /* This styles the page to look like a piece of paper on the screen */
        body {
            background: #525659;
            display: flex;
            justify-content: center;
            padding: 40px;
            font-family: "Times New Roman", Times, serif; /* Formal font for certificates */
        }
        
        .document-paper {
            background: white;
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            padding: 40mm 20mm;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            position: relative;
        }

        .header { text-align: center; margin-bottom: 50px; }
        .header h3 { margin: 5px 0; font-weight: normal; font-size: 16px; }
        .header h1 { margin: 15px 0; font-size: 28px; text-transform: uppercase; text-decoration: underline; }

        .content { font-size: 18px; line-height: 1.8; text-align: justify; }
        .bold-text { font-weight: bold; text-transform: uppercase; }

        .signature-area { margin-top: 100px; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 40%; }
        .sig-line { border-bottom: 1px solid #000; margin-bottom: 5px; height: 40px; }

        /* Controls for the staff to click print (hidden when actually printing) */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn-print {
            background: #248135;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        /* MAGIC CSS: Anything in here ONLY applies to the printer! */
        @media print {
            body { background: white; padding: 0; }
            .document-paper { box-shadow: none; width: 100%; min-height: auto; padding: 20mm; }
            .print-controls { display: none; /* Hides the button from the printed paper */ }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">🖨️ Print Document</button>
    </div>

    <div class="document-paper">
        <div class="header">
            <h3>Republic of the Philippines</h3>
            <h3>Province of Misamis Oriental</h3>
            <h3>Municipality of Opol</h3>
            <h3>Barangay Igpit</h3>
            <br>
            <h1><?php echo htmlspecialchars($data['document_type']); ?></h1>
        </div>

        <div class="content">
            <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
            <br>
            <p>
                This is to certify that <span class="bold-text"><?php echo htmlspecialchars($data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name']); ?></span>, 
                is a bonafide resident of <span class="bold-text"><?php echo htmlspecialchars($data['address']); ?></span>, Barangay Igpit.
            </p>
            <p>
                This certification is being issued upon the request of the above-named person for the purpose of: <br>
                <strong style="text-decoration: underline;"><?php echo htmlspecialchars($data['purpose']); ?></strong>.
            </p>
            <p>
                Issued this <strong><?php echo date('jS'); ?></strong> day of <strong><?php echo date('F, Y'); ?></strong> at Barangay Igpit, Opol, Misamis Oriental.
            </p>
        </div>

        <div class="signature-area">
            <div class="sig-box">
                </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <strong>Hon. Barangay Captain</strong>
                <br><small>Punong Barangay</small>
            </div>
        </div>
        
        <div style="margin-top: 50px; font-size: 12px; color: #555;">
            <p>Transaction ID: <?php echo htmlspecialchars($data['transaction_id']); ?></p>
        </div>
    </div>

</body>
</html>