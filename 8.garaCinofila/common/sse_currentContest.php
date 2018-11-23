<?php
session_start();

require_once("Votazione.php");
require_once("Common.php");
require_once("Gara.php");

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Check user session validity
if (!isset($_SESSION['username']) || $_SESSION['accountType'] != Common::$USER_GIUDICE) {
    $sse_response = "data: " . json_encode(array('error' => 1, 'error_message' => 'User Authentication Failed')) . PHP_EOL . PHP_EOL;
    echo $sse_response;
    exit();
}


//establish a connection to DBserver
$pdo = Common::createPDO();

$idGiudice = $_SESSION['idGiudice'];
$idGara    = $_SESSION['idGara'   ];

$gare = new Gare($pdo);

$stato = $gare->getStatus($idGara);

switch($stato){
    case Gare::$GARA_INCORSO:
        $votazioni = new Votazioni($pdo);
        $nextDog = array();
        $nextDog['idCane'] = $votazioni->getNextDog($idGiudice, $idGara);

        $output = "data: " .json_encode($nextDog). PHP_EOL . PHP_EOL;

        echo "event: idCane\n".$output. PHP_EOL . PHP_EOL;
    break;
    case Gare::$GARA_TERMINATA:
        echo "event: garaTerminata\ndata: true".$output. PHP_EOL . PHP_EOL;
    break;
    case Gare::$GARA_PROGRAMMATA:
        echo "event: garaProgrammata\ndata: true".$output. PHP_EOL . PHP_EOL;        
    break;
}



flush();
?>