<?php
 require_once "../config/database.php";
 require_once "../includes/auth_farmer.php";

 $farmer_id = $_SESSION["farmer_id"];

 $total_stmt = $conn->prepare("
 SELECT COALESCE(SUM(quantity),0) AS total
 FROM milk_collections
 WHERE farmer_id = ?
 ");

 $total_stmt->bind_param("i", $farmer_id);
 $total_stmt->execute();
 $total_milk = $total_stmt->get_result()->fetch_assoc()["total"];

 $amount_stmt = $conn->prepare("
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM milk_collections
    WHERE farmer_id = ?
");
 $amount_stmt->bind_param("i", $farmer_id);
 $amount_stmt->execute();
 $total_amount = $amount_stmt->get_result()->fetch_assoc()["total"];


 $payment_stmt = $conn->prepare("
 SELECT COALESCE(SUM(amount), 0 ) AS total
 FROM  payments
 WHERE farmer_id = ?
 ");

 $payment_stmt->bind_param("i", $farmer_id);
 $payment_stmt->execute();
 $total_paid = $payment_stmt-> get_result()-> fetch_assoc()["total"];
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
 </head>
 <body>
    <div class="farmer-dashboard">
        <h2>Welcome to Farmer Portal</h2>

        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Total Milk Collected</h3>

                <h2><?php echo number_format($total_milk,2); ?>L</h2>
            </div>

            <div class="dashboard-card">
                <h3>Total Amount Earned</h3>
                <h2>Rs. <?php echo number_format($total_amount,2); ?></h2>
            </div>

            <div class="dashboard-card">
                <h3>Total Payment Received</h3>
                <h2>Rs. <?php echo number_format($total_paid,2);?></h2>
            </div>

        </div>
    </div>
    
 </body>
 </html>