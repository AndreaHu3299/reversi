<?php
require_once("Common.php");

    Class ManagerBean{
        public $username;
        public $nome    ;
        public $cognome ;

        public function __construct($manager){
            $this->username = $manager['username'];
            $this->nome     = $manager['nome'    ];
            $this->cognome  = $manager['cognome' ];
        }

    }

    Class Manager{
        public $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getName($username) {
            $query = "SELECT nome FROM Manager WHERE username = ? ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['nome'];
        }

        public function getBean($username) {
            $query = "SELECT username, nome, cognome FROM Manager WHERE username = ? ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            $result = $stmt->fetch();
            return json_encode(new ManagerBean($result));
        }
        
    }

?>