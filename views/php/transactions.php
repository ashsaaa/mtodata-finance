<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance</title>
    <link href="../../assets/css/finance.css" rel="stylesheet" />
</head>

<body>
    <?php
    include 'db_conn.php';

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Remove deleted data from transaction_finance
    $deleteSql = "DELETE tf FROM transaction_finance tf
              LEFT JOIN transaction_donation td ON tf.transaction_code = td.transaction_code
              LEFT JOIN transaction_contribution tc ON tf.transaction_code = tc.transaction_code
              LEFT JOIN transaction_expenses te ON tf.transaction_code = te.transaction_code
              LEFT JOIN transaction_payment tp ON tf.transaction_code = tp.transaction_code
              WHERE td.transaction_code IS NULL
                AND tc.transaction_code IS NULL
                AND te.transaction_code IS NULL
                AND tp.transaction_code IS NULL";

    $deleteResult = $conn->query($deleteSql);

    if ($deleteResult === false) {
        die("Error executing the query: " . $conn->error);
    }

    $sql = "INSERT INTO transaction_finance (amount, transaction_code, account_type, transaction_date, date_created) 
        SELECT amount, transaction_code, transaction_type, date_created, date_created FROM transaction_donation
        WHERE NOT EXISTS (
            SELECT 1 FROM transaction_finance WHERE transaction_code = transaction_donation.transaction_code
        )
        UNION ALL
        SELECT amount, transaction_code, transaction_type, date_created, date_created FROM transaction_contribution
        WHERE NOT EXISTS (
            SELECT 1 FROM transaction_finance WHERE transaction_code = transaction_contribution.transaction_code
        )
        UNION ALL
        SELECT amount, transaction_code, transaction_type, date_created, date_created FROM transaction_expenses
        WHERE NOT EXISTS (
            SELECT 1 FROM transaction_finance WHERE transaction_code = transaction_expenses.transaction_code
        )
        UNION ALL
        SELECT amount, transaction_code, transaction_type, date_created, date_created FROM transaction_payment
        WHERE NOT EXISTS (
            SELECT 1 FROM transaction_finance WHERE transaction_code = transaction_payment.transaction_code
        )";

    $result = $conn->query($sql);

    if ($result === false) {
        die("Error executing the query: " . $conn->error);
    }

    // Fetch inserted data
    $selectSql = "SELECT *, DATE_FORMAT(date_created, '%Y-%m-%d') AS formatted_date FROM transaction_finance";
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
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
                <th>Date Created</th>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>";

        $balance = 0;

        while ($row = $selectResult->fetch_assoc()) {

            $balance += $row['debit'] - $row['credit'];

            if ($selectResult === false) {
                die("Error executing the query: " . $conn->error);
            }

            if ($row['account_type'] === 'Donation' || $row['account_type'] === 'Contribution' || $row['account_type'] === 'Renewal' || $row['account_type'] === 'New Member') {
                $add2debit = "UPDATE transaction_finance SET debit = " . $row['amount'] . " WHERE transaction_code = '" . $row['transaction_code'] . "'";
                $addResult = $conn->query($add2debit);
                if ($addResult === false) {
                    die("Error executing the query: " . $conn->error);
                }
            } else {
                $add2credit = "UPDATE transaction_finance SET credit = " . $row['amount'] . " WHERE transaction_code = '" . $row['transaction_code'] . "'";
                $addResult = $conn->query($add2credit);
                if ($addResult === false) {
                    die("Error executing the query: " . $conn->error);
                }
            }

            echo "
        <tr>
            <td>" . $row["ID"] . "</td>
            <td>" . $row["transaction_code"] . "</td>
            <td>" . $row["account_type"] . "</td>
            <td>" . $row["amount"] . "</td>
            <td>" . $row["transaction_date"] . "</td>
            <td>" . $row["debit"] . "</td>
            <td>" . $row["credit"] . "</td>
            <td>" . $balance . "</td>
            <td>" . $row["formatted_date"] . "</td>
            <td>" . $row["user_name"] . "</td>
        </tr>";
        }

        echo "
        </tbody>
    </table>
    <h1>Funds: $balance</h1>";
    }

    $conn->close();
    ?>

</body>

</html>