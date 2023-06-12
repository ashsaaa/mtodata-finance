<?php

include 'db_conn.php';

// Retrieve form data
$donor = $_POST['donor'];
$amount = $_POST['amount'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into database
$sql = "INSERT INTO transaction_donation (donor_id, amount) VALUES ('$donor', '$amount')";

if ($conn->query($sql) === TRUE) {
    // Get the auto-incrementing ID of the inserted row
    $lastInsertedId = $conn->insert_id;

    // Calculate the incrementing number with leading zeros
    $incrementingNumber = str_pad($lastInsertedId, 3, '0', STR_PAD_LEFT);

    // Generate the transaction code
    $dateToday = date('mdy');
    $transactionCode = "DON{$dateToday}{$incrementingNumber}";

    // Update the transaction code in the database
    $updateSql = "UPDATE transaction_donation SET transaction_code = '$transactionCode' WHERE id = $lastInsertedId";

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