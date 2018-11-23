<?php 
    session_start();
    require_once("common/Common.php");
    require_once("common/Cane.php"  );
    
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_PROPRIETARIO){
        header("Location: login.php");
        die();
    }

    $username = $_SESSION['username'];
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $cani = new Cani($pdo);

    /*//if a court deletion request is received, attempt to delete court
    if(isset($_GET['action']))
    {
        switch($_GET['action']){
            case "delete":
                $result = $gare->delete($_GET['idGara']);

                if($result) $message = "Successfully deleted!";
                else $message = "Something went wrong...";
            break;
            case "edit":
                $altCoscia  = $_POST['altCoscia' ];
                $altGarrese = $_POST['altGarrese'];

                $result = $cani->update("altezze", $idCane, [$altCoscia, $altGarrese]);
            break;
            case "transfer":
                $codProprietario = $_POST['codProprietario'];
                $result = $cani->update("proprietario", $idCane, [$codProprietario]);
            break;
        }

        echo "<script type='text/javascript'>alert('$message')</script>";
    }*/

    //gets all the dogs
    $results = json_decode($cani->getDogs($username), true);

    //closes pdo connection
    $pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Court Overview</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/cards.css">
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/jquery-ui.css">
        <link rel="stylesheet" href="styles/contextMenu.css">
        <script type='text/javascript' src='javascript/jquery.min.js'></script>
        <script type='text/javascript' src='javascript/jquery-ui.min.js'></script>
        <script type='text/javascript' src='javascript/jquery.contextmenu.js'></script>
        <script>
            var dialog;
            var form;

//-----------------------------------Edit/transfer dog---------------------------------------------

            function actionDog(action, idCane) {
                var parameters = $( '#paramForm' ).serializeArray().reduce(function(a, x){
                    a[x.name] = x.value;
                    return a;
                }, {});
                ajaxDogAction = $.ajax({
                    url : "common/ajax_dogAction.php",
                    data: { "action"     : action,
                            "idCane"     : idCane,
                            "parameters" : JSON.stringify(parameters)},
                    type: "post"
                });
                ajaxDogAction.done(function (response, textStatus, jqXHR){
                    if(response){
                        alert("Operazione effettuata");
                        location.reload();
                    }else {
                        alert("Operazione fallita");
                    }
                });

                ajaxDogAction.fail(function (jqXHR, textStatus, errorThrown){
                    console.error("This error occorred: " + textStatus, errorThrown);
                });

                ajaxDogAction.always(function(){});
            }
//--------------------------------------------------------------------------------------------------

            function goBack() {
                location.href='proprietarioPortal.php';
            }
            function newDog(id) {
                location.href = "pNewDog.php";
            }

            function showEditDog(idCane){
                dialog.dialog( "open" );
                $( '#modal-form p' ).text( "Inserisci i nuovi dati." );
                form.html(
                    "<label for='altG'>Altezza Garrese</label> "
                    +"<input id='altG' type='number' name='altGarrese' min='0' placeholder='in cm' required/><br/>"
                    +"<label for='altC'>Altezza Coscia</label>"
                    +"<input id='altC' type='number' name='altCoscia'  min='0' placeholder='in cm' required/>"
                    +"<input type='submit' tabindex='-1' style='position:absolute; top:-1000px'>"
                );

                form.on( "submit", function( event ) {
                    event.preventDefault();
                    actionDog("edit", idCane);
                });
            }

            function showTransferDog(idCane){
                dialog.dialog( "open" );
                $( '#modal-form p' ).text( "Inserisci il username del preprietario a cui trasferire" );
                form.html(
                    "<label for='username'>Username</label>"
                    +"<input type='text' name='username' id='username' placeholder='es: esempio@esempio.it'>"
                    +"<input type='submit' tabindex='-1' style='position:absolute; top:-1000px'>"
                );

                form.on( "submit", function( event ) {
                    event.preventDefault();
                    actionDog("transfer", idCane);
                });
            }

            $(function() {
                form = $( '#paramForm' );

                dialog = $( "#modal-form" ).dialog({
                    autoOpen: false,
                    height: 300,
                    width: 400,
                    modal: true,
                    buttons: {
                        Conferma: function(){
                            form.submit();
                        },
                        Cancel: function() {
                            dialog.dialog( "close" );
                        }
                    },
                    close: function() {
                        form[ 0 ].reset();
                        //allFields.removeClass( "ui-state-error" );
                    }
                });

                $( '.card' ).each(function(index){
                    $( this ).attr("oncontextmenu", "return false;");
                });

				// Show menu when #myDiv is clicked
				$( '.card' ).each(function(index){
                    $( this ).contextMenu({
                        menu: 'myMenu'
                    },
					function(action, el, pos) {
                        var idTemp = $(el).attr('id');
                        switch(action) {
                            case 'delete':
                                if(confirm("Sei sicuro di voler eliminare il cane?")){
                                    location.href = "pMyDogs.php?action="+action+"&idCane="+idTemp;
                                }
                            break;
                            case 'edit':
                                showEditDog(idTemp);
                            break;
                            case 'transfer':
                                showTransferDog(idTemp);
                            break;
                        }
                    });
                });
            });
        </script>
    </head>
    <body> 
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button>
            <button onclick="newDog()">Register new dog</button><br/>
            <h1>I miei cani</h1>

            <div class="cards">
            <?php
            if(!empty($results)){
                foreach($results as $cane){
                    echo "
                    <div class='card [ is-collapsed ] context-menu-one btn btn-neutral' id='".$cane['numeroChip']."'>
                        <div class='card__inner [ js-expander ]'>
                            <div class='darken'></div>
                            <span class='nome'>".$cane['nome']."</span>
                        </div>
                        <div class='card__expander'>
                            <span class='info big'>".$cane['razza']." nato il ".Common::convDate($cane['dataNascita'])."</span>
                            <span class='info small'>Numero chip : ".$cane['numeroChip']." | Peso : ".$cane['peso']." kg | Altezza coscia : ".$cane['altCoscia']." cm | altGarrese : ".$cane['altGarrese']." cm</span>
                        </div>
                    </div>";
                }
            }else echo "Non hai ancora nessun cane. Aggiungine uno nuovo ora!";
            ?>
            </div>
            <script src="javascript/cards.js"></script>
        </div>


		<ul id="myMenu" class="contextMenu">
			<li class="transfer"><a href="#transfer">Trasferisci</a></li>
			<li class="edit separator"><a href="#edit">Modifica</a></li>
			<li class="delete"><a href="#delete">Elimina</a></li>
        </ul>
        
        
 
        <div id="modal-form" title="">
            <p class="validateTips"></p>
            <form id="paramForm">
            </form>
        </div>

        <?php include "common/footer.php"; ?>

    </body>
</html>