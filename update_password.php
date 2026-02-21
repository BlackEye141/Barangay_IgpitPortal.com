<?php
session_start();
include 'db.php';

// Include SweetAlert2 globally for the response
echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: "Inter", sans-serif; }</style>
</head>
<body>';

if(isset($_POST['new_password']) && isset($_SESSION['reset_email'])){
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = $_SESSION['reset_email'];

    // Validation: Check if passwords match
    if($new_pass !== $confirm_pass) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Match Error',
                text: 'Passwords do not match. Please re-type your new password.',
                confirmButtonColor: '#1e3799'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit();
    }

    // Update the password in the database
    $sql = "UPDATE users SET password='$new_pass' WHERE email='$email'";

    if($conn->query($sql)){
        // Clear the session for security
        unset($_SESSION['reset_email']);
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Password Updated!',
                text: 'Your password has been changed successfully. You can now log in with your new credentials.',
                confirmButtonColor: '#1e3799'
            }).then(() => {
                window.location.href='index.php';
            });
        </script>";
    } else {
        echo "Database Error: " . $conn->error;
    }
} else {
    header("Location: index.php");
}
echo '</body></html>';
?>