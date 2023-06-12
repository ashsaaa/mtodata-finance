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

$sql = "INSERT INTO `transaction_finance` (amount, transaction_code, transaction_type, for_month) 
SELECT transaction_code, amount, date_created, user_name FROM transaction_donation
UNION ALL
SELECT transaction_code, amount, date_created, user_name FROM transaction_contribution
UNION ALL
SELECT transaction_code, amount, date_created, user_name FROM transaction_expenses
UNION ALL
SELECT transaction_code, amount, date_created, user_name FROM transaction_payment
UNION ALL";

$conn->close();

?>

