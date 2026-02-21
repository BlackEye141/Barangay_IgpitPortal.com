<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit_request'])) {
    $email = $_SESSION['user_email'];
    $document_type = mysqli_real_escape_string($conn, $_POST['document_type']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = $_POST['middle_name'] ?? "";
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    
    $transaction_id = "TRN-" . rand(100000, 999999);
    
    $sql = "INSERT INTO document_requests (email, document_type, first_name, last_name, middle_name, address, contact_number, purpose, transaction_id, payment_status) 
            VALUES ('$email', '$document_type', '$first_name', '$last_name', '$middle_name', '$address', '$contact_number', '$purpose', '$transaction_id', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        
        // --- PROFESSIONAL SUCCESS SCREEN ---
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Request Submitted</title>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                body { 
                    font-family: 'Inter', sans-serif; 
                    background: #f4f7f6; 
                    display: flex; 
                    justify-content: center; 
                    align-items: center; 
                    height: 100vh; 
                    margin: 0; 
                }
                .success-card {
                    background: white;
                    padding: 50px;
                    border-radius: 30px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                    text-align: center;
                    max-width: 450px;
                    width: 90%;
                    animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                @keyframes popIn {
                    from { opacity: 0; transform: scale(0.8); }
                    to { opacity: 1; transform: scale(1); }
                }
                .checkmark {
                    font-size: 60px;
                    color: #2ecc71;
                    margin-bottom: 20px;
                    animation: bounce 1s infinite alternate;
                }
                @keyframes bounce {
                    from { transform: translateY(0); }
                    to { transform: translateY(-10px); }
                }
                h2 { color: #2d3436; margin: 0 0 10px 0; }
                p { color: #636e72; margin-bottom: 30px; }
                .trn-box {
                    background: #f8f9fa;
                    padding: 10px;
                    border-radius: 10px;
                    font-family: monospace;
                    font-size: 18px;
                    color: #457B9D;
                    margin-bottom: 30px;
                    border: 1px dashed #457B9D;
                }
                .btn-pay {
                    background: #457B9D;
                    color: white;
                    padding: 15px 40px;
                    border: none;
                    border-radius: 15px;
                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: all 0.3s;
                    box-shadow: 0 10px 20px rgba(69, 123, 157, 0.2);
                    width: 100%;
                }
                .btn-pay:hover {
                    background: #1d3557;
                    transform: translateY(-3px);
                }
            </style>
        </head>
        <body>
            <div class='success-card'>
                <div class='checkmark'><i class='fa-solid fa-circle-check'></i></div>
                <h2>Request Saved!</h2>
                <p>Your document request has been successfully submitted and recorded. To begin the verification and processing of your document, please proceed to the payment section. Thank you!.</p>
                
                <div class='trn-box'>ID: $transaction_id</div>

                <form action='payment.php' method='POST'>
                    <input type='hidden' name='id' value='$last_id'>
                    <button type='submit' class='btn-pay'>
                        <i class='fa-solid fa-credit-card'></i> Proceed to Payment
                    </button>
                </form>
                <a href='dashboard.php' style='display:block; margin-top:20px; color:#b2bec3; text-decoration:none; font-size:14px;'>Pay Later</a>
            </div>
        </body>
        </html>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    header("Location: dashboard.php");
}
?>