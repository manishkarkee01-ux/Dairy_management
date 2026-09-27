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
}

if(
    $name === "" ||
    $phone === ""||
    $password === ""||
){
    $error ="Please fill in all required fields.";
}else{
    $result=$conn->query(
        "SELECT farmer_id
        FROM farmers
        ORDER BT farmer_id DESC
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
    
}