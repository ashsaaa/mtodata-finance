<?php

include 'db_conn.php';

// Retrieve form data
$memberId = $_POST['memid'];
$type = $_POST['type'];
$amount = $_POST['amount'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate the transaction code based on the account type
$dateToday = date('mdy');
$transactionCode = '';
$trantype = '';

if ($type === 'RNW') {
    $trantype = 'Renewal';
    $sql = "INSERT INTO transaction_payment (member_id, amount, transaction_code, transaction_type) VALUES ('$memberId', '$amount', '', '$trantype')";
} elseif ($type === 'NEW') {
    $trantype = 'New Member';
    $sql = "INSERT INTO transaction_payment (member_id, amount, transaction_code, transaction_type) VALUES ('$memberId', '$amount', '', '$trantype')";
} else {
    echo "Invalid transaction type.";
    exit();
}

// Insert data into database
if ($conn->query($sql) === TRUE) {
    // Get the auto-incrementing ID of the inserted row
    $lastInsertedId = $conn->insert_id;
    
    // Calculate the incrementing number with leading zeros
    $incrementingNumber = str_pad($lastInsertedId, 3, '0', STR_PAD_LEFT);
    
    // Generate the transaction code based on the account type
    if ($type === 'RNW') {
        $transactionCode = "RNW{$dateToday}{$incrementingNumber}";
    } elseif ($type === 'NEW') {
        $transactionCode = "NEW{$dateToday}{$incrementingNumber}";
    }
    
    // Update the transaction code in the database
    $updateSql = "UPDATE transaction_payment SET transaction_code = '$transactionCode' WHERE id = $lastInsertedId";

    if ($conn->query($updateSql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error updating data: " . $conn->error;
    }
} else {
    echo "Error inserting data: " . $conn->error;
}

$conn->close();

?>
