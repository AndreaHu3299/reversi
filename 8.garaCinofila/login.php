<?php 
$usr  = "";
$psw = "";

//if an account is already logged on
if(isset($_SESSION['username'])) {
    header("Location: index.php");
    die();
}

//if login form is received, it will try to login
if(!empty($_POST)) {
    $usr = $_POST['username'];
    $psw = $_POST['password'];

    require_once("common/Credenziale.php");

    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $credenziali = new Credenziali($pdo);
    
    //check for credentials
    $result = $credenziali->checkCredential($usr, $psw);
    
    switch($result){
        case 1:     //correct password
            $accountType = $credenziali->getAccountType($usr);

            session_start();
            $_SESSION['accountType'] = $accountType;
            $_SESSION['username'   ] = $usr;

            switch($accountType)
            {
                case Common::$USER_MANAGER:
                    $link = "management.php";       //link to manager portal
                break;
                case Common::$USER_PROPRIETARIO:
                    $link = "proprietarioPortal.php";           //link to proprietario portal
                break;
                case Common::$USER_GIUDICE:
                    $link = "jVote.php";             //link to giudice portal
                break;
            }
            header("Location: $link");
            exit();
            break;

        case 0:     //wrong password
            echo "<script type='text/javascript'>alert('Username or password incorrect!')</script>";            
            break;
        case -1:    //account non-existent
            echo "<script type='text/javascript'>alert('Account does not exist!')</script>";
        break;
    }

    //closes pdo connection
    $pdo = null;    
}
?>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/login.css" />
        <script>
            function check() {
                var usr = document.getElementById("usr").value;
                var psw = document.getElementById("psw").value;
                if(usr==""||psw=="") {
                    alert("Inputs are empty!");
                    return false;
                }
            }
        </script>
    </head>
    <body>
		<?php include "common/header.php"; ?>

            <img class="profileIcon" src="rsrc/usericon.png"/>
            <form action=login.php method="post" onsubmit="return check()">
                <input class="inputField" type="text"     placeholder="Username" id="usr" name="username" value="<?=$usr?>" autofocus required/><br/>
                <input class="inputField" type="password" placeholder="Password" id="psw" name="password" value="<?=$psw?>" required/><br/>
                <input class="submit"     type="submit" id="login" value="Login"/><br/>
            </form>
        </div>

        <div class="registerText">Non sei ancora registrato? <a href="register.php">Registrati ora</a>!
        
		<?php include "common/footer.php"; ?>
    </body>
</html>