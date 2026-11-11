
<?php
require_once "../config/database.php";
require_once "../includes/auth_farmer.php";

$farmer_id = $_SESSION["farmer_id"];

$stmt = $conn->prepare("
    SELECT collection_id, collection_date, shift,
           quantity, fat, snf, rate, amount, payment_status
    FROM milk_collections
    WHERE farmer_id = ?
    ORDER BY collection_date DESC, collection_id DESC
");

$stmt->bind_param("i", $farmer_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Collection History</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="farmer-layout">

    <?php include "sidebar.php"; ?>

    <div class="farmer-main-content">

        <h2>My Collection History</h2>

        <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Quantity (L)</th>
                        <th>FAT (%)</th>
                        <th>SNF (%)</th>
                        <th>Rate (Rs.)</th>
                        <th>Amount (Rs.)</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php $sn = 1; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>
                                <td><?php echo $sn++; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row["collection_date"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["shift"]); ?>
                                </td>

                                <td><?php echo number_format($row["quantity"], 2); ?></td>

                                <td><?php echo number_format($row["fat"], 2); ?></td>

                                <td><?php echo number_format($row["snf"], 2); ?></td>

                                <td><?php echo number_format($row["rate"], 2); ?></td>

                                <td><?php echo number_format($row["amount"], 2); ?></td>

                                <td>
                                    <?php echo htmlspecialchars($row["payment_status"]); ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="9">No collection records found.</td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>
