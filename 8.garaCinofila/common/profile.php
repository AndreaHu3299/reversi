<?php 

    session_start();

    //Check if a session is active, if not go back to login page
    if(!isset($_SESSION['clientID'])){
        header("Location: login.php");
        die();
    }

    require_once("common/Socio.php"     );
    require_once("common/Secretary.php" );
    require_once("common/Connection.php");

    $id = $_SESSION['clientID'];
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $clients     = new Soci($pdo);
    $secretaries = new Secretaries($pdo);

    if($_SESSION['accountType'] == "c"){    //if a client is logged in
        $idTag  = "clientID";
        $result = json_decode($clients    ->getClient   ($id), true);
    }else 
    if($_SESSION['accountType'] == "s"){    //if a secretary is logged in
        $idTag  = "secrID"  ;
        $result = json_decode($secretaries->getSecretary($id), true);
    }

    $pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/profile.css"/>
        <script>
            function goBack() {
                window.history.back();
            }
            function logout() {
                window.location.replace("common/logout.php");
            }
        </script>
    </head>
    <body>
        
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()" class="pulse">Back</button><br/> 
            <img id = "profileIcon" src = "rsrc/usericon.png"><br/>
            <?php
            echo "
            <div class='category'>"   .ucfirst($idTag)         ."</div><br/>
            <div class='description'>".ucfirst($result[$idTag])."</div><br/>

            <div class='category'>Name</div><br/>
            <div class='description'>".ucfirst($result['name'])."</div><br/>

            <div class='category'>Surname</div><br/>
            <div class='description'>".ucfirst($result['surname'])."</div><br/>";

            if($idTag == "clientID") {      //if a client is logged in, show all the fields
                echo "
                    <div class='category'>Address</div><br/>
                    <div class='description'>".ucfirst($result['street'])." ".$result['number']."</div><br/>

                    <div class='category'>City</div><br/>
                    <div class='description'>".$result['city'      ]."</div><br/>

                    <div class='category'>Subscription Date</div><br/>
                    <div class='description'>".$result['subscrDate']."</div><br/>

                    <div class='category'>Telephone</div><br/>
                    <div class='description'>".$result['tel'       ]."</div><br/>
                    ";
            }?>
        </div>

        <button id="logout" onclick = "logout()">Logout</button><br/>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>