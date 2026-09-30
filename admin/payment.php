<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title= "Payments";
$message="";

if(isset($_GET["saved"]) && $_GET["saved"]=== "1"){
    $message = "Payment recorded successfully";
}

$stmt = $conn ->prepare(
    "SELECT
    p.payment_id,
    p.payment_date,
    p.amount,
    p.payment_method,
    p.remarks,
    f.farmer_code,
    f.name
    FROM payments p
    INNER JOIN farmers f
    ON p.farmer_id = f.farmer_id
    ORDER BY 
    p.payment_date DESC,
    p.payment_id DESC"
);

$stmt->execute();

$payments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment- Dairy Management System</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    
<div class="admin-layout">
    <?php include "../includes/sidebar.php";?>

    <div class="main-content">
        <?php include "../includes/header.php"; ?>

        <main class="dashboard=content">
            <div class="page-header">

            <div>

            <h2>Payments</h2>
            <p>Manage payment made to farmers.</p>
            </div>

            <a href="add_payment.php" class="primary-btn">+ Add Payment</a>
            </div>

            <?php if($message !== ""): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <div class="dashboard-section">

                <div class="section-header">
                    <h2>
                        Payment Records
                    </h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Date</th>
                                <th>Farmer</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Remarks</th>
                        </tr>
                        </thead>

                        <tbody>
                            <?php if($payments->num_rows > 0): ?>

                                <?php while (
                                    $payment = $payment->fetch_assoc()
                                ): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment["payment_id"]); ?></td>
                                    <td><?php echo htmlspecialchars($payment["payment_date"]); ?></td>
                                    <td> 
                                        <strong>
                                            <?php echo htmlspecialchars($payment["farmer_code"]); ?>
                                        </strong><br>
                                        
                                        <?php htmlspecialchars($payment["name"]); ?>

                                    </td>

                                    <td>
                                        NPR 
                                        <?php echo number_format($payment["amount"],2); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($payment["payment_method"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($payment["remarks"]); ?>
                                    </td>
                                    
                                </tr>

                                <?php endwhile; ?>
                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="empty-state">
                                        No Payment records found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                </div>
        </main>

        <?php include "../includes/footer.php"; ?>
    </div>
</div>
</body>
</html>