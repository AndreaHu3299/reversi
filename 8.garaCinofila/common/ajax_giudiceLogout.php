<?php
    require_once("Common.php");
    require_once("Giudice.php");

    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $giudici = new Giudici($pdo);

    $idGara   = $_POST['idGara'];
    $username = $_POST['username'];

    $idGiudice = json_decode($giudici->getBean($username), true)['idGiudice'];
    
    $giudici->logout($idGiudice, $idGara);
    
    //closes pdo connection
    $pdo = null;
?>