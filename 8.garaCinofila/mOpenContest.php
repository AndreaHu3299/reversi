<?php 
    session_start();
    require_once("common/Common.php");
    require_once("common/Gara.php"  );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $gare = new Gare($pdo);

    //gets all the courts
    $results = json_decode($gare->getSpecificList(Gare::$GARA_APERTA), true);
    $nPart = array();
    if(sizeof($results) > 0){
        foreach($results as $gara){
            $idTemp = $gara['idGara'];
            $nPart[$idTemp] = $gare->getParticipants($idTemp);
        }
    }

    //closes pdo connection
    $pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Open Contest</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/oldcontest.css"/>
        <script>
            function goBack() {
                location.href='management.php';
            }
            function startContest(id, nPart) {
                if(nPart > 1) {
                    if(confirm("Are you sure to start this contest? \nYou won't be able to add new contestants anymore.")){
                        location.href = "mStartContest.php?idGara="+id;
                    }
                }else {
                    alert("Non puoi iniziare una gara con meno di 2 partecipanti!");
                }
                
            }
        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>
            <?php
            if(sizeof($results) > 0){
                foreach($results as $gara){
                    $idTemp = $gara['idGara'];
                    echo "
                    <div class ='pContest'>
                        <div class='contestText'>
                            <div class='contestName'>".$gara['nome']."</div><br/>
                            <div class='contestInfo'>".Common::convDate($gara['data'])." - ".$gara['luogo']." - ".$nPart[$idTemp]." Partecipanti</div>
                        </div>
                        <div class='contestButtonHolder'>
                            <button onclick=\"startContest('".urlencode($idTemp)."', ".$nPart[$idTemp].")\">Inizia gara</button>
                        </div>
                        <div style='clear:both'></div>
                    </div>
                    ";
                }
            }else echo "<h3>Nessuna gara aperte</h3>";
            ?>
        </div>

        <?php include "common/footer.php"; ?>

    </body>
</html>