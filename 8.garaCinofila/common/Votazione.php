<?php
require_once("Common.php");

    Class VotazioneBean{
        public $idGiudice;
        public $commento ;
        public $idGara   ;
        public $idCane   ;
        public $voto     ;

        public function __construct($votazione){
            $this->idGiudice = $votazione['idGiudice'];
            $this->commento  = $votazione['commento' ];
            $this->idGara    = $votazione['idGara'   ];
            $this->idCane    = $votazione['idCane'   ];
            $this->voto      = $votazione['voto'     ];
        }

    }

    Class Votazioni{
        public $pdo;
        private $votazioneBeanList;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function vota($idGiudice, $idCane, $idGara, $voto, $commento) {
            $query = "UPDATE Votazione 
                         SET voto      = ?
                            ,commento  = ?
                       WHERE idGiudice = ?
                         AND idCane    = ?
                         AND idGara    = ?";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$voto, $commento, $idGiudice, $idCane, $idGara]);
        }
  
        public function broadcastDog($idGiudice, $idCane, $idGara) {
            $query = "INSERT INTO Votazione 
                                 (idGiudice
                                 ,idCane
                                 ,idGara
                                 ,voto
                                 ,commento)
                           VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$idGiudice, $idCane, $idGara, null, null]);
        }

        public function getNextDog($idGiudice, $idGara) {
            $query = "SELECT idCane
                        FROM Votazione 
                       WHERE idGiudice = ?
                         AND idGara    = ?
                         AND voto IS NULL";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGiudice, $idGara]);
            return $stmt->fetch()['idCane'];
        }

        public function getVoto($idGiudice, $idGara, $idCane) {
            $query = "SELECT idGiudice
                            ,commento
                            ,idGara
                            ,idCane 
                            ,voto
                        FROM Votazione 
                       WHERE idGiudice = ?
                         AND idGara    = ?
                         AND idCane    = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGiudice, $idGara, $idCane]);
            return $stmt->fetch();
        }

        public function getVoti($jsonGiudici, $idGara, $idCane) {
            $this->votazioneBeanList = array();
            $judges = json_decode($jsonGiudici, true);
            if(count($judges) > 0) {
                foreach($judges as $judge) {
                    $idGiudiceTemp = $judge['idGiudice'];
                    $this->votazioneBeanList[] = $this->getVoto($idGiudiceTemp, $idGara, $idCane);
                }
            }
            return json_encode($this->votazioneBeanList);
        }

        public function getClassifica($idGara) {
            $this->votazioneBeanList = array();
            $query = "SELECT c.nome    AS nomeCane
                            ,p.nome    AS nomeProp
                            ,p.cognome AS cognomeProp
                            ,ROUND(AVG(v.voto), 2) as media
                        FROM Votazione    AS v
                            ,Cane         AS c
                            ,Proprietario AS p
                       WHERE v.idGara        = ?
                         AND c.numeroChip    = v.idCane
                         AND p.codiceFiscale = c.codProprietario
                    GROUP BY v.idCane
                    ORDER BY SUM(v.voto) DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            while($result = $stmt->fetch()){
                $candidatoTemp = array();
                $candidatoTemp['media'      ] = $result['media'      ];
                $candidatoTemp['nomeCane'   ] = $result['nomeCane'   ];
                $candidatoTemp['nomeProp'   ] = $result['nomeProp'   ];
                $candidatoTemp['cognomeProp'] = $result['cognomeProp'];
                $this->votazioneBeanList[] = $candidatoTemp;
            }
            return json_encode($this->votazioneBeanList);
        }
    }

?>