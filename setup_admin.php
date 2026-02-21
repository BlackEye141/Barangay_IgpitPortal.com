<?php
include 'db.php';

// I-set diri ang imong gusto nga Admin account
$name = "Admin";
$email = "admin@test.com";
$pass = "admin123"; 
$role = "admin";

// I-check kung naa na ang email para dili mag-duplicate
$check = $conn->query("SELECT * FROM users WHERE email = '$email'");

if ($check->num_rows == 0) {
    $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')";
    if ($conn->query($sql)) {
        echo "<h1>✅ Admin account created!</h1>";
        echo "<p>Email: $email | Pass: $pass</p>";
        echo "<a href='index.php'>Go to Login</a>";
    }
} else {
    echo "<h1>Admin already exists!</h1>";
}
?>