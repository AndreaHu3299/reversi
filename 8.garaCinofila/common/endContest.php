<?php
session_start();                                            //opens session

$idGara = $_SESSION['idGara'];
unset($_SESSION['idGara']);                                 //deletes the idGara from session

require_once("Gara.php");
require_once("Common.php");

//establish a connection to DBserver
$pdo = Common::createPDO();
$gare = new Gare($pdo);

$gare->updateStatus($idGara, Gare::$GARA_TERMINATA);

header("Location: ../mClassifica.php?idGara=$idGara");     //redirects to the classifica
exit();
?>