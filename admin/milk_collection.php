<?php

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title ="Milk Collection";

$error= "";
$success = "";

$rate_query= $conn->query(
    "SELECT base_price, fat_multiplier, snf_multiplier 
    FROM rate_setting
    ORDER BY rate_id DESC
    LIMIT 1"
);

if($rate_query->num_rows===0){
    die("Milk rate settings are not configured.");
}

$rate_setting =$rate_setting["base_price"];
$fat_multiplier=$rate_setting["fat_multiplier"];
$snf_multiplier= $rate_setting["snf_multiplier"];

if($_SERVER["REQUEST_METHOD"]==="POST"){
 $farmer_id=intval($_POST["farmer_id"] ?? 0);
 $collection_data= $_POST["collection_data"]??"";
 $shift= $_POST["shift"] ?? "";
 $quantity = floatval($_POST["quantity"] ?? 0);
 $fat = floatval($_POST["fat"]?? 0);
 $snf = floatval($_POST["snf"] ?? 0);
 
 if(
    $farmer_id <=0 ||
    $collection_data === ""||
    $shift === ""||
    $quantity <=0 ||
    $fat < 0 ||
    $snf <0
 ){
    $error = "Please enter all required info.";
 }elseif(!in_array($shift,["Morining","Evening"])){
    $error ="Invalid shift selected.";
 }else {
    $rate=$base_price + ($fat * $fat_multiplier) + ($snf* $snf_multiplier);

    $amount= $quantity *$rate;

    $stmt = $conn->prepare(
        "INSERT INTO milk_collections
        (
        farmer_id,
        collection_date,
        shift,
        quantity,
        fat,
        snf,
        rate,
        amount
        )
        VALUES(?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issdddd d"
        $farmer_id,
        $collection_id,
        $shift,
        $quantity,
        $fat,
        $snf,
        $rate,
        $amount
    
    );

    if(stmt->execute()){
        $success = "Milk collection saved successfully.";
    }else{
        $error="Unable to save milk collection.";
    }
    $stmt->close();
 }
}

$farmers= $conn->query(
    "SELECT farmer_id, farmer_code, name
    FROM farmers
    WHERE status= 'Active'
    ORDER BY name ASC"
);

?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Collection- Dairy Management System</title>

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
                <p>Record of milk supplied by farmers.</p>
            </div>
        </div>

        <?php if($error !==""): ?>
        </main>
        </div>
    </div>
    
</body>
</html>