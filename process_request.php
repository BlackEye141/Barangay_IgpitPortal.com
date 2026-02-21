<?php
include 'db.php';

if (isset($_POST['submit_request'])) {
    $doc_type = $_POST['document_type'];
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $mname = $_POST['middle_name'];
    $addr = $_POST['address'];
    $contact = $_POST['contact_number'];
    $purpose = $_POST['purpose'];
    $trans_id = "MDC" . strtoupper(uniqid());

    $sql = "INSERT INTO document_requests (document_type, first_name, last_name, middle_name, address, contact_number, purpose, transaction_id) 
            VALUES ('$doc_type', '$fname', '$lname', '$mname', '$addr', '$contact', '$purpose', '$trans_id')";

    if ($conn->query($sql) === TRUE) {
       
        $last_id = $conn->insert_id;
        header("Location: payment.php?id=$last_id");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>