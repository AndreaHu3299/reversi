<?php
    require_once("Common.php" );
    require_once("Votazione.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $votazioni= new Votazioni($pdo);
    
    $giudiciElec = $_POST['giudiciElec'];
    $idGara      = $_POST['idGara'     ];
    $idCane      = $_POST['idCane'     ];

    echo $votazioni->getVoti($giudiciElec, $idGara, $idCane);
    
    //closes pdo connection
    $pdo = null;
?>