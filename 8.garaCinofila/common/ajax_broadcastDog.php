<?php
    require_once("Common.php" );
    require_once("Votazione.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $votazioni = new Votazioni($pdo);
    
    $giudici = json_decode($_POST['giudici'], true);
    $idGara  = $_POST['idGara'];
    $idCane  = $_POST['idCane'];

    foreach($giudici as $giudice) {
        if(!$votazioni->broadcastDog($giudice['idGiudice'], $idCane, $idGara)) echo "error";
    }
    
    echo "ok";
    
    //closes pdo connection
    $pdo = null;
?>