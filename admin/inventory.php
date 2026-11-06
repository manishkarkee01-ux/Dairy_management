<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Inventory";

$stmt = $conn->prepare(
    "SELECT
        i.inventory_id,
        i.transaction_type,
        i.quantity,
        i.transaction_date,
        i.remarks,
        p.product_name,
        p.unit
     FROM inventory i
     INNER JOIN products p
        ON i.product_id = p.product_id
     ORDER BY
        i.transaction_date DESC,
        i.inventory_id DESC"
);

$stmt->execute();

$inventory = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Inventory - Dairy Management System
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content">

        <?php include "../includes/header.php"; ?>

        <main class="dashboard-content">

            <div class="page-header">

                <div>

                    <h2>Inventory</h2>

                    <p>
                        Manage stock in and stock out transactions.
                    </p>

                </div>

                <a
                    href="add_inventory.php"
                    class="primary-btn"
                >
                    + Add Transaction
                </a>

            </div>


            <div class="table-card">

                <div class="table-header">

                    <h3>Inventory Records</h3>

                </div>


                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>S.N.</th>

                                <th>Product</th>

                                <th>Type</th>

                                <th>Quantity</th>

                                <th>Date</th>

                                <th>Remarks</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php $sn = 1; ?>

                        <?php if ($inventory->num_rows > 0): ?>

                            <?php while (
                                $row = $inventory->fetch_assoc()
                            ): ?>

                                <tr>

                                    <td>
                                        <?php echo $sn++; ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["product_name"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["transaction_type"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo number_format(
                                            $row["quantity"],
                                            2
                                        );

                                        echo " " .
                                            htmlspecialchars(
                                                $row["unit"]
                                            );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["transaction_date"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["remarks"] ?? ""
                                        );
                                        ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6">
                                    No inventory records found.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </main>

    </div>

</div>

</body>
</html>