<?php
session_start();
include 'db.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id']) && isset($_SESSION['user_email'])) {
    $id = intval($_GET['id']);
    $user_email = $_SESSION['user_email'];

  
    $sql = "DELETE FROM document_requests WHERE id = ? AND email = ? AND payment_status = 'Unpaid'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $user_email);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: track_documents.php?msg=deleted");
        } else {
            
            die("Error: Walay na-delete. I-check ang payment_status sa DB kung 'Unpaid' ba gyud ang spelling.");
        }
    } else {
        die("Database Error: " . $conn->error);
    }
    exit();
} else {
    die("Error: Walay ID o Email nga nadawat.");
}
?>