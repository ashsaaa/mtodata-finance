<?php

include 'db_conn.php';

// Retrieve form data
$body = $_POST['body_no'];
$for_date = $_POST['date'];
$amount = $_POST['amount'];


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into database
$sql = "INSERT INTO `transaction_contribution` (body_no, amount, for_date) VALUES ('$body', '$amount', '$for_date')";

if ($conn->query($sql) === TRUE) {
    // Get the auto-incrementing ID of the inserted row
    $lastInsertedId = $conn->insert_id;

    // Calculate the incrementing number with leading zeros
    $incrementingNumber = str_pad($lastInsertedId, 3, '0', STR_PAD_LEFT);

    // Generate the transaction code
    $dateToday = date('mdy');
    $transactionCode = "CON{$dateToday}{$incrementingNumber}";

    // Update the transaction code in the database
    $updateSql = "UPDATE `transaction_contribution` SET transaction_code = '$transactionCode' WHERE id = $lastInsertedId";

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