<?php
    session_start();
    require_once("common/Common.php");

    //Check if a session is active, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_PROPRIETARIO){
        header("Location: login.php");
        die();
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Management</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <script>
        </script>
    </head>
    <body>
        
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <div class = "item" onclick = "location.href='pMyDogs.php'">
                <img class = "itemImg" src = "rsrc/categoryImage.jpg"/>
                <a class = "itemText">I miei cani</a>
            </div>
            <div class = "item" onclick = "location.href='pEnterContest.php'">
                <img class = "itemImg" src = "rsrc/categoryImage.jpg"/>
                <a class = "itemText">Partecipa a gara</a>
            </div>
        </div>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>