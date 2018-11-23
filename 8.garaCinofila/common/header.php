<?php
    require_once("common/Common.php");

    if(session_id() == '') session_start();     //if the session isn't already ope, opens the session
    $account  = "Login";
    $link     = "login.php";
    $loggedIn = false;

    if(isset($_SESSION['username'])){           // if a client credential session is found, get the name of the client or secretary to display
        $loggedIn = true;
        $username = $_SESSION['username'];      //gets username from form
        $pdo      = Common::createPDO();        //create pdo connection
        $link     = "profile.php";              //changes link of text to profile instead of login

        $accountType = $_SESSION['accountType'];//gets account type of username

        switch($accountType) {
            case Common::$USER_MANAGER:
                require_once("common/Manager.php");
                $user       = new Manager($pdo);
                $portal     = "Competition management"; //text of new button in navigation bar
                $portalLink = "management.php";         //link of new button in navigation bar
            break;
            case Common::$USER_PROPRIETARIO:
                require_once("common/Proprietario.php");
                $user       = new Proprietari($pdo);
                $portal     = "Zona proprietari";                //text of new button in navigation bar
                $portalLink = "proprietarioPortal.php";             //link of new button in navigation bar
            break;
            case Common::$USER_GIUDICE:
                require_once("common/Giudice.php");
                $user       = new Giudici($pdo);
                $portal     = "Vota";                   //text of new button in navigation bar
                $portalLink = "jVote.php";          //link of new button in navigation bar
            break;
        }

        /* //query to get the name of the client or secretary
        $query = "SELECT name FROM $dbName WHERE $idName = ?";
        if($stmt = $pdo->prepare($query)){
            $stmt->execute([$id]);
            $name = $stmt->fetch()['name'];
        } */
        
        //imports library and instatiates credenziali object
        require_once("Credenziale.php");
        $credenziali = new Credenziali($pdo);

        //gets name of the logged user
        $name = $user->getName($username);

        //sets the name
        $account = "Welcome, " . ucfirst($name);
    }

    //closes pdo connection
    $pdo = null;
?>
<header class="topbar">
    <div class="top">
        <!--<img class="logo" src="rsrc/logo.png"/>-->
        <img class="sign" src="rsrc/logo_round.png"/>
        <div class="login">
            <a id="loginText" href="<?=$link?>"><?=$account?>
                <img class="loginIcon" src="rsrc/userIcon.png"/>
            </a>
        </div>
    </div>

    <div id="divider"></div>
    
    <nav>
        <ul>
            <li><a href="index.php" title="Homepage">Home</a></li>
            <li><a href="oldContest.php" title="oldContest">Gare precedenti</a></li>
            <!--<li><a href="aboutUs.php" title="About Us">About Us</a></li>-->
            <?php 
                //if a client is logged in, change to text "login", to "welcome, *name*" and changes link to profile page instead of login page.
                if($loggedIn) echo "
            <li><a href='".$portalLink."' title='".$portal."'>".$portal."</a></li>"
            ?>
        </ul>
    </nav>
</header>