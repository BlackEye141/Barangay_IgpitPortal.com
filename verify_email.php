<?php
session_start();
include 'db.php';

echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Inter", sans-serif; background: #f4f7f6; }
        /* Custom styling for SweetAlert to match your Igpit Portal */
        .swal2-popup {
            border-radius: 25px !important;
            padding: 2rem !important;
            font-family: "Inter", sans-serif !important;
        }
        .swal2-title {
            color: #1e3799 !important;
            font-weight: 800 !important;
        }
        .swal2-confirm {
            border-radius: 12px !important;
            padding: 12px 30px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
        }
    </style>
</head>
<body>';

if(isset($_POST['email'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($result->num_rows > 0){
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php");
        exit();
    } else {
        // Modernized SweetAlert replaces the ugly browser alert
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'User Not Found',
                text: 'The email address you entered is not in our records.',
                confirmButtonColor: '#1e3799',
                confirmButtonText: 'Try Again',
                backdrop: `rgba(30, 55, 153, 0.1)`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='forgot_password.php';
                }
            });
        </script>";
    }
}
echo '</body></html>';
?>