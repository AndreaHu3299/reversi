<?php
    require_once("Common.php");
    require_once("Votazione.php");
    require_once("Giudice.php");

    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $votazioni = new Votazioni($pdo);
    $giudici   = new Giudici($pdo);

    $voto     = $_POST['voto'    ];
    $idGara   = $_POST['idGara'  ];
    $idCane   = $_POST['idCane'  ];
    $username = $_POST['username'];
    $commento = $_POST['commento'];

    $idGiudice = json_decode($giudici->getBean($username), true)['idGiudice'];
    
    echo $votazioni->vota($idGiudice, $idCane, $idGara, $voto, $commento);
    
    //closes pdo connection
    $pdo = null;
?>