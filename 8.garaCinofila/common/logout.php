<?php
    session_start();                        //opens session
    unset($_SESSION['username']);              //deletes the client from session
    session_destroy();                      //closes session
    header("Location: ../index.php");       //redirects to the homepage
    exit();
?>