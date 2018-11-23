<?php
require_once("Common.php");

    Class ProprietarioBean{
        public $username;
        public $nome    ;
        public $cognome ;
        public $CF      ;
        public $telefono;

        public function __construct($proprietario){
            $this->username = $proprietario['username'     ];
            $this->nome     = $proprietario['nome'         ];
            $this->cognome  = $proprietario['cognome'      ];
            $this->CF       = $proprietario['codiceFiscale'];
            $this->telefono = $proprietario['telefono'     ];
        }
    }

    Class Proprietari{
        public $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getName($username) {
            $query = "SELECT nome 
                        FROM Proprietario
                       WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['nome'];
        }

        public function getCF($username) {
            $query = "SELECT codiceFiscale 
                        FROM Proprietario
                       WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['codiceFiscale'];
        }

        public function getBean($username) {
            $query = "SELECT username
                            ,codiceFiscale
                            ,nome
                            ,cognome
                            ,telefono
                        FROM Proprietario 
                       WHERE username = ? ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return json_encode($stmt->fetch());
        }

        public function createNewAccount($cf, $nome, $cognome, $telefono, $username, $password){
            require_once("Credenziale.php");
            $credenziali = new Credenziali($this->pdo);
            
            if($credenziali->createNewAccount($username, $password)){
                $query = "INSERT INTO Proprietario (
                                      username
                                     ,codiceFiscale
                                     ,nome
                                     ,cognome
                                     ,telefono)
                              VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($query);
                $result = $stmt->execute([$username, $cf, $nome, $cognome, $telefono]);
                if($result){
                    return 1;
                }else return 0;
            }else return -1;

        }
        
    }

?>