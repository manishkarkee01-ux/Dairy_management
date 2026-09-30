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

$rate_setting =$rate_query->fetch_assoc();

$base_price =$rate_setting["base_price"];
$fat_multiplier=$rate_setting["fat_multiplier"];
$snf_multiplier= $rate_setting["snf_multiplier"];

if($_SERVER["REQUEST_METHOD"]==="POST"){
 $farmer_id=intval($_POST["farmer_id"] ?? 0);
 $collection_date= $_POST["collection_date"]??"";
 $shift= $_POST["shift"] ?? "";
 $quantity = floatval($_POST["quantity"] ?? 0);
 $fat = floatval($_POST["fat"]?? 0);
 $snf = floatval($_POST["snf"] ?? 0);
 
 if(
    $farmer_id <=0 ||
    $collection_date === ""||
    $shift === ""||
    $quantity <=0 ||
    $fat < 0 ||
    $snf <0
 ){
    $error = "Please enter all required info.";
 }elseif(!in_array($shift,["Morning","Evening"])){
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
        "issddddd",
        $farmer_id,
        $collection_date,
        $shift,
        $quantity,
        $fat,
        $snf,
        $rate,
        $amount
    
    );

    if($stmt->execute()){
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
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

            <?php endif; ?>

            <?php if ($success !== ""): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success); ?>

                </div>
                <?php endif; ?>

                <div class="form-card">
                    <h3>Record Milk Collection</h3>
                    <form  method="post">

                    <div class="form-group">
                        <label >Farmer*</label>

                        <select name="farmer_id" required>
                            <option value="">Select Farmer</option>
                            <?php while ($farmer =$farmers->fetch_assoc()): ?>

                                <option value="<?php echo $farmer["farmer_id"]; ?>">
                                    <?php echo htmlspecialchars($farmer["farmer_code"]); ?> -

                                    <?php echo htmlspecialchars($farmer["name"]); ?>
                                </option>

                                <?php endwhile; ?>

                            
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label >Collection Date *</label>

                            <input type="date" name="collection_date" value="<?php data("Y-m-d"); ?>" required>
                        </div>

                        <div class="form-group">
                            <label >Shift*</label>

                            <select name="shift" required>

                            <option value="">Select Shift</option>

                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">

                        <label for="">Quantity (liter) *</label>

                        <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" placeholder="enter quantity" required>
                        </div>

                        <div class="form-group">
                            <label for="">FAT(%)*</label>
                            <input type="number" name="fat" id="fat" step="0.01" min="0" placeholder="Enter fat" required>
                        </div>

                         <div class="form-group">
                            <label for="">SNF(%)*</label>
                            <input type="number" name="snf" id="snf" step="0.01" min="0" placeholder="Enter snf" required>
                        </div>


                    </div>

                    <div class="calculation-box">
                        <h3>Calculation</h3>

                        <div class="calculation-row">
                            <span>Base Price</span>

                            <strong>
                                NPR <?php echo number_format($base_price,2); ?>
                            </strong>
                        </div>

                        <div class="calculation-row">
                            <span>Rate/Liter</span>

                            <strong id="rateDisplay">NPR 0.00</strong>
                        </div>

                        <div class="calculation-row total-row">

                        <span>
                            Payable Amount
                        </span>

                        <strong id="amountDisplay">NPR 0.00</strong>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="secondary-btn" onclick="resetCalculation()">
                            Reset
                        </button>

                        <button type="submit" class="primary-btn">Save Collection</button>
                    </div>

                    </form>


                </div>

                
        </main>
        </div>
    </div>

    <script>
        const basePrice =
        <?php echo $base_price; ?>;

        const fatMultiplier=
        <?php echo $fat_multiplier; ?>;

        const snfMultiplier =
        <?php echo $snf_multiplier; ?>;

        function calculateMilkRate(){
            const quantity=
            parseFloat(
                document.getElementById("quantity").value
            )|| 0;

            const fat=
            parseFloat(
                document.getElementById("fat").value
            ) || 0;

            const snf=
            parseFloat(
                document.getElementById("snf").value
            )|| 0;

            const rate = basePrice + (fat* fatMultiplier) + (snf*snfMultiplier);

            const amount = quantity * rate;

            document.getElementById("rateDisplay")
            .innerText =
            "NPR" + rate.toFixed(2);

            document.getElementById("amountDisplay").innerText="NPR" + amount.toFixed(2);
        }

        document.getElementById("quantity").addEventListener("input", calculateMilkRate);

        document.getElementById("fat").addEventListener("input", calculateMilkRate);

        document.getElementById("snf").addEventListener("input",calculateMilkRate);

        function resetCalculation(){
            document.getElementById("rateDisplay").innerText= "NPR 0.00";
        }
    </script>

    
</body>
</html>