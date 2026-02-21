<?php 
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Safely check if an ID was passed to this page
if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_REQUEST['id']);

// Fetch the data
$result = $conn->query("SELECT * FROM document_requests WHERE id=$id");

if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - Barangay Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gcash-blue: #007dfe;
            --maya-green: #2ecc71;
            --card-orange: #e67e22;
            --bg-color: #f4f7f6;
            --text-dark: #2d3436;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--bg-color); 
            margin: 0; 
            padding: 40px 20px;
            color: var(--text-dark);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pay-box { 
            background: white; 
            padding: 40px; 
            border-radius: 30px; 
            max-width: 500px; 
            margin: auto; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            animation: slideUp 0.6s ease-out;
        }

        .cancel-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #636e72;
            font-weight: 600;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .cancel-link:hover { color: #e74c3c; transform: translateX(-5px); }

        .summary-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #edf2f7;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px dashed #ddd;
            font-weight: 800;
            font-size: 18px;
            color: var(--gcash-blue);
        }

        h4 { margin: 25px 0 15px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #a0aec0; }

        .method { 
            background: #fff; 
            padding: 15px; 
            border-radius: 15px; 
            margin: 10px 0; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            border: 2px solid #edf2f7;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .method.active { 
            border-color: var(--gcash-blue); 
            background: #f0f7ff; 
            transform: scale(1.02);
        }

        .input-group {
            margin-top: 25px;
        }

        label { font-size: 13px; font-weight: 700; display: block; margin-bottom: 8px; }

        input { 
            width: 100%; 
            padding: 15px; 
            border: 2px solid #edf2f7; 
            border-radius: 12px; 
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
            font-family: monospace; /* Para klaro ang numbers */
        }
        input:focus { border-color: var(--gcash-blue); }

        .error-msg {
            color: #e74c3c;
            font-size: 11px;
            margin-top: 5px;
            display: none;
            font-weight: 600;
        }

        .btn-pay { 
            background: var(--gcash-blue); 
            color: white; 
            width: 100%; 
            padding: 18px; 
            margin-top: 25px; 
            border: none; 
            border-radius: 15px; 
            font-size: 16px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0, 125, 254, 0.2);
        }
        .btn-pay:hover { 
            background: #0061c5; 
            transform: translateY(-3px);
        }

        .security-note {
            text-align: center;
            font-size: 12px;
            color: #b2bec3;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div style="max-width:500px; margin: auto;">
        <a href="dashboard.php" class="cancel-link"><i class="fa-solid fa-xmark"></i> Cancel Payment</a>
    </div>
    
    <div class="pay-box">
        <h2 style="margin-top:0;"><i class="fa-solid fa-shield-halved" style="color: var(--gcash-blue);"></i> Checkout</h2>
        
        <div class="summary-card">
            <div class="summary-row">
                <span>Document Type</span>
                <b><?php echo htmlspecialchars($data['document_type']); ?></b>
            </div>
            <div class="summary-row">
                <span>Processing Fee</span>
                <b>₱50.00</b>
            </div>
            <div class="total-row">
                <span>Total Amount</span>
                <span id="totalDisplay">₱50.00</span>
            </div>
        </div>

        <h4>Select Payment Method</h4>
        
        <div class="method active" onclick="selectMethod(this, 'gcash')">
            <span><i class="fa-solid fa-mobile-screen-button" style="color: var(--gcash-blue); margin-right: 10px;"></i> GCash</span>
            <i class="fa-solid fa-circle-check" style="color: var(--gcash-blue);"></i>
        </div>
        
        <div class="method" onclick="selectMethod(this, 'maya')">
            <span><i class="fa-solid fa-wallet" style="color: var(--maya-green); margin-right: 10px;"></i> Maya</span>
            <i class="fa-regular fa-circle" style="color: #ddd;"></i>
        </div>

        <div class="method" onclick="selectMethod(this, 'card')">
            <span><i class="fa-solid fa-credit-card" style="color: var(--card-orange); margin-right: 10px;"></i> Credit Card</span>
            <i class="fa-regular fa-circle" style="color: #ddd;"></i>
        </div>
        
        <form action="payment_success.php" method="POST" id="paymentForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="input-group">
                <label id="methodLabel">GCash Mobile Number</label>
                <input 
                    type="text" 
                    id="phoneInput" 
                    name="account_number"
                    placeholder="09XXXXXXXXX" 
                    maxlength="11"
                    oninput="validateNumbers(this)"
                    required
                >
                <div id="numberError" class="error-msg">Numbers only!</div>
            </div>

            <button type="submit" class="btn-pay" id="payBtn">
                Pay ₱50.00
            </button>
        </form>

        <div class="security-note">
            <i class="fa-solid fa-lock"></i> Your transaction is secured and encrypted.
        </div>
    </div>

    <script>
        // Number only validation function
        function validateNumbers(input) {
            const errorDiv = document.getElementById('numberError');
            const originalValue = input.value;
            
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');
            
            // If the value changed, it means they tried to type a letter
            if (originalValue !== input.value) {
                errorDiv.style.display = 'block';
                setTimeout(() => { errorDiv.style.display = 'none'; }, 2000);
            }
        }

        function selectMethod(element, type) {
            // Reset all methods
            document.querySelectorAll('.method').forEach(m => {
                m.classList.remove('active');
                m.querySelector('i:last-child').className = 'fa-regular fa-circle';
                m.querySelector('i:last-child').style.color = '#ddd';
            });

            // Set active method
            element.classList.add('active');
            const icon = element.querySelector('i:last-child');
            icon.className = 'fa-solid fa-circle-check';
            
            const label = document.getElementById('methodLabel');
            const input = document.getElementById('phoneInput');
            const btn = document.getElementById('payBtn');
            const totalDisp = document.getElementById('totalDisplay');

            // Switch colors and text based on selection
            if (type === 'gcash') {
                label.innerText = "GCash Mobile Number";
                input.placeholder = "09XXXXXXXXX";
                input.maxlength = "11";
                icon.style.color = "var(--gcash-blue)";
                btn.style.background = "var(--gcash-blue)";
                totalDisp.style.color = "var(--gcash-blue)";
            } else if (type === 'maya') {
                label.innerText = "Maya Mobile Number";
                input.placeholder = "09XXXXXXXXX";
                input.maxlength = "11";
                icon.style.color = "var(--maya-green)";
                btn.style.background = "var(--maya-green)";
                totalDisp.style.color = "var(--maya-green)";
            } else {
                label.innerText = "Credit Card Number";
                input.placeholder = "XXXX XXXX XXXX XXXX";
                input.maxlength = "16";
                icon.style.color = "var(--card-orange)";
                btn.style.background = "var(--card-orange)";
                totalDisp.style.color = "var(--card-orange)";
            }
        }
    </script>
</body>
</html>