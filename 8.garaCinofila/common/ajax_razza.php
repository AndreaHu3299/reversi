<?php
    require_once("Common.php" );
    require_once("Razza.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $razze = new Razze($pdo);
    
    $idRazza = $_POST['idRazza'];

    echo $razze->getRazza($idRazza);
    
    //closes pdo connection
    $pdo = null;
?>