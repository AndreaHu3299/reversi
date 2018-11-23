<?php
    session_start();
    require_once("common/Common.php");
    require_once("common/Gara.php");

    //Check if a session is active, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }

    //establish a connection to DBserver
    $pdo = Common::createPDO();

    $gare = new Gare($pdo);
    $gareInCorso = json_decode($gare->getSpecificList(Gare::$GARA_INCORSO), true);
    $startedContest = (count($gareInCorso) > 0);

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
            <div class = "item" onclick = "location.href='mNewContest.php'">
                <img class = "itemImg" src = "rsrc/categoryImage.jpg"/>
                <a class = "itemText">Organizza nuova gara</a>
            </div>
            <div class = "item" onclick = "location.href='mOldContest.php'">
                <img class = "itemImg" src = "rsrc/categoryImage.jpg"/>
                <a class = "itemText">Tutte le gare</a>
            </div>
            <div class = "item" onclick = "location.href='mOpenContest.php'">
                <img class = "itemImg" src = "rsrc/categoryImage.jpg"/>
                <a class = "itemText">Stato gare aperte</a>
            </div>
            <?php
            if($startedContest)
            echo "
            <div class = 'item' onclick = \"location.href='mManageContest.php?idGara=".$gareInCorso[0]['idGara']."'\">
                <img class = 'itemImg' src = 'rsrc/categoryImage.jpg'/>
                <a class = 'itemText'>Gara in corso</a>
            </div>   
            ";
            ?>
        </div>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>