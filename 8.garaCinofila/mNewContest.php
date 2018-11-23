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
$username = $_SESSION['username'];

//if a contest creation form is received, attempt so
if(!empty($_POST)){
    $luogo = htmlspecialchars($_POST['luogo'], ENT_QUOTES);
    $nome  = $_POST['nome'];
    $data  = $_POST['data'];

    $result = $gare->insert($nome, $luogo, $data);

    if($result) {   //if account creation was successful, show message and redirect to manage members
        echo "<script type='text/javascript'>alert('Successfully added!');</script>";
        echo "<script type='text/javascript'>window.location.href='mOldContest.php';</script>";
    }
    else {          //if account creation was unsuccessful, show error message
        echo "<script type='text/javascript'>alert('Error encountered!');</script>";
    }
}

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
                location.href='management.php';
            }
        </script>
    </head>
    <body>
        
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>

            <form method="post" action="mNewContest.php">
                <p class="category">Nome gara</p>
                <input class="formInput" type="text" name="nome" required/>

                <p class="category clientOption">Data</p>
                <input class="formDate clientOption" type="date" name="data" min="<?php echo date('Y-m-d')?>" autofocus required/>

                <p class="category">Luogo</p>
                <input class="formInput" type="text" name="luogo" required/>

                <input class="formSubmit" type="submit" id="submit" value="Conferm"/>
            </form>
        </div>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>