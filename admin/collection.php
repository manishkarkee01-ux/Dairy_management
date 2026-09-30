<?php

require_once "../includes/auth_admin.php";
require_once"../config/database.php";

$page_title= "Milk Collection"

$message= "";

if(isset($_GET["saved"]) && $_GET["saved"]=== "1"){
    $message= "Milk collection saved successfully";
}

$stmt = $conn->prepare(
    "SELECT 
    mc.collection_id,
    mc.collection_date,
    mc.shift,
    mc.quantity,
    mc.fat,
    mc.snf,
    mc.rate,
    mc.amount,
    mc.payment_status,
    f.farmer_code, 
    f.name
    FROM milk_collections mc
    INNER JOIN farmers f
    ON mc.farmer_id= f.farmer_id
    ORDER BY
    mc.collection_date DESC
    mc.collection_id DESC"
);

$stmt->ececute();

$collections= $stmt ->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Collection -Dairy Management System</title>

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
                    <h2>Milk Collection</h2>
                    <p>view and manage farmer milk collection records.</p>
                </div>

                <a href="milk_collection.php" class="primary-btn"> + Add Collection</a>
            </div>

            <?php if($message !==""): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                 <?php endif; ?>

                 <div class="dashboard-section">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Farmer</th>
                                    <th>Date</th>
                                    <th>Shift</th>
                                    <th>Quantity</th>
                                    <th>FAT %</th>
                                    <th>SNF %</th>
                                    <th>Rate/L</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                
                                </tr>
                            </thead>

                            <tbody>
                                <?php if($collections->num_rows > 0): ?>
                                    <?php while($row =$collection->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row["farmer_code"]); ?></strong> <br>
                                                <small><?php echo htmlspecialchars($row["name"]); ?></small>
                                            </td>

                                            <td><?php echo htmlspecialchars($row["collection_date"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["shift"]); ?></td>
                                            <td><?php echo number_format($row["quantity"],2); ?>L</td>
                                            <td><?php echo number_format($row["fat"],2); ?>%</td>
                                            <td><?php echo number_format($row["snf"],2); ?>%</td>
                                            <td><?php echo number_format($row["rate"],2); ?></td>
                                            <td><strong>NPR<?php echo number_format($row["amount"],2);?></strong></td>

                                        </tr>
                            </tbody>
                        </table>
                    </div>
                 </div>
            </main>
        </div>
    </div>
    
</body>
</html>