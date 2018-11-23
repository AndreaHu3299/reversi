<?php 
    session_start();
    require_once("common/Votazione.php");
    require_once("common/Common.php" );
    require_once("common/Gara.php" );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
/*    if(!isset($_SESSION['username']) || $_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }*/
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $votazioni = new Votazioni($pdo);
    $gare      = new Gare     ($pdo);

    if(isset($_GET['idGara'])){
        $idGara = $_GET['idGara'];
		$nomeGara = json_decode($gare->getGara($idGara), true)['nome'];
        $classifica = json_decode($votazioni->getClassifica($idGara), true);
        if(sizeof($classifica) == 0){
            echo "<script type='text/javascript'>alert('Questa gara non e' ancora terminata!\nVerrai portato alla pagina iniziale')</script>";
            header("Location: index.php");
            die();
        }
    }else {
        header("Location: index.php");
        die();
    }

    //closes pdo connection
    $pdo = null;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Contest ranking</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/oldcontest.css"/>
        <link rel="stylesheet" href="styles/jquery-ui.css"/>
        <script src='javascript/jquery.min.js'></script>
        <script src='javascript/jquery-ui.min.js'></script>
        <script> //javascript
            function goBack() {
                window.history.back();
            }
        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button>
            <h1>Classifica <?=$nomeGara?></h1><br/>

            <table class="table">
                <tr>
                    <th>Rank</th>
                    <th>Cane</th>
                    <th>Proprietario</th>
                    <th>Media</th>
                </tr>
                <?php
                foreach($classifica as $indice=>$candidato){
                    echo "
                    <tr>
                        <td>".($indice + 1)."</td>
                        <td>".$candidato['nomeCane']."</td>
                        <td>".$candidato['nomeProp']." ".$candidato['cognomeProp']."</td>
                        <td>".$candidato['media']."</td>
                    </tr>
                    ";
                }
                ?>
            </table>

        </div>

        <?php include "common/footer.php"; ?>

    </body>
</html>