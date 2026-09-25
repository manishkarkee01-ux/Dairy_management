<?php
require_once"../includes/auth_admin.php";
require_once"../config/database.php";

$page_title = "Dashboard";
?>

<!Doctype html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dairy Management System -Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include "../includes/sidebar.php";?>

        <div class="main-content">
            <?php include "../includes/header.php";?>
            <main class="dashboard-content">

            <div class="welcome-section">

            <div>
                <h2>
                    welcome,
                    <?php echo htmlspecialchars($_SESSION["user_name"]);?>!
                </h2>

                <p>
                    Here's what's happening in your dairy today.
               </p>

            </div>
            </div>

            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class ="card- icon farmer-icon">
                        F
                    </div>
                    <div>
                        <span>Total Farmers</span>

                        <h3>
                            <?php
                            $result = $conn->query(
                                "SELECT COUNT(*) AS total
                                FROM farmers
                                WHERE status= 'Active'"
                            );

                            $row = $result->fetch_assoc();

                            echo $row["total"];
                            ?>
                        </h3>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon milk-icon">
                        M
                    </div>
                    <div>
                        <span>Today's Milk</span>
                        <h3>
                            <?php
                            $stmt= $conn->prepare(
                                "SELECT COALESCE(SUM(quantity),0) AS Total
                                FROM milk_collections
                                WHERE collection_date= CURDATE()"
                            );
                            $stmt->execute();
                            $result= $stmt->get_result();
                            $row =$result->fetch_assoc();

                            echo number_format(
                                $row["total"],
                                2
                            );
                            ?>
                            L
                        </h3>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon payment-icon">
                         ₹
                    </div>
                    <div>
                        <span>Unpaid Amount</span>
                        <h3>
                            NPR

                            <?php 
                            $stmt= $conn->prepare(
                                "SELECT COALESCE(SUM(amount),0) AS total
                                FROM milk_collections
                                WHERE payment_status='Unpaid'"
                            );

                            $stmt->execute();

                            $result= $stmt->get_result();
                            $row =$result->fetch_assoc();

                            echo number_format(
                                $row["total"],
                                2
                            );
                            ?>
                        </h3>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon product-icon">
                        P
                    </div>
                    <div>
                        <span>
                            Total Products
                        </span>
                        <h3>
                            <?php
                            $result= $conn->query(
                                "SELECT COUNT(*) AS total
                                FROM products"
                            );

                            $row =$result->fetch_assoc();
                            echo $row["total"];
                            ?>
                        </h3>
                    </div>
                </div>

            </div>

            <div class="dashboard-section">
                <div class="section-header">
                    <h2>Recent Milk Collections</h2>

                    <a href="collection.php">
                        View All
                    </a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Farmer</th>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Quantity</th>
                                <th>Fat%</th>
                                <th>Snf%</th>
                                <th>Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt= $conn->prepare(
                                "SELECT
                                f.name,
                                mc.collection_date,
                                mc.shift,
                                mc.quantity,
                                mc.fat,
                                mc.snf,
                                mc.amount
                                FROM milk_collections mc 
                                INNER JOIN farmers f
                                ON mc.farmer_id=f.farmer_id
                                ORDER BY mc.collection_id DESC
                                LIMIT 5"

                            );

                            $stmt->execute();
                            $result=$stmt->get_result();

                            if($result->num_rows>0):
                                while($row=$result->fetch_assoc()):
                                ?>

                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row["name"]);?>
                                    </td>
                                    
                                    <td>
                                        <?php echo htmlspecialchars($row["collection_date"]);?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($row["shift"]);?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row["quantity"],2);?>L
                                    </td>
                                    <td>
                                        <?php echo number_format($row["fat"],2);?>%
                                    </td>
                                    <td>
                                        <?php echo number_format($row["snf"],2);?>%
                                    </td>
                                    <td>
                                        NPR<?php echo number_format($row["amount"],2);?>
                                    </td>

                                </tr>

                                <?php
                                endwhile;
                            else:
                                ?>

                                <tr>
                                    <td colspan="7" class="empty-state">
                                        No milk collection records found.
                                    </td>
                                </tr>
                                <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>

      </main>
                <?php include"../includes/footer.php";?>
            
        </div>
    </div>
    
</body>
</html>