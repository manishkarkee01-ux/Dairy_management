<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Edit Product";

$error = "";

$product_id = intval($_GET["id"] ?? 0);

if ($product_id <= 0) {
    header("Location: products.php");
    exit();
}


/* Get existing product */

$stmt = $conn->prepare(
    "SELECT
        product_id,
        product_name,
        unit,
        quantity,
        minimum_stock
     FROM products
     WHERE product_id = ?"
);

$stmt->bind_param(
    "i",
    $product_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

$stmt->close();


/* Update product */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_name = trim($_POST["product_name"] ?? "");
    $unit = trim($_POST["unit"] ?? "");
    $minimum_stock = floatval($_POST["minimum_stock"] ?? 0);

    if (
        $product_name === "" ||
        $unit === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif ($minimum_stock < 0) {

        $error = "Minimum stock cannot be negative.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE products
             SET
                product_name = ?,
                unit = ?,
                minimum_stock = ?
             WHERE product_id = ?"
        );

        $stmt->bind_param(
            "ssdi",
            $product_name,
            $unit,
            $minimum_stock,
            $product_id
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: products.php?updated=1");
            exit();

        } else {

            $error = "Unable to update product.";
        }

        $stmt->close();
    }
}

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
        Edit Product - Dairy Management System
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

                    <h2>Edit Product</h2>

                    <p>
                        Update product information.
                    </p>

                </div>

                <a
                    href="products.php"
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

                <h3>Product Information</h3>

                <form
                    action=""
                    method="post"
                >

                    <div class="form-group">

                        <label for="product_name">
                            Product Name *
                        </label>

                        <input
                            type="text"
                            id="product_name"
                            name="product_name"
                            value="<?php
                                echo htmlspecialchars(
                                    $product["product_name"]
                                );
                            ?>"
                            required
                        >

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="unit">
                                Unit *
                            </label>

                            <select
                                name="unit"
                                id="unit"
                                required
                            >

                                <option value="">
                                    Select Unit
                                </option>

                                <option
                                    value="Liter"
                                    <?php
                                    if ($product["unit"] === "Liter") {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    Liter
                                </option>

                                <option
                                    value="Kg"
                                    <?php
                                    if ($product["unit"] === "Kg") {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    Kg
                                </option>

                                <option
                                    value="Piece"
                                    <?php
                                    if ($product["unit"] === "Piece") {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    Piece
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label>
                                Current Quantity
                            </label>

                            <input
                                type="number"
                                value="<?php
                                    echo $product["quantity"];
                                ?>"
                                step="0.01"
                                readonly
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="minimum_stock">
                            Minimum Stock
                        </label>

                        <input
                            type="number"
                            id="minimum_stock"
                            name="minimum_stock"
                            value="<?php
                                echo $product["minimum_stock"];
                            ?>"
                            step="0.01"
                            min="0"
                        >

                    </div>


                    <div class="form-actions">

                        <a
                            href="products.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Update Product
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>

</div>

</body>
</html>