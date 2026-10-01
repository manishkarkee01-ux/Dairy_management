<?php 

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Add Payment";

$error ="";
$success = "";

if($_SERVER["REQUEST_METHOD"]=== "POST"){

$farmer_id = intval($_POST["farmer_id"] ?? 0);
$payment_date = $_POST["payment_date"] ?? "";
$amount = floatval($_POST["amount"] ?? 0);
$payment_method = trim($_POST["payment_method"] ?? "");
$remarks = trim($_POST["remarks"] ?? "");


if(
    $farmer_id <= 0 ||
    $payment_date === "" ||
    $amount <= 0 || 
    $payment_method === ""
)
{
    $error = "Please fill in all required fields.";

}else{
    $stmt = $conn ->prepare(
        "INSERT INTO payments
        (
        farmer_id,
        payment_date,
        amount,
        payment_method,
        remarks
    )
    VALUES(?, ?, ?, ?, ?)"
    );

    $stmt-> bind_param(
        "isdss",
        $farmer_id,
        $payment_date,
        $amount,
        $payment_method,
        $remarks
    );

    if($stmt->execute()){
    $remaining_amount = $amount;

    $collection_stmt = $conn->prepare(
        "SELECT 
        collection_id,
        amount
        FROM milk_collections
        WHERE farmer_id = ?
        AND payment_status = 'Unpaid'
        ORDER BY collection_date ASC, collection_id ASC"
    );

    $collection_stmt->bind_param(
        "i",
        $farmer_id

    );

    $collection_stmt -> execute();
    $collections = $collection_stmt->get_result();

    while(
        $collection = $collections->fetch_assoc()
    )
    {
        if($remaining_amount <= 0){
            break;
        }
        if(
            $remaining_amount >= $collection["amount"]
        ){
            $update_stmt = $conn->prepare(
                "UPDATE milk_collections
                SET payment_status = 'Paid'
                WHERE collection_id = ?"
            );

            $update_stmt ->bind_param(
                "i",
                $collection["collection_id"]
            );
            $update_stmt->execute();
            $update_stmt->close();

            $remaining_amount -= $collection["amount"];
        }else{
            break;
        }

    }
    $collection_stmt->close();

    header(
        "Location: payment.php?saved=1"
    );
    exit();

    }else{
        $error = "Unable to save payment";
    }
    $stmt->close();

}
}

$farmers =$conn->query(
    "SELECT 
    farmer_id,
    farmer_code,
    name
    FROM farmers
    WHERE status ='Active'
    ORDER BY name ASC"
);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment - Dairy Management System</title>

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
            Add Payment
        </h2>

        <p>
            Record a payment made to a farmer.
        </p>
        </div>

        <a href="payments.php" class="secondary-btn">  ← Back </a>
    </div>

    <?php if($error !== ""): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
    </div>

    <?php endif; ?>

    <div class="form-card">
        <h3>Payment Information</h3>

        <form action="" method="post">

        <div class="form-group">
            <label for="">Farmer *</label>
            <select name="farmer_id" required>

            <option value="">Select Farmer</option>

            <?php while(
                $farmer =
                $farmers->fetch_assoc()
            
            ): ?>

            <option value="<?php echo $farmer["farmer_id"]; ?>">

            <?php echo htmlspecialchars($farmer["farmer_code"]); ?> -

            <?php echo htmlspecialchars($farmer["name"]);?>
            </option>

            <?php endwhile; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label >Payment Date*</label>

                <input type="date" name="payment_date" value="<?php echo date("Y-m-d"); ?>" required>
            </div>

            <div class="form-group">
                <label for="">Amount (NPR) *</label>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="Enter payment amount" required>
            </div>
        </div>

        <div class="form-group">
            <label >Payment Method *</label>

            <select name="payment_method" required>

            <option value=""> Select Payment Method</option>

            <option value="Cash">Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Mobile Banking">Mobile Banking</option>
            </select>
        </div>

        <div class="form-group">
            <label for="">Remarks</label>
            <textarea name="remarks" Placeholder="Enter remarks"></textarea>
        </div>

        <div class="form-actions">
            <a href="payments.php" class="secondary-btn">Cancel</a>

            <button type="submit" class="primary-btn">Save Payment</button>
        </div>
        </form>
    </div>
    </main>
</div>
    </div>
</body>
</html>