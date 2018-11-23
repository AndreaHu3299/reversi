<?php 
    session_start();
    require_once("common/Giudice.php");
    require_once("common/Common.php" );
    require_once("common/Gara.php"   );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $giudici = new Giudici($pdo);
    $gare    = new Gare   ($pdo);

    
    if(!empty($_POST)){
        $idGara          = $_POST['idGara'];
        $selectedJudges  = (isset($_POST['selectedJudges'])) ? $_POST['selectedJudges'] : null;
        $eletronicJudges = $_POST['judge'];
        $errors = false;

        $errors = ($gare->updateStatus($idGara, Gare::$GARA_INCORSO)) ? $errors : true;
        $errors = ($giudici->updateJudges($idGara, $selectedJudges )) ? $errors : true;
        if($eletronicJudges) $errors = ($giudici->updateElectronicJudges($idGara, json_encode($eletronicJudges))) ? $errors : true;

        if($errors){
            echo "<script type='text/javascript'>alert('Error encountered!')</script>";
        }else{
            $_SESSION['idGara'] = $idGara;
            header("Location: mManageContest.php");
            die();
        }
    } 
    
    $idGara = $_GET['idGara'];

    //gets all the judges
    $resultsGiudici = json_decode($giudici->getList(), true);

    //closes pdo connection
    $pdo = null;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Start Contest</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/sortableList.css"/>
        <link rel="stylesheet" href="styles/jquery-ui.css"/>
        <script src='javascript/jquery.min.js'></script>
        <script src='javascript/jquery-ui.min.js'></script>
        <script> //jquery
        var selectedJudges = new Array();
            $( function() {
                //Jquery - imposta il tooltip dei giudici
                $( "label.draggableItem" ).attr('title', 'Trascina a destra i giudici partecipanti. Segna la casella se il giudice utilizza un dispositivo elettronico.');
                $( "label.draggableItem" ).tooltip();

                //Jquery - imposta tutti i checkbox come jquery checkbox
                $( "input[type=checkbox]").checkboxradio();

                //relaziona assieme le due liste di giudici
                $( "#sortable1, #sortable2" ).sortable({
                    connectWith: ".connectedSortable"
                }).disableSelection();

                //su conferma giudice
                $( "#submit" ).click(function() {

                    if($('#sortable2').children().length > 1) { //se almeno un giudice e selezionato

                        //inserisce tutti gli idGiudice della seconda lista in un array
                        $('#sortable2 label').each(function(){ 
                            selectedJudges.push($(this).attr('id'));
                        });
                        //salva la lista in formato json nell'input hidden
                        $('#judges').val(JSON.stringify(selectedJudges)); 

                    }else{                                      //se nessun giudice selezionato
                        alert("Seleziona almeno un giudice");   //notifica errore
                    }

                });
            });
        </script>
        <script> //javascript
            function goBack() {
                location.href='management.php';
            }
            function check() {
                if(document.getElementById('sortable2').children.length > 1) return true;
                else {
                    alert("Nessun giudice selezionato");
                    return false;
                } 
            }
        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>

            <fieldset id="sortable1" class="connectedSortable">
                <legend align="center" class="legend">Available judges</legend>
            <?php
            $i = 1;
            if(sizeof($resultsGiudici) > 0) {
                foreach($resultsGiudici as $giudice){
                    $idTemp = $giudice['idGiudice'];
                    echo "
                    <label id='".$idTemp."' class='draggableItem' for='checkbox".$i."'>".$giudice['nome']." ".$giudice['cognome']."
                        <input type='checkbox' id='checkbox".$i."' name='judge[]' value='".$idTemp."' class='elecCheck'/>
                    </label>
                    ";
                    $i++;
                }
            }
            ?>
            </fieldset>
            <form action="mStartContest.php" method="post" onsubmit="return check()">
                <fieldset id="sortable2" class="connectedSortable">
                    <legend align="center" class="legend">Chosen judges</legend>
                </fieldset>
                <input type="hidden" name="idGara" value="<?=$idGara?>"/>
                <input type="hidden" id="judges" name="selectedJudges"/>
                <input type="submit" id="submit" value="Confirm" class='confirm'/>
            </form>
        </div>

        <?php include "common/footer.php"; ?>

    </body>
</html>