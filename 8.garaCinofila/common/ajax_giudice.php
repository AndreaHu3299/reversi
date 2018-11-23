<?php
    require_once("Common.php" );
    require_once("Giudice.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $giudici = new Giudici($pdo);
    
    $idGara = $_POST['idGara'];
    
    echo $judgesJson = $giudici->getContestJudgesStatus($idGara);
    
    //closes pdo connection
    $pdo = null;
?>