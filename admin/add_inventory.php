<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Add Inventory";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_id = intval($_POST["product_id"] ?? 0);
    $transaction_type = $_POST["transaction_type"] ?? "";
    $quantity = floatval($_POST["quantity"] ?? 0);
    $transaction_date = $_POST["transaction_date"] ?? "";
    $remarks = trim($_POST["remarks"] ?? "");

    if (
        $product_id <= 0 ||
        $transaction_type === "" ||
        $quantity <= 0 ||
        $transaction_date === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (
        $transaction_type !== "IN" &&
        $transaction_type !== "OUT"
    ) {

        $error = "Invalid transaction type.";

    } else {

        /* Get current product quantity */

        $product_stmt = $conn->prepare(
            "SELECT quantity
             FROM products
             WHERE product_id = ?"
        );

        $product_stmt->bind_param(
            "i",
            $product_id
        );

        $product_stmt->execute();

        $product_result = $product_stmt->get_result();

        if ($product_result->num_rows === 0) {

            $error = "Product not found.";

        } else {

            $product = $product_result->fetch_assoc();

            $current_quantity = floatval(
                $product["quantity"]
            );


            /* Check OUT quantity */

            if (
                $transaction_type === "OUT" &&
                $quantity > $current_quantity
            ) {

                $error = "Stock OUT quantity cannot be greater than available stock.";

            } else {

                if ($transaction_type === "IN") {

                    $new_quantity =
                        $current_quantity + $quantity;

                } else {

                    $new_quantity =
                        $current_quantity - $quantity;
                }


                /* Start transaction */

                $conn->begin_transaction();

                try {

                    /* Insert inventory record */

                    $inventory_stmt = $conn->prepare(
                        "INSERT INTO inventory
                        (
                            product_id,
                            transaction_type,
                            quantity,
                            transaction_date,
                            remarks
                        )
                        VALUES (?, ?, ?, ?, ?)"
                    );

                    $inventory_stmt->bind_param(
                        "isdss",
                        $product_id,
                        $transaction_type,
                        $quantity,
                        $transaction_date,
                        $remarks
                    );

                    if (!$inventory_stmt->execute()) {
                        throw new Exception(
                            "Unable to save inventory transaction."
                        );
                    }

                    $inventory_stmt->close();


                    /* Update product quantity */

                    $update_stmt = $conn->prepare(
                        "UPDATE products
                         SET quantity = ?
                         WHERE product_id = ?"
                    );

                    $update_stmt->bind_param(
                        "di",
                        $new_quantity,
                        $product_id
                    );

                    if (!$update_stmt->execute()) {
                        throw new Exception(
                            "Unable to update product quantity."
                        );
                    }

                    $update_stmt->close();


                    /* Save both changes */

                    $conn->commit();

                    header(
                        "Location: inventory.php?saved=1"
                    );

                    exit();

                } catch (Exception $e) {

                    $conn->rollback();

                    $error = $e->getMessage();
                }
            }
        }

        $product_stmt->close();
    }
}


/* Get products */

$products = $conn->query(
    "SELECT
        product_id,
        product_name,
        unit,
        quantity
     FROM products
     ORDER BY product_name ASC"
);

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
        Add Inventory - Dairy Management System
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

                    <h2>Add Inventory</h2>

                    <p>
                        Record a stock in or stock out transaction.
                    </p>

                </div>

                <a
                    href="inventory.php"
                    class="secondary-btn"
                >
                    ← Back
                </a>

            </div>


            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php endif; ?>


            <div class="form-card">

                <h3>Inventory Information</h3>

                <form
                    action=""
                    method="post"
                >

                    <div class="form-group">

                        <label for="product_id">
                            Product *
                        </label>

                        <select
                            name="product_id"
                            id="product_id"
                            required
                        >

                            <option value="">
                                Select Product
                            </option>

                            <?php while (
                                $product =
                                $products->fetch_assoc()
                            ): ?>

                                <option
                                    value="<?php
                                        echo $product["product_id"];
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $product["product_name"]
                                    );
                                    ?>

                                    -
                                    <?php
                                    echo htmlspecialchars(
                                        $product["unit"]
                                    );
                                    ?>

                                    (Stock:
                                    <?php
                                    echo number_format(
                                        $product["quantity"],
                                        2
                                    );
                                    ?>
                                    )

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="transaction_type">
                                Transaction Type *
                            </label>

                            <select
                                name="transaction_type"
                                id="transaction_type"
                                required
                            >

                                <option value="">
                                    Select Type
                                </option>

                                <option value="IN">
                                    Stock IN
                                </option>

                                <option value="OUT">
                                    Stock OUT
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="quantity">
                                Quantity *
                            </label>

                            <input
                                type="number"
                                name="quantity"
                                id="quantity"
                                step="0.01"
                                min="0.01"
                                placeholder="Enter quantity"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="transaction_date">
                            Transaction Date *
                        </label>

                        <input
                            type="date"
                            name="transaction_date"
                            id="transaction_date"
                            value="<?php
                                echo date("Y-m-d");
                            ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="remarks">
                            Remarks
                        </label>

                        <textarea
                            name="remarks"
                            id="remarks"
                            placeholder="Enter remarks"
                        ></textarea>

                    </div>


                    <div class="form-actions">

                        <a
                            href="inventory.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Save Transaction
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>