<?php 
    session_start();
    require_once("common/Common.php");
    require_once("common/Gara.php"  );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }

    $id = $_SESSION['username'];
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $gare = new Gare($pdo);

    //if a court deletion request is received, attempt to delete court
    if(isset($_GET['action']))
    {
        switch($_GET['action']){
            case "delete":
                $result = $gare->delete($_GET['idGara']);

                if($result) $message = "Successfully deleted!";
                else $message = "Something went wrong...";
            break;
            case "open":
                $result = $gare->updateStatus($_GET['idGara'], Gare::$GARA_APERTA);

                if($result) $message = "Successfully opened!";
                else $message = "Something went wrong...";
                echo "<script type='text/javascript'>alert('$message')</script>";
                echo "<script type='text/javascript'>window.location.href='mOpenContest.php';</script>";
            break;
        }

        echo "<script type='text/javascript'>alert('$message')</script>";
    }

    //gets all the courts
    $resultsOld = json_decode($gare->getSpecificList(Gare::$GARA_TERMINATA  ), true);

    $resultsPro = json_decode($gare->getSpecificList(Gare::$GARA_PROGRAMMATA), true);

    //print_r($resultsOld);
    //print_r($resultsPro);

    //closes pdo connection
    $pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Court Overview</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/oldcontest.css"/>
        <script src='javascript/jquery.min.js'></script>
        <script>
            $( function(){
                $( ".row" ).tooltip();
                
                $( '.row' ).hover(function(){
                    $(this).addClass("highlight");
                }, function(){
                    $(this).removeClass("highlight");
                })
            });
            function goBack() {
                location.href='management.php';
            }
            function showRanking(id) {
                location.href = "mClassifica.php?idGara="+id;
            }
            function deleteContest(id) {
                if(confirm("Are you sure to delete this contest?")){
                    location.href = "mOldContest.php?action=delete&idGara="+id;
                }
            }
            function openContest(id) {
                if(confirm("Are you sure to open this contest? \nYou won't be able to edit the details anymore.")){
                    location.href = "mOldContest.php?action=open&idGara="+id;
                }
            }
            function editContest(id) {
                location.href = "mEditContest.php?idGara="+id;
            }
        </script>
    </head>
    <body> 
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>
            <h1>Gare Programmate</h1>
            <?php
            if(sizeof($resultsPro) > 0){
                foreach($resultsPro as $gara){
                    echo "
                    <div class ='pContest'>
                        <div class='contestText'>
                            <div class='contestName'>".$gara['nome']."</div><br/>
                            <div class='contestInfo'>".Common::convDate($gara['data'])." - ".$gara['luogo']."</div>
                        </div>
                        <div class='contestButtonHolder'>
                            <button onclick=\"openContest  ('".urlencode($gara['idGara'])."')\">Apri ad iscrizioni</button>
                            <button onclick=\"editContest  ('".urlencode($gara['idGara'])."')\">Edit              </button>
                            <button onclick=\"deleteContest('".urlencode($gara['idGara'])."')\">Delete            </button>
                        </div>
                        <div style='clear:both'></div>
                    </div>
                    ";
                }
            }else echo "<h3>Nessuna gara in programma</h3>";
            ?>
            <h1>Gare Precedenti</h1>
            <table class = "table">
                <tr>
                    <th>Nome</th>
                    <th>Luogo</th>
                    <th>Data</th>
                </tr>
                <?php
                    if(sizeof($resultsOld) > 0){
                        foreach($resultsOld as $gara) {
                            echo "
                            <tr class = 'row' onclick = 'showRanking(".$gara['idGara'].")' title='Premi una gara per visualizzare la sua classifica'>
                                <td>".ucfirst($gara['nome' ])."</td>

                                <td>".ucfirst($gara['luogo'])."</td>
                                
                                <td>".Common::convDate($gara['data'])."</td>
                            </tr>";
                        }
                    }else echo "<h3>Nessuna gara precedente</h3>";
                ?>
            </table>
        </div>
        
        <?php include "common/footer.php"; ?>

    </body>
</html>