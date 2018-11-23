<?php 
    session_start();
    require_once("common/Common.php");
    require_once("common/Gara.php"  );
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $gare = new Gare($pdo);

    $resultsOld = json_decode($gare->getSpecificList(Gare::$GARA_TERMINATA), true);

    //closes pdo connection
    $pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Old contests</title>
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
                window.history.back();
            }
            function showRanking(id) {
                location.href = "mClassifica.php?idGara="+id;
            }
        </script>
    </head>
    <body> 
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>
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