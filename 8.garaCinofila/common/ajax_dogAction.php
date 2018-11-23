<?php 
    session_start();
    require_once("Common.php");
    require_once("Cane.php"  );
    /*
//Check if a session is active and if the logged in account is a secretary, if not go back to login page
    if(!isset($_SESSION['username']) || $_SESSION['accountType'] != Common::$USER_PROPRIETARIO){
        header("Location: login.php");
        die();
    }
//Check if GET data are present
    if(!isset($_GET['action']) || !isset($_GET['idCane'])){
        header("Location: proprietarioPortal.php");
        die();
    }*/
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $cani = new Cani($pdo);

    if(isset($_POST)){
        $action = $_POST['action'];
        $idCane = $_POST['idCane'];
        $parameters = $_POST['parameters'];

        $result = $cani->update($action, $idCane, $parameters);
    }

    echo $result;

    //closes pdo connection
    $pdo = null;
?>
