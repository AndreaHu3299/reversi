<?php
    require_once("Common.php");
    require_once("Cane.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $cani = new Cani($pdo);
    
    $idCane = $_POST['idCane'];
    
    echo $caneJson = $cani->getDog($idCane);

    //closes pdo connection
    $pdo = null;
?>