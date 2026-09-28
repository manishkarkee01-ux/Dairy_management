<?php 

require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title ="Edit Farmer";

$error="";
$success="";

$farmer_id=intval($_GET["id"] ?? 0);

if ($farmer_id<=0){
    header("location: farmer.php");
    exist();
}

$stmt =$conn->prepare(
    "SELECT farmer_id, farmer_code, name, phone, address, email, dtatus
    FROM farmers
    WHERE farmer_id=?
    LIMIT 1"
);

$stmt-> bind_param("i",$farmer_id);
$stmt->execute();

$result= $stmt->get_result();

if($result->num_rows !==1){
    header("location: farmers.php");
    exit();
}

$farmer= $result->fetch_assoc();


if($_SERVER["REQUEST_METHOD"]==="POST"){
    $name= trim($_POST["name"]??"");
    $phone= trim($_POST["phone"]??"");
    $address= trim($_POST["address"]??"");
    $email= trim($_POST["email"]??"");
    $status= trim($_POST["status"]??"");
    $new_password= trim($_POST["password"]??"");

    if($name ===""||$phone===""){
        $error="Name and phone number are required.";
    }
    elseif(!in_array($status,["Active","Inactive"])){
        $error="Invalid status.";
    }else{
        if($new_password !==""){
            $hashed_password=password_hash
            (
                $new_password,
                PASSWORD_DEFAULT
            );

            $stmt=$conn->prepare(
                "UPDATE farmers
                SET name=?,
                phone=?,
                address=?,
                email=?,
                status=?
                password=?
                WHERE farmer_id=?
                "
            );

            $stmt->bind_param(
                "ssssssi",
                $name,
                $phone,
                $address,
                $email,
                $status,
                $hashed_password,
                $farmer_id
            );
        }else{
            $stmt=$conn->prepare(
                "UPDATE farmers
                SET name=?,
                phone=?,
                address=?,
                email=?,
                status=?,
                WHERE farmer_id=?"
            );
            $stmt->n=bind_param(
                "sssssi",
                $name,
                $phone,
                $address,
                $email,
                $status,
                $farmer_id
            );
        }

        if($stmt->execute()){
            header(
                "location:farmers.php?updated=1"
            );
            exit();
        }else{
            $error="Unable to update farmers.";
        }
    }
}

?>

<DOCTYPE html>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Farmer- Dairy Management System</title>

        <link rel="stylesheet" href="../assets/style.css">
    </head>
    <body>
        <div class="admin-layout">
            <?php include "../includes/sidebar.php";?>

            <div class="main-content">
                <?php include "../includes/header.php";?>

                <main class="dashboard-content">
                    <div class="page-header">

                    <div>
                        <h2>Edit Farmer</h2>
                        <p>Update farmer information.</p>
                    </div>

                    <a href="farmers.php" class="secondary-btn">
                          ← Back
                    </a>
                    </div>

                    <?php if($error !==""):?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error);?>
                        </div>
                        <?php endif; ?>
                        <div class="form-card">
                            <h3>Farmer Information</h3>

                            <form  method="POST">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label >Farmer Code</label>
                                        <input type="text" value="<?php echo htmlspecialchars($farmer["farmer_code"]);?>" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>
                </main>
            </div>
        </div>
        
    </body>
    </html>
