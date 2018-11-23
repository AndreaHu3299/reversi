<?php 
    session_start();
    require_once("common/Giudice.php");
    require_once("common/Common.php" );
    require_once("common/Gara.php"   );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_GIUDICE){
        header("Location: login.php");
        die();
    }
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();

    $gare    = new Gare($pdo);
    $giudici = new Giudici($pdo);

    $username = $_SESSION['username'];

    $gareInCorso = json_decode($gare->getSpecificList(Gare::$GARA_INCORSO), true);
    if(!isset($_SESSION['idGara'])){
        if(count($gareInCorso) > 0) $idGara = $gareInCorso[0]['idGara'];
        else $noContestFound = true;
    }else $idGara = $_SESSION['idGara'];
    
    $idGiudice = json_decode($giudici->getBean($username), true)['idGiudice'];
    $idSessione = session_id();

    if(!isset($noContestFound)) {
		$giudici->login($idSessione, $idGiudice, $idGara);
		$_SESSION['idGiudice'] = $idGiudice;
		$_SESSION['idGara'   ] = $idGara   ;
	}else{
		echo "<script>alert('Nessuna gara in corso. Verrai portato alla pagina iniziale...');</script>";
		echo "<script>location.href='index.php';</script>";
    }

    //closes pdo connection
    $pdo = null;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Start Contest</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/contest.css"/>
        <link rel="stylesheet" href="styles/jquery-ui.css"/>
        <script src='javascript/jquery.min.js'></script>
        <script src='javascript/jquery-ui.min.js'></script>
        <script src='javascript/jquery.mousewheel.min.js'></script>
        <script src='javascript/spinbox.js'></script>
        <script> 
            var username = "<?=$username?>";
            var idGara   = <?=$idGara?>;
            var currentDog;
            var idCane;
            
            var spinnerView;

//-------------------------------------Richiedi dati riferimento------------------------------------

            function requestRazzaInfo(idRazza) {
                requestRazza = $.ajax({
                    url : "common/ajax_razza.php",
                    data: {"idRazza" : idRazza},
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

//----------------------------------------Richiedi dati cane----------------------------------------

            function requestDogInfo(idCane) {
                requestDog = $.ajax({
                    url : "common/ajax_dog.php",
                    data: {"idCane" : idCane},
                    type: "post"
                });

                requestDog.done(function (response, textStatus, jqXHR){
                    currentDog = JSON.parse(response);
                });

                requestDog.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                })

                requestDog.always(function(){});
            }

//-----------------------------------------Logout giudice-------------------------------------------

            function logoutGiudice() {
                ajaxLogoutGiudice = $.ajax({
                    url : "common/ajax_giudiceLogout.php",
                    data: {"idGara"   : idGara,
                           "username" : username},
                    type: "post"
                });
                ajaxLogoutGiudice.done(function (response, textStatus, jqXHR){
                });

                ajaxLogoutGiudice.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                ajaxLogoutGiudice.always(function(){});
            }

//--------------------------------------------Vota cane---------------------------------------------

            function votaCane(idCaneTemp, votoTemp, commentoTemp) {
                ajaxVota = $.ajax({
                    url : "common/ajax_giudiceVota.php",
                    data: {"idGara"   : idGara      ,
                           "username" : username    ,
                           "idCane"   : idCaneTemp  ,
                           "voto"     : votoTemp    ,
                           "commento" : commentoTemp},
                    type: "post"
                });
                ajaxVota.done(function (response, textStatus, jqXHR){
                    if(response){
                        alert("Votazione effettuata");
                    }else {
                        alert("Votazione fallita");
                    }
                });

                ajaxVota.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                ajaxVota.always(function(){});
            }
            
//------------------------------------SSE Ricevi notifiche cani-------------------------------------

            function subscribeSSE() {
                var contestES = new EventSource("common/sse_currentContest.php");
                contestES.addEventListener('garaTerminata', function(e) {
                    if(e.data){
                        $( '#waitView span' ).text("Gara terminata!")
                    }
                }, false);
                contestES.addEventListener('garaProgrammata', function(e) {
                    if(e.data){
                        $( '#waitView span' ).html("La gara non e' ancora iniziata!<br/>Attendi...")
                    }
                }, false);
                contestES.addEventListener('idCane', function(e) {
                    var data = JSON.parse(e.data);
                    if(data.idCane == null) {
                        hideDogView();
                    }else{
                        updateDogView(data.idCane);
                        idCane = data.idCane;
                    }
                }, false);
                contestES.addEventListener('error', function(e) {
                    if(e.readyState == EventSource.CLOSED) {
                        //connection closed
                        alert("La connessione al server e' fallita. La pagina verra' ricaricata.")
                        location.refresh();
                    }
                }, false);
            }

            function updateDogView(idCane) {
                requestDogInfo(idCane);
                if(currentDog != null){
                    updateDogInfo(currentDog);
                    requestRazzaInfo(currentDog['idRazza']);
                    showDogView();
                }
            }

            function showDogView() {
                $('#waitView').slideUp  (1000);
                $('#dogView' ).slideDown(1000);
            }

            function hideDogView() {
                $('#waitView').slideDown(1000);
                $('#dogView' ).slideUp  (1000);
            }

            function updateDogInfo(dog) {
                $('#pesoCane').text(dog["peso"      ] + " kg");
                $('#garrCane').text(dog["altGarrese"] + " cm");
                $('#coscCane').text(dog["altCoscia" ] + " cm");

                $('#dogName'    ).text(dog["nome"     ]);
                $('#dogSpecies' ).text(dog["nomeRazza"]);
                $('#ownerName'  ).text("di "      + dog["nomePropr"  ]);
                $('#dogBirthday').text("nato il " + dog["dataNascita"]);
            }

            $(function() {
                $('#dogView').hide();
                subscribeSSE();

                $('#confirm').click(function() {
                    var voto     = spinnerView.spinner("value");
                    var commento = $('#commento').val();
                    if(voto<=10 && voto>0){
                        votaCane(idCane, voto, commento);
                        hideDogView();
                    }else {
                        alert("Formato voto errato!");
                    }
                });
                spinnerView = $('#voto').spinner();
            });

        </script>
    </head>
    <body>
        <?php include "common/header.php"; ?>

        <script>
            window.onbeforeunload = function(){
                logoutGiudice();
                return 'Are you sure to leave this page?';
            }
        </script>

        <div class="mainContent" onbeforeunload="return logoutGiudice()">

            <div id="waitView" class='waitView'>
                <span>Attendi...</span>
            </div>

            <div id="dogView">
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
                <div style="clear: both"></div>
                <div>
                    <input id="voto" type="number" min="1" max="10" step="1"/>
                    <textarea id="commento" rows="5" cols="50" placeholder="Inserisci un commento..."></textarea>
                </div>
                <button id="confirm">Conferma voto</button>
            </div>
        </div>

        <?php include "common/footer.php"; ?>

    </body>
</html>