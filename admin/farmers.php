<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title ="Farmers";

$message="";
$error="";

if(isset($_GET["action"]) && isset($_GET["id"])){
    $farmer_id=intval($_GET["id"]);
    $action =$_GET["action"];

    if($action === "deactivate"){
        $status ="Inactive";

    }elseif($action === "activate"){
        $status= "Active";
    }else{
        $status ="";
    }

    if ($status !==""){
        $stmt =$conn->prepare(
            "UPDATE farmers
            SET status =?
            WHERE farmer_id =?"
        );

        $stmt-> bind_param(
            "si",
            $status,
            $farmer_id
        );

        if($stmt->execute()){
            $message = "Farmer status updated successfully.";
        }
        else{
            $error ="Unable to update farmer status.";
        }
     }

}

$stmt =$conn->prepare(
    "SELECT 
    farmer_id,
    farmer_code,
    name,
    phone,
    address,
    email,
    status,
    created_at
    FROM farmers
    ORDER BY farmer_id DESC"
);

$stmt->execute();

$farmers=$stmt->get_result();

?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers- Dairy Management System</title>

    <link rel="stylesheet" href="../assests/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content">
        <?php include "../includes/header.php";?>

        <main class="dashboard-content">

        <div class="page-header">
            <div>
                <h2>Farmers</h2>

                <p>
                    Manage Registered dairy farmers.
                </p>
            </div>

            <a href="add_farmer.php" class="primary-btn">
                + Add Farmer
            </a>
        </div>

        <?php if ($message !== ""): ?>

            <div class="success-message">

            <?php echo htmlspecialchars($message);?>
            </div>

        <?php endif; ?>
        <?php if ($error !==""):?>

            <div class="error-message">
                <?php echo htmlspecialchars($error);?>
            </div>

            <?php endif; ?>

            <div class="dashboard-section">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Farmer Code</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($farmers->num_rows >0): ?>

                                <?php while ($farmer = $farmers->fetch_assoc()):?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <?php
                                                echo htmlspecialchars(
                                                    $farmer["farmer_code"]
                                                );
                                                ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?php echo htmlspexialchars($farmer["name"]);?>
                    
                                        </td>
                                        <td>
                                            <?php echo htmlspexialchars($farmer["phone"]);?>
                    
                                        </td>
                                        <td>
                                            <?php echo htmlspexialchars($farmer["name"]);?>
                    
                                        </td>
                                        <td>
                                            <?php echo htmlspexialchars($farmer["address"]?? "-");?>
                    
                                        </td>
                                        <td>
                                            <?php echo htmlspexialchars($farmer["email"] ?? "-");?>
                    
                                        </td>
                                        <td>
                                            <?php if($farmer["status"]==="Active"): ?>

                                                <span class="status-badge status-active">
                                                    Active

                                                </span>

                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">Inactive</span>

                                                <?php endif; ?>
                    
                                        </td>

                                        <td>
                                            <a 
                                            href="edit_farmer.php?id=<?php echo $farmer["farmer_id"];?>"
                                            class="action-btn edit-btn">Edit</a>

                                            
                                        </td>

                                        
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