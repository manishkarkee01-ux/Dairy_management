<?php 
if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

if (
    !isset($_SESSION["farmer_id"]) ||
    $_SESSION["user_type"] !== "farmer"
) {
    header("Location: ../auth/login.php");
    exit();
}
?>