<?php 

    session_start();

    //Check if a session is active, if not go back to login page
    if(!isset($_SESSION['username'])){
        header("Location: login.php");
        die();
    }

    require_once("common/Common.php");

    $accountType = $_SESSION['accountType'];
    $username    = $_SESSION['username'   ];

    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    switch($accountType) {
        case Common::$USER_MANAGER:
            require_once("common/Manager.php");
            $user = new Manager($pdo);
        break;
        case Common::$USER_PROPRIETARIO:
            require_once("common/Proprietario.php");
            $user = new Proprietari($pdo);
        break;
        case Common::$USER_GIUDICE:
            require_once("common/Giudice.php");
            $user = new Giudici($pdo);
        break;
    }

    $result = json_decode($user->getBean($username), true);

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
            <div class='category'>Username</div><br/>
            <div class='description'>".ucfirst($result['username'])."</div><br/>

            <div class='category'>Name</div><br/>
            <div class='description'>".ucfirst($result['nome'])."</div><br/>

            <div class='category'>Surname</div><br/>
            <div class='description'>".ucfirst($result['cognome'])."</div><br/>";

            if($accountType == Common::$USER_PROPRIETARIO) {      //if a proprietario is logged in, show these fields
                echo "
                    <div class='category'>Codice Fiscale</div><br/>
                    <div class='description'>".ucwords($result['codiceFiscale'])."</div><br/>

                    <div class='category'>Telephone</div><br/>
                    <div class='description'>".$result['telefono']."</div><br/>
                    ";
            }?>
        </div>

        <button id="logout" onclick = "logout()">Logout</button><br/>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>