<?php 
    session_start();
    require_once("common/Giudice.php");
    require_once("common/Common.php" );
    require_once("common/Gara.php"   );
    require_once("common/Cane.php"   );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
        header("Location: login.php");
        die();
    }
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();

    $giudici = new Giudici($pdo);
    $gare    = new Gare   ($pdo);
    $cani    = new Cani   ($pdo);

    if(!isset($_SESSION['idGara'])){
        $gareInCorso = json_decode($gare->getSpecificList(Gare::$GARA_INCORSO), true);
        if(count($gareInCorso) == 0){
            header("Location: management.php");
            die();
        }
        $idGara = $gareInCorso[0]['idGara'];
        $_SESSION['idGara'] = $idGara;
    }else $idGara = $_SESSION['idGara'];

    if(isset($_GET['currentDog'])){
        $currentDog = $_GET['currentDog'];
    }else {
        $lastDog = $gare->getLastDog($idGara);
        $contestDogs = json_decode($cani->getContestDogs($idGara), true);
        $indexLastDog = array_search($lastDog, array_column($contestDogs, 'numeroChip'));
        if(!$indexLastDog){
            $indexLastDog = count($contestDogs) - 1;
        }
        header("Location: mManageContest.php?idGara=4&currentDog=$indexLastDog");
        die();
    }
    
    $contestDogsJson = $cani   ->getContestDogs      ($idGara);
    $judgesJson      = $giudici->getContestJudgesList($idGara);
    $judges = json_decode($judgesJson, true);


    //closes pdo connection
    $pdo = null;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Manage Contest</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/contest.css"/>
        <script src='javascript/jquery.min.js'></script>
        <script> //javascript
            let STATO_READY   = <?=Giudici::$STATO_READY  ?>;
            let STATO_OFFLINE = <?=Giudici::$STATO_OFFLINE?>;

            let ELECTR_NO  = <?=Giudici::$ELECTR_NO ?>;
            let ELECTR_YES = <?=Giudici::$ELECTR_YES?>;

            var idGara        = <?=$idGara?>;
            var contestDogs   = JSON.parse('<?=$contestDogsJson?>');
            var judges        = JSON.parse('<?=$judgesJson     ?>');
            var electroJudges;
            var numDogs       = contestDogs.length;
            var currentDog    = <?php if(isset($currentDog)) echo $currentDog; else echo '0';?>;

//--------------------------------------------------------------------------------------------------

            function requestRazzaInfo() {
                requestRazza = $.ajax({
                    url : "common/ajax_razza.php",
                    data: {"idRazza" : contestDogs[currentDog]["idRazza"]},
                    type: "post"
                });

                requestRazza.done(function (response, textStatus, jqXHR){
                    var refData = JSON.parse(response);
                    $('#pesoRef').text(refData['refPeso'      ] + " kg");
                    $('#garrRef').text(refData['refAltGarrese'] + " cm");
                    $('#coscRef').text(refData['refAltCoscia' ] + " cm");
                });

                requestRazza.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                })

                requestRazza.always(function(){});
            }

//--------------------------------------------------------------------------------------------------
            function requestGiudiciInfo() {
                requestGiudici = $.ajax({
                    url : "common/ajax_giudice.php",
                    data: {"idGara" : idGara},
                    type: "post"
                });

                requestGiudici.done(function (response, textStatus, jqXHR){
                    var giudiciResp = JSON.parse(response);
                    $.each(giudiciResp, function(index, value) {
                        var idSessione  = value.idSessione;
                        var idGiudice   = value.idGiudice ;
                        var electro     = value.electro   ;
                        var stato       = value.stato     ;

                        if(electro == ELECTR_YES){
                            switch(stato) {
                                case STATO_OFFLINE:
                                    $( '#'+idGiudice+'status' ).removeClass("online").addClass("offline");
                                    $( '#'+idGiudice+'status' ).text("offline");
                                break;
                                case STATO_READY:
                                    $( '#'+idGiudice+'status' ).removeClass("offline").addClass("online");
                                    $( '#'+idGiudice+'status' ).text("online");
                                break;
                            }
                        }
                    });
                });

                requestGiudici.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                requestGiudici.always(function(){});
            }

//--------------------------------------------------------------------------------------------------
           
            function requestVotes(){
                getVotes = $.ajax({
                    url : "common/ajax_getGiudiceVoto.php",
                    data: {"giudiciElec" : JSON.stringify(judges),
                           "idGara"      : idGara,
                           "idCane"      : contestDogs[currentDog]["numeroChip"]},
                    type: "post"
                });
                getVotes.done(function (response, textStatus, jqXHR){
                    var votes = JSON.parse(response);
                    updateVotes(votes);
                });
                getVotes.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                getVotes.always(function(){});
            }

//--------------------------------------------------------------------------------------------------
            
            function broadcastDogInfo(){
                startVotes = $.ajax({
                    url : "common/ajax_broadcastDog.php",
                    data: {"giudici" : JSON.stringify(judges),
                           "idGara"  : idGara,
                           "idCane"  : contestDogs[currentDog]["numeroChip"]},
                    type: "post"
                });
                startVotes.done(function (response, textStatus, jqXHR){
                    //alert(response);
                });
                startVotes.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                startVotes.always(function(){});
            }

//--------------------------------------------------------------------------------------------------

            function votaCane(idCaneTemp, votoTemp, idGiudiceTemp) {
                ajaxVota = $.ajax({
                    url : "common/ajax_managerVota.php",
                    data: {"idGara"    : idGara       ,
                           "idGiudice" : idGiudiceTemp,
                           "idCane"    : idCaneTemp   ,
                           "voto"      : votoTemp},
                    type: "post"
                });
                ajaxVota.done(function (response, textStatus, jqXHR){
                    if(response){
                        alert("Votazione effettuata");
                        $( '#'+response+"vote" ).prop('disabled', true);
                    }else {
                        alert("Votazione fallita");
                    }
                });

                ajaxVota.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });
                
                ajaxVota.always(function(){});

            }

//--------------------------------------------------------------------------------------------------

            function updateDogInfo(){
                $('#pesoCane').text(contestDogs[currentDog]["peso"      ] + " kg");
                $('#garrCane').text(contestDogs[currentDog]["altGarrese"] + " cm");
                $('#coscCane').text(contestDogs[currentDog]["altCoscia" ] + " cm");

                $('#dogName'    ).text(contestDogs[currentDog]["nome"     ]);
                $('#dogSpecies' ).text(contestDogs[currentDog]["nomeRazza"]);
                $('#ownerName'  ).text("di "      + contestDogs[currentDog]["nomePropr"  ]);
                $('#dogBirthday').text("nato il " + contestDogs[currentDog]["dataNascita"]);
            }

            function updateVotes(votes){
                $.each(votes, function (index, value){
                    if(judges.filter(function(judge){return judge['electro'];}) == ELECTR_YES){
                        $( '#' + value['idGiudice'] + 'vote' ).text(value['voto']);
                        $( '#' + value['idGiudice'] + 'vote' ).val (value['voto']);
                    }else if(value['voto']){
                        $( '#' + value['idGiudice'] + 'vote input' ).remove();
                        $( '#' + value['idGiudice'] + 'vote' ).text(value['voto']);
                        $( '#' + value['idGiudice'] + 'vote' ).val (value['voto']);
                    }
                });
            }

            function getElectroJudges() {
                electroJudges = new Array();
                $.each(judges, function(index, value){
                    if(value['electro'] == ELECTR_YES){
                        electroJudges.push(value);
                    }
                });
                return electroJudges;
            }

            function allVoted() {
                if($( "td[id$='vote']" ).filter(function(value){return (this.value)}).length == judges.length){
                    return true;
                }else return false;
            }

            function allOnline() {
                if($('.online').length == electroJudges.length) {
                    return true;
                }
                return false;
            }

            function makeRequests(){
                requestGiudiciInfo();
                requestVotes();
            }


            $( function() {
                requestRazzaInfo();
                makeRequests();
                setInterval(makeRequests, 5000);
                updateDogInfo();
                getElectroJudges();
                $( 'input' ).prop('disabled', true);

                if(currentDog == numDogs-1) {
                    $('#nextDog').text("Termina gara");
                }

                $('#nextDog' ).click(function() {
                    if(allVoted()) {
                        if(currentDog < numDogs-1) {
                            if(confirm("Sei sicuro di voler proseguire al prossimo cane?")){
                                currentDog++;
                                location.replace("mManageContest.php?idGara=" + idGara + "&currentDog=" + currentDog);
                            }
                        }else {
                            location.replace('common/endContest.php');
                        }
                    }else {
                            alert("Non tutti i giudici hanno votato!");
                    }
                    
                });

                $( "td[name=giudice] input" ).bind("confirmVote", function(){
                    var voto = $(this).val();
                    if(voto<1 || voto>10){
                        alert("Formato voto errato! Inserisci un voto tra 1 e 10.");
                    }else{
                        if(confirm("Confermi il voto " + voto + "?")){
                            var idCane = contestDogs[currentDog]["numeroChip"];
                            var voto = $(this).val();
                            var idGiudice = $(this).closest('td').attr('id').replace("vote", "");
                            votaCane(idCane, voto, idGiudice);
                            requestVotes();
                        }
                    } 
                });

                $( "td[name=giudice] input" ).keyup(function (e) {
                    if (e.keyCode == 13) {
                        $(this).trigger("confirmVote");
                    }
                });

                $( '#startVoting' ).click(function() {
                    if(allOnline()) {
                        broadcastDogInfo();
                        alert("Votazione iniziata!");
                        $(this).hide();
                        $( 'input' ).prop('disabled', false);
                    }else{
                        alert("Non tutti i giudici sono online!");
                    }
                });
            });

//--------------------------------------------------------------------------------------------------

            function skipDog() {

            }

            function goBack() {
                location.href='management.php';
            }
        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button>
            <button id="nextDog">Next dog</button>
            <button id="startVoting">Start voting</button><br/>
            <button id="skipDog" style="float: right">Skip dog</button><br/>
            <div class="dogImage"></div>
            <div class="analitics">
                <span class="dogName"     id="dogName"    ></span>
                <span class="ownerName"   id="ownerName"  ></span><br/>
                <span class="dogSpecies"  id="dogSpecies" ></span>
                <span class="dogBirthday" id="dogBirthday"></span>
                <table class="dogData">
                    <tr>
                        <th></th>
                        <th>Dati del cane</th>
                        <th>Dati di riferimento</th>
                    </tr>
                    <tr>
                        <td>Peso</td>
                        <td id="pesoCane"></td>
                        <td id="pesoRef"></td>
                    </tr>
                    <tr>
                        <td>Alt. Garrese</td>
                        <td id="garrCane"></td>
                        <td id="garrRef"></td>
                    </tr>
                    <tr>
                        <td>Alt. Coscia</td>
                        <td id="coscCane"></td>
                        <td id="coscRef"></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="table">
            <tr>
                <th>Giudice</th>
                <th>Stato</th>
                <th>Voto</th>
            </tr>
            <?php

            if(!empty($judges)){
                foreach($judges as $giudice){
                    echo "
                <tr>
                    <td>".$giudice['nome']." ".$giudice['cognome']."</td>";

                if($giudice['electro'] == Giudici::$ELECTR_YES) {
                    echo "
                    <td id='".$giudice['idGiudice']."status'></td>
                    <td id='".$giudice['idGiudice']."vote' class='votoGiudice'>";
                }else echo "
                    <td>local</td>
                    <td id='".$giudice['idGiudice']."vote' name='giudice' class='votoGiudice'>
                    <input type='number' min='1' max='10' step='1'/>";
                echo "
                    </td>
                </tr>
                    ";
                }
            }
            ?>
        </table>

        <?php include "common/footer.php"; ?>

    </body>
</html>