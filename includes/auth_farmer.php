<?php 
if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

if(isset($_SESSION["farmer_id"])){
    header("Location: ../auth/farmer_login.php");
    exit();
}
?>