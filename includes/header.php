<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
?>

<header class="top-header">
    <div class="header-title">
        <h1><?php echo $page_title ?? "Dashboard";?></h1>
    </div>

    <div class="header-user">
        <div class="user-info">

        <strong>
            <?php echo htmlspecialchars($_SESSION["user_name"]?? "Admin");?> 
        </strong>

        <span>
            <?php echo htmlspecialchars($_SESSION["role"]?? "Admin"); ?>
        </span>

        </div>

        <div class="user-avtar">
            <?php
            echo strtoupper(
                substr($_SESSION["user_name"]?? "A", 0, 1)
            );
            ?>
        </div>
    </div>
</header>
