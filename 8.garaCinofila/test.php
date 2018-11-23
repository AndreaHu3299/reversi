<?php
require_once("common/Candidato.php");
require_once("common/Cane.php");
require_once("common/Common.php");
require_once("common/Credenziale.php");
require_once("common/Gara.php");
require_once("common/Giudice.php");
require_once("common/Proprietario.php");
require_once("common/Razza.php");
require_once("common/Votazione.php");

//establish a connection to DBserver
$pdo = Common::createPDO();

$candidati = new Candidati($pdo);
$cani = new Cani($pdo);
$credenziali = new Credenziali($pdo);
$gare = new Gare($pdo);
$giudici = new Giudici($pdo);
$proprietari = new Proprietari($pdo);
$razze = new Razze($pdo);
$votazioni = new Votazioni($pdo);

$classifica = json_decode($votazioni->getClassifica(3), true);
print_r($classifica);

?>