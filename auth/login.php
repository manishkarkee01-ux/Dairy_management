<?php
session_start();
require_once "../config/database.php";

$error="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $username=trimp($_POST["username"]??"");
    $password=$_POST["password"]?? "";

    if($username==""|| $password==""){
        $error="please enter username and password.";
    }else{
        $stmt=$conn->prepare(
            "SELECT admin_id, name, username, password, role
            FROM admin WHERE username=?
            LIMIT 1"
        );
        $stmt->bind_param("s",username);
        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows===1){
            $user=$result->fetch_assoc();

            if(password_verify($password,$user["password"])){
                $_SESSION["user_id"]=$user["admin_id"];
                $_SESSION["user_name"]=$user["name"];
                $_SESSION["role"]=$user["role"];
                $_SESSION["user_type"]="admin";

                header("location:../admin/dashboard.php");
                exist();
            }
            else{
                $error="invalid username or password.";
            }
        }
        else{
            $stmt=$conn->prepare(
                "SELECT farmer_id, farmer_code, name, password
                FROM farmers
                WHERE farmer_code=?
                AND status='Active'
                LIMIT 1"
            );

            $stmt-> bind_param("s",$username);
            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows===1){
                $farmer = $result->fetch_assoc();

                if(password_verify($password,$farmer["farmer_id"])){
                    $SESSION["user_id"]=$farmer["farmer_id"];
                    $SESSION["user_name"]=$farmer["name"];
                    $SESSION["farmer_code"]=$farmer["farmer_code"];
                    $SESSION["user_type"]= "farmer";

                    header("Location: ../farmer/dashboard.php");
                    exist();
                }
                else{
                    $error= "Invalid username or password.";
                }
            }else{
                $error="Invalid username or password";
            }
        }
    }
}

?>

<!Doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dairy Management System</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Dairy Management System</h1>
                    <p>Login to your account</p>
                </div>
                <?php if ($error !== ""):?>

                    <div class="error-message">
                        <?php echo htmlspecialchars($error);?>
                    </div>

                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">

                        <label for="username">
                            Username/ Farmer ID
                        </label>

                        <input type="text" id="username" name="username" placeholder="Enter your username">
                
                          </div>

                          <div class="form-group">
                            <label for="password">password</label>
                            <input type="text" id="password" name="password" placeholder="Enter your password" required>
    
                          </div>

                          <button type="submit" class="login-btn">
                            Login
                          </button>


                    </form>
            </div>
        </div>
    </body>
</html>
