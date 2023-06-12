<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance</title>
</head>

<body>
    <?php
    include 'db_conn.php';

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO transaction_finance (amount, transaction_code, account_type, transaction_date) 
            SELECT amount, transaction_code, transaction_type AS account_type, date_created AS transaction_date FROM transaction_donation
            UNION ALL
            SELECT amount, transaction_code, transaction_type AS account_type, date_created AS transaction_date  FROM transaction_contribution
            UNION ALL
            SELECT amount, transaction_code, transaction_type AS account_type, date_created AS transaction_date  FROM transaction_expenses
            UNION ALL
            SELECT amount, transaction_code, transaction_type AS account_type, date_created AS transaction_date  FROM transaction_payment";

    $result = $conn->query($sql);

    if ($result === false) {
        die("Error executing the query: " . $conn->error);
    }

    // Fetch inserted data
    $selectSql = "SELECT * FROM transaction_finance";
    $selectResult = $conn->query($selectSql);

    if ($selectResult === false) {
        die("Error executing the query: " . $conn->error);
    }

    if ($selectResult->num_rows === 0) {
        echo "No rows found.";
    } else {
        echo "
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Transaction Code</th>
                    <th>Account Type</th>
                    <th>Amount</th>
                    <th>Transaction Date</th>
                    <th>Date Created</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>";

        while ($row = $selectResult->fetch_assoc()) {
            echo "
            <tr>
                <td>" . $row["ID"] . "</td>
                <td>" . $row["transaction_code"] . "</td>
                <td>" . $row["account_type"] . "</td>
                <td>" . $row["amount"] . "</td>
                <td>" . $row["transaction_date"] . "</td>
                <td>" . $row["date_created"] . "</td>
                <td>" . $row["user_name"] . "</td>
            </tr>";
        }

        echo "
            </tbody>
        </table>";
    }

    $conn->close();
    ?>
</body>

</html>