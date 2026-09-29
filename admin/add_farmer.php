<?php 
require_once "../includes/auth_admin.php";
require_once"../config/database.php";


$page_title ="Add Farmer";

$error ="";

if($_SERVER["REQUEST_METHOD"]=== "POST"){
    $name =trim($_POST["name"]?? "");
    $phone =trim($_POST["phone"]?? "");
    $address =trim($_POST["address"]?? "");
    $email =trim($_POST["email"]?? "");
    $password =trim($_POST["password"]?? "");


if(
    $name === "" ||
    $phone === ""||
    $password === ""
){
    $error ="Please fill in all required fields.";
}else{
    $result=$conn->query(
        "SELECT farmer_id
        FROM farmers
        ORDER BY farmer_id DESC
        LIMIT 1"
    );

    if ($result-> num_rows >0){
        $last =$result->fetch_assoc();
        $next_id =$last["farmer_id"] + 1;

    }else{
        $next_id=1;
    }

    $farmer_code ="FRM" .str_pad(
        $next_id,
        3,
        "0",
        STR_PAD_LEFT
    );

    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $conn->prepare(
        "INSERT INTO farmers
        (
            farmer_code,
            name,
            phone,
            address,
            email,
            password
        )
        VALUES(?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssss",
        $farmer_code,
        $name,
        $phone,
        $address,
        $email,
        $hashed_password
    );

    if($stmt->execute()){
        header(
            "Location:farmers.php?added=1"
        );
        exit();
    }else{
        $error="Unable to add farmer.";
    }
}

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Farmer-Dairy Management System</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <?php include "../includes/sidebar.php";?>

    <div class="main-content">
        <?php include "../includes/header.php";?>

        <main class="dashboard-content">
        <div class="page-header">

        <div>
            <h2>
                Add Farmer
            </h2>

            <p>Register a new Farmer.</p>
        </div>

        <a href="farmers.php" class="secondary-btn">← Back</a>
        </div>

        <?php if($error !== ""): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error);?>
            </div>
            <?php endif; ?>

            <div class="form-card">

            <h3>Farmer Information</h3>

            <form  method="post">

            <div class="form-row">
                <div class="form-group">
                    <label >
                        Full Name*
                    </label>

                    <input type="text" name="name" placeholder="enter your name." required>
                </div>

                <div class="form-group">
                    <label >Phone Number*</label>

                    <input type="text" name="phone" placeholder="enter your number." required>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group">
                    <label >email</label>

                    <input type="email" name="email" placeholder="enter your email address">

                </div>

                <div class="form-group">
                    <label >password *</label>

                    <input type="password" name="password" placeholder="set your password." required>
                </div>
            </div>

            <div class="form-group">
                <label >address</label>

                <textarea name="address" placeholder="enter farmer address"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">save farmer</button>

                <a href="farmers.php" class="secondary-btn">cancel</a>
            </div>
            </form>
            </div>
        </main>

        <?php include "../includes/footer.php";?>
    </div>
</div>
    
</body>
</html>