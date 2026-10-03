<?php 

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Products";

$stmt = $conn-> prepare(
    "SELECT 
    product_id,
    product_name,
    unit,
    quantity,
    minimum_stock
    FROM products
    ORDER BY product_name ASC"
);

$stmt->execute();
$products= $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product - Dairy Management System</title>

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
                <h2>Products</h2>

                <p>Manage dairy products and their stock information.</p>
            </div>

            <a href="add_product.php" class="primary-btn">
                + Add Product
            </a>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>Product List</h3>
            </div>
            <div class="table-container">

            <table>
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Minimum Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $sn =1; ?>

                    <?php if ($products-> num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo $sn++; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($product["product_name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($product["unit"]); ?>
                                </td>

                                <td> 
                                    <?php echo number_format($product["quantity"], 2); ?>
                                </td>

                                <td>
                                    <?php echo number_format($product["minimum_stock"], 2); ?>
                                </td>

                                <td>
                                    <?php echo number_format($product["minimum_stock"],2); ?>
                                </td>

                                <td>
                                     <a href="edit_product.php?id=<?php echo $product["product_id"]; ?>" class="edit-btn">
                                        Edit
                                     </a>
                                </td>
                            </tr>
                        
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6"> No product found.</td>
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