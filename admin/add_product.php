<?php 
require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Add Product";

$error = "";
$success ="";

if($_SERVER["REQUEST_METHOD"]=== "POST"){

$product_name = trim($_POST["product_name"] ?? "");
$unit = trim($_POST["unit"] ?? "");
$quantity = floatval($_POST["quantity"] ?? 0);
$minimum_stock = floatval($_POST["minimum_stock"] ?? 0);

if(
    $product_name === "" ||
    $unit === "" 
){
    $error = "Please fill in all required fields.";
}elseif($quantity < 0 || $minimum_stock < 0){
    $error = "Quantity and minimum stock cannot be negative ";
}else {
    $stmt = $conn ->prepare(
        "INSERT INTO products
        (
        product_name,
        unit,
        quantity,
        minimum_stock
        )
        VALUES(?, ?, ?, ?)"
    );

    $stmt -> bind_param(
        "ssdd",
        $product_name,
        $unit,
        $quantity,
        $minimum_stock
    );

    if($stmt->execute()){
        header("Location: products.php?saved=1");
        exit();
    }else{
        $error = "Unable to save product.";
    }

    $stmt->close();
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Dairy Management System</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include "../includes/sidebar.php"; ?>
        <div class="main-content">
            <?php include "../includes/header.php"; ?>

            <main class="dashboard-content">
                <div class="page-header">

            <div>
                <h2>
                    Add Product
                </h2>

                <p>
                    Add a new dairy product to the system.
                </p>
            </div>

            <a href="products.php" class="secondary-btn">
                 ← Back
            </a>
            </div>

            <?php if ($error!== ""): ?>

                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>

                <?php endif; ?>

                <div class="form-card">
                    <h3>Product Information</h3>
                    <form action="" method="post">
                        <div class="form-group">

                        <label for="product-name">
                            Product Name *
                        </label>

                        <input type="text" id="product_name" name="product_name" placeholder="Enter product name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="unit">
                                    Unit *
                                </label>

                                <select name="unit" id="unit" required>
                                    <option value="">Select Unit</option>
                                    <option value="Liter">Liter</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Piece">Piece</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Initial Quantity</label>
                                <input type="number" id="quantity" name="quantity" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="minimum_stock">Minimum Stock</label>
                            <input type="number" id="minimum_stock" name="minimum_stock" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-actions">
                            <a href="products.php" class="secondary-btn">Cancel</a>
                            <button type="submit" class="primary-btn"> Save Product</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>