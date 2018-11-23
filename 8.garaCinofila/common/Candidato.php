<?php
require_once("Common.php");

    Class CandidatoBean{

        public function __construct($socio){

        }

    }

    Class Candidati{
        public $pdo;
        private $candidatoBeanList;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function getName($username) {
            $query = "SELECT nome 
                        FROM Candidato
                       WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['nome'];
        }
    }

?>