<?php 
session_start();

require_once("common/Cane.php"  );
require_once("common/Common.php");

//Check if a session is active, if not go back to login page
if(!isset($_SESSION['username'])||$_SESSION['accountType'] != Common::$USER_PROPRIETARIO){
    header("Location: login.php");
    die();
}

//establish a connection to DBserver
$pdo = Common::createPDO();

$cani = new Cani($pdo);
$username = $_SESSION['username'];

//if a contest creation form is received, attempt so
if(!empty($_POST)){
    $nome         = htmlspecialchars($_POST['nome'], ENT_QUOTES);
    $dataNascita  = $_POST['dataNascita'];
    $numeroChip   = $_POST['numeroChip' ];
    $razza        = $_POST['razza'      ];
    $peso         = $_POST['peso'       ];
    $altGarrese   = $_POST['altGarrese' ];
    $altCoscia    = $_POST['altCoscia'  ];

    $result = $cani->insert($numeroChip, $username, $nome, $dataNascita, $razza, $peso, $altGarrese, $altCoscia);

    if($result) {   //if account creation was successful, show message and redirect to manage members
        echo "<script type='text/javascript'>alert('Successfully added!');</script>";
        echo "<script type='text/javascript'>window.location.href='pMyDogs.php';</script>";
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
        <title>Register dog</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="styles/common.css"/>
        <script>
            function goBack() {
                location.href='pMyDogs.php';
            }
        </script>
    </head>
    <body>
        
        <?php include "common/header.php"; ?>

        <div class="mainContent">
            <button id="back" onclick="goBack()">Back</button><br/>

            <form method="post" action="pNewDog.php">
                <p class="category">Nome</p>
                <input class="formInput" type="text" name="nome" required/>

                <p class="category">Numero microchip</p>
                <input class="formInput" type="number" name="numeroChip" min="100000000000000" max="999999999999999" required/>

                <p class="category clientOption">Data nascita</p>
                <input class="formDate clientOption" type="date" name="dataNascita" max="<?php echo date('Y-m-d')?>" required/>

                <p class="category">Razza</p>
                <input class="formInput" type="text" name="razza" required/>

                <p class="category">Peso</p>
                <input class="formInput" type="number" name="peso" step="0.01" min="0" required/>
                
                <p class="category">Altezza garrese</p>
                <input class="formInput" type="number" name="altGarrese" min="0" placeholder="in cm" required/>
                
                <p class="category">Altezza coscia</p>
                <input class="formInput" type="number" name="altCoscia" min="0" placeholder="in cm" required/>

                <input class="formSubmit" type="submit" id="submit" value="Conferm"/>
            </form>
        </div>
        
        <?php include "common/footer.php"; ?>
    </body>
</html>