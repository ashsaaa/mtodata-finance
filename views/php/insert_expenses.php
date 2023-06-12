<?php

include 'db_conn.php';

// Retrieve form data
$type = $_POST['type'];
$amount = $_POST['amount'];
$month = $_POST['formonth'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate the transaction code based on the account type
$dateToday = date('mdy');
$transactionCode = '';
$trantype = '';

if ($type === 'ELE') {
    $trantype = 'Expenses - Electricity';
    $sql = "INSERT INTO transaction_expenses (amount, transaction_code, transaction_type, for_month) VALUES ('$amount', '', '$trantype', '$month')";
} elseif ($type === 'REN') {
    $trantype = 'Expenses - Rent';
    $sql = "INSERT INTO transaction_expenses (amount, transaction_code, transaction_type, for_month) VALUES ('$amount', '', '$trantype', '$month')";
    
} elseif ($type === 'WAT') {
    $trantype = 'Expenses - Water';
    $sql = "INSERT INTO transaction_expenses (amount, transaction_code, transaction_type, for_month) VALUES ('$amount', '', '$trantype', '$month')";
    
} elseif ($type === 'PRO') {
    $trantype = 'Programs';
    $sql = "INSERT INTO transaction_expenses (amount, transaction_code, transaction_type, for_month) VALUES ('$amount', '', '$trantype', '$month')";
    
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
    if ($type === 'ELE') {
        $transactionCode = "ELE{$dateToday}{$incrementingNumber}";
    } elseif ($type === 'REN') {
        $transactionCode = "REN{$dateToday}{$incrementingNumber}";
    } elseif ($type === 'WAT') {
        $transactionCode = "WAT{$dateToday}{$incrementingNumber}";
    } elseif ($type === 'PRO') {
        $transactionCode = "PRO{$dateToday}{$incrementingNumber}";
    }
    
    // Update the transaction code in the database
    $updateSql = "UPDATE transaction_expenses SET transaction_code = '$transactionCode' WHERE id = $lastInsertedId";

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
