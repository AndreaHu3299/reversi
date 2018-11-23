<?php 
session_start();

require_once("common/Gara.php"  );
require_once("common/Common.php");

//Check if a session is active, if not go back to login page
if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_MANAGER){
    header("Location: login.php");
    die();
}

//establish a connection to DBserver
$pdo = Common::createPDO();

$gare = new Gare($pdo);

//if a contest modification form is received, attempt so
if(!empty($_POST)){
    $luogo  = htmlspecialchars($_POST['luogo'], ENT_QUOTES);
    $nome   = htmlspecialchars($_POST['nome' ], ENT_QUOTES);
    $idGara = $_POST['idGara'];
    $data   = $_POST['data'];

    $result = $gare->update($idGara, $nome, $luogo, $data);

    if($result) {   //if account creation was successful, show message and redirect to manage members
        echo "<script type='text/javascript'>alert('Successfully modified!');</script>";
        echo "<script type='text/javascript'>window.location.href='mOldContest.php';</script>";
    }
    else {          //if account creation was unsuccessful, show error message
        echo "<script type='text/javascript'>alert('Error encountered!');</script>";
    }
}

$idGara = $_GET['idGara'];
$result = json_decode($gare->getGara($idGara), true);

//closes pdo connection
$pdo = null;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>New Client</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <!--<link rel="stylesheet" href="styles/snewClient.css"/>-->
        <script>
            function goBack() {
                location.href='mOldContest.php';
            }
        </script>
    </head>
    <body>
        
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>

            <form method="post" action="mEditContest.php">
                <p class="category">Nome gara</p>
                <input class="formInput" type="text" name="nome" value="<?=$result['nome']?>" required/>

                <p class="category clientOption">Data</p>
                <input class="formDate clientOption" type="date" name="data" min="<?php echo date('Y-m-d')?>" value="<?=$result['data']?>"autofocus required/>

                <p class="category">Luogo</p>
                <input class="formInput" type="text" name="luogo" value="<?=$result['luogo']?>" required/>
                
                <input type="hidden" name="idGara" value="<?=$idGara?>"/>

                <input class="formSubmit" type="submit" id="submit" value="Conferm"/>
            </form>
        </div>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>