<?php
require_once("Common.php");

    Class Credenziali{
        public $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }


        public function getCredential($username) {
            $query = "SELECT password
                        FROM Credenziale
                       WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['password'];

        }

        public function checkCredential($username, $password) {
            $cred = $this->getCredential($username);
            if(sizeof($cred) > 0) {
                if(password_verify($cred, $password)) return 1;    //correct password
                else return 0;                      //wrong password
            }
            return -1;                              //wrong username
        }

        public function getAccountType($username) {
            $query = "SELECT accountType
                        FROM Credenziale
                       WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['accountType'];
        }

        public function createNewAccount($username, $password){
            $accountType = Common::$USER_PROPRIETARIO;
            $query = "INSERT INTO Credenziale (
                             username
                            ,password
                            ,accountType)
                     VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$username, $password, $accountType]);
        }
        
    }

?>