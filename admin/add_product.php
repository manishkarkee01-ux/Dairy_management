<?php 
require_once "../includes/auth_admin.php";
require_once "../config/database.php";

$page_title = "Add Product";

$error = "";
$success ="";

if($_SERVER["REQUEST_METHOD"]=== "POST"){

$product_name = trim($_POST["product_name"] ?? "");
$unit = trim($_POST["unit"] ?? "");
$quantity = floatval($_POST["unit"] ?? 0);
$minimum_stock = flatval($_POST["minimum_stock"] ?? 0);

if(
    $product_name === "" ||
    $unit === "" 
){
    $error = "Please fill in all required fields.";
}elseif($quantity < 0 || $minimum_stock < ){
    $error = "Quantity and minimum stock cannot be negative ";
}else {
    $stmt = $conn ->prepare(
        "INSERT INTO products
        (
        product_name,
        unit,
        quantity,
        minimum_stock
        )
        VALUES(?, ?, ?, ?)"
    );
}
}