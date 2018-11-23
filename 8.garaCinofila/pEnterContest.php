<?php 
session_start();
require_once("common/Candidato.php");
require_once("common/Common.php"   );
require_once("common/Gara.php"     );
require_once("common/Cane.php"     );

//Check if a session is active and if the logged in account is a secretary, if not go back to login page
if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_PROPRIETARIO){
    header("Location: login.php");
    die();
}

$username = $_SESSION['username'];

//establish a connection to DBserver
$pdo = Common::createPDO();

$candidati = new Candidati($pdo);
$gare      = new Gare     ($pdo);
$cani      = new Cani     ($pdo);

if(!empty($_POST)){
    $idCane = $_POST['idCane'];
    $idGara = $_POST['idGara'];

    if($candidati->applyContest($idCane, $idGara)) {
        echo "<script type='text/javascript'>alert('Successfully registered!');</script>";
        echo "<script type='text/javascript'>window.location.href='pEnterContest.php';</script>";
    }else {
        echo "<script type='text/javascript'>alert('Error encountered!');</script>";
    }
}

//richiede tutte le gare aperte ad iscrizione
$resultsContest = json_decode($gare->getSpecificList(Gare::$GARA_APERTA), true);

//richiede il numero di partecipanti di ogni gara
$nPart = array();
if(!empty($resultsContest)){
    foreach($resultsContest as $gara){
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
        <link rel="stylesheet" href="styles/modal.css">
        <script type='text/javascript' src='javascript/jquery.min.js'></script>
        <script type='text/javascript' src='javascript/modal.js'></script>
        <script type='text/javascript'>

            function requestAvailableDogs(username, idGara) {
                ajaxGetDog = $.ajax({
                    url : "common/ajax_getAvailableDogs.php",
                    data: {"username" : username,
                           "idGara"   : idGara},
                    type: "post"
                });
                ajaxGetDog.done(function (response, textStatus, jqXHR){
                    $( '#dogContainer' ).empty();
                    $( '#dogContainer' ).html(response);
                });

                ajaxGetDog.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });
                
                ajaxGetDog.always(function(){});

            }
            function goBack() {
                location.href='proprietarioPortal.php';
            }
            function check() {
                if(document.getElementsByClassName('card').length > 0 && document.getElementsByClassName('selected').length == 1) 
                return true;
                else return false;
            }
        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>
            <?php
            if(sizeof($resultsContest) > 0){
                foreach($resultsContest as $gara){
                    $idTemp = $gara['idGara'];
                    echo "
                    <div class ='pContest'>
                        <div class='contestText'>
                            <div class='contestName'>".$gara['nome']."</div><br/>
                            <div class='contestInfo'>".Common::convDate($gara['data'])." - ".$gara['luogo']." - ".$nPart[$idTemp]." Partecipanti</div>
                        </div>
                        <div class='contestButtonHolder'>
                            <button id='' onclick=\"chooseContest('".$username."', ".urlencode($idTemp).")\">Partecipa gara</button>
                        </div>
                        <div style='clear:both'></div>
                    </div>
                    ";
                }
            }else echo "<h3>Nessuna gara aperta</h3>";

//------------------------------------------------MODAL-------------------------------------------------
            echo "
            <div id='id01' class='modal animate-opacity'>
                <div class='modal-content card-4'>
                        <header class='header'> 
                            <span onclick='closeModal()' class='button large display-topright'>&times;</span>
                            <h2>Choose a dog</h2>
                        </header>
                        <div id='dogContainer' class='container'>
                        </div>
                        <form action='pEnterContest.php' method='post' onsubmit='return check()'>
                            <input type='hidden' id='idGara' name='idGara'/>
                            <input type='hidden' id='idCane' name='idCane'/>
                            <input type='submit' class='submit' value='Confirm'/>
                        </form>
                    </div>
                </div>
            </div>
            ";

        ?>
        </div>
        <?php include "common/footer.php"; ?>

    </body>
</html>