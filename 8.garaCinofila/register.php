<?php 
$usr  = "";
$psw = "";

//if login form is received, it will try to login
if(!empty($_POST)) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $telefono = $_POST['telefono'];
    $cognome  = $_POST['cognome' ];
    $nome     = $_POST['nome'    ];
    $CF       = strtoupper($_POST['CF']);
    require_once("common/Proprietario.php");

    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $proprietari = new Proprietari($pdo);
    
    //check for credentials
    $result = $proprietari->createNewAccount($CF, $nome, $cognome, $telefono, $username, $password);
    
    switch($result){
        case 1:     //registration successful
            session_start();
            $_SESSION['accountType'] = Common::$USER_PROPRIETARIO;
            $_SESSION['username'   ] = $username;

            echo "<script type='text/javascript'>alert('Account registration successful!')</script>";

            header("Location: index.php");
            exit();
            break;

        case 0:     //ehmmmm... not supposed to happen?
            echo "<script type='text/javascript'>alert('Something went wrong...')</script>";            
            break;
        case -1:    //Account already taken
            echo "<script type='text/javascript'>alert('Email address already taken')</script>";
        break;
    }

    //closes pdo connection
    $pdo = null;    
}
?>
<html>
    <head>
        <title>Register</title>
        <link rel="stylesheet" href="styles/common.css"/>
        <link rel="stylesheet" href="styles/register.css" />
        <script>
            function check() {
                var CF       = document.getElementById("CF");
                var psw      = document.getElementById("psw" ).value;
                var pswCheck = document.getElementById("psw2").value;
                var email    = document.getElementById("email");
                var telefono = document.getElementById("telefono");

                return (checkPassword(psw, psw2) && validateCF(CF) && validateEmail(email) && validatePhone(telefono));
            }

            function checkPassword(psw1, psw2) {
                if(psw1==psw2){
                    alert("Password does not match!");
                    return false;
                }else{
                    return true;
                }
            }

            function validateEmail(email)
            {
                var formatMail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

                if(email.value.search(formatMail) == -1)
                {
                    alert("L'indirizzo di posta elettronica inserita e' invalida!");
                    email.focus();
                    return false;  
                }else
                {
                    return true;
                }
            }
            function validateCF(codiceFiscale)
            {
                // Definisco un pattern per il confronto
                var formatCF = /^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$/;

                // utilizzo il metodo search per verificare che il valore inserito nel campo
                // di input rispetti la stringa di verifica (pattern)
                if (codiceFiscale.value.search(formatCF) == -1)
                {
                    // In caso di errore stampo un avviso
                    alert("Il codice fiscale inserito e' invalido!");
                    codiceFiscale.focus();
                    return false;
                }else{
                    return true;
                }
            }
            function validatePhone(numero)
            {
                var formatPhone = /^\d{10}$/;
                if(numero.value.search(formatPhone) == -1){
                    alert("Il numero telefonico inserito e' invalido!");
                    numero.focus();
                    return false;
                }else{
                    return true;
                }
            }
        </script>
    </head>
    <body>
		<?php include "common/header.php"; ?>

            <img class="profileIcon" src="rsrc/usericon.png"/>
            <form class="grid-container" action="register.php" method="post" onsubmit="return check()">
                
                <div   class='grid-item text'>Nome</div>
                <input class="grid-item input" type="text"     id="nome"     name="nome"     required autofocus/>

                <div   class='grid-item text'>Cognome</div>
                <input class="grid-item input" type="text"     id="cognome"  name="cognome"  required/>

                <div   class='grid-item text'>Codice Fiscale</div>
                <input class="grid-item input" type="text"     id="CF"       name="CF"       required/>

                <div   class='grid-item text'>Numero telefono</div>
                <input class="grid-item input" type="number"   id="telefono" name="telefono" required/>

                <div   class='grid-item text'>E-mail</div>
                <input class="grid-item input" type="text"     id="email"    name="username" required placeholder="ex:esempio@esempio.it"/>

                <div   class='grid-item text'>Password</div>
                <input class="grid-item input" type="password" id="psw"      name="password" required/>

                <div   class='grid-item text'>Reinserisci password</div>
                <input class="grid-item input" type="password" id="psw2" required/>

                <input class="grid-item" type="submit" id="register" value="Register"/>
            </form>
        </div>
        
		<?php include "common/footer.php"; ?>
    </body>
</html>