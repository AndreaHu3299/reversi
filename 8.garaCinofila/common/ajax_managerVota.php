<?php
    require_once("Common.php" );
    require_once("Votazione.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $votazioni = new Votazioni($pdo);
    
    $idGiudice = $_POST['idGiudice'];
    $idGara    = $_POST['idGara'   ];
    $idCane    = $_POST['idCane'   ];
    $voto      = $_POST['voto'     ];

    if($votazioni->vota($idGiudice, $idCane, $idGara, $voto, "")){
        echo $idGiudice;
    }else echo false;
    
    //closes pdo connection
    $pdo = null;
?>