<?php
require_once("Common.php");

    Class CaneBean{
        //public $codProprietario;
        public $dataNascita;
        public $numeroChip ;
        public $altGarrese ;
        public $altCoscia  ;
        public $idRazza    ;
        public $razza      ;
        public $nome       ;
        public $peso       ;
        
        public function __construct($cane){
            $this->dataNascita = $cane['dataNascita'];
            $this->numeroChip  = $cane['numeroChip' ];
            $this->altGarrese  = $cane['altGarrese' ];
            $this->altCoscia   = $cane['altCoscia'  ];
            $this->idRazza     = $cane['idRazza'    ];
            $this->razza       = $cane['nomeRazza'  ];
            $this->nome        = $cane['nome'       ];
            $this->peso        = $cane['peso'       ];
        }

    }

    Class Cani{
        public $pdo;
        private $caneBeanList;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getDog($numeroChip) {
            $query = "SELECT c.numeroChip
                            ,c.nome
                            ,c.dataNascita
                            ,r.nomeRazza
                            ,r.idRazza
                            ,c.peso
                            ,c.altGarrese
                            ,c.altCoscia
                            ,p.nome as nomePropr
                        FROM Cane         as c
                            ,Razza        as r
                            ,Proprietario as p
                       WHERE c.numeroChip = ?
                         AND r.idRazza = c.razza
                         AND p.codiceFiscale = c.codProprietario
                    ORDER BY c.nome ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$numeroChip]);
            $result = $stmt->fetch();
            return json_encode($result);
        }

        public function getDogs($username) {
            $this->caneBeanList = array();
            $query = "SELECT c.numeroChip
                            ,c.nome
                            ,c.dataNascita
                            ,r.nomeRazza
                            ,r.idRazza
                            ,c.peso
                            ,c.altGarrese
                            ,c.altCoscia
                        FROM Cane         as c
                            ,Razza        as r
                            ,Proprietario as p
                       WHERE p.username = ?
                         AND c.codProprietario = p.codiceFiscale
                         AND r.idRazza = c.razza
                    ORDER BY c.nome ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);

			while($result = $stmt->fetch()){
				$this->caneBeanList[] = new CaneBean($result);
            }
            return json_encode($this->caneBeanList);
        }

        public function getMyDogs($username, $idGara) {
            $this->caneBeanList = array();
            $query = "SELECT cn.numeroChip
                            ,cn.nome
                            ,cn.dataNascita
                            ,r.nomeRazza
                            ,r.idRazza
                            ,cn.peso
                            ,cn.altGarrese
                            ,cn.altCoscia
                        FROM Cane         as cn
                            ,Razza        as r
                            ,Proprietario as p
                       WHERE p.username         = ?
                         AND cn.codProprietario = p.codiceFiscale
                         AND r.idRazza          = cn.razza
                         AND cn.numeroChip NOT IN (SELECT idCane 
                                                     FROM Candidato
                                                    WHERE idGara = ?)
                    ORDER BY cn.nome ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username, $idGara]);
            while($result = $stmt->fetch()){
				$this->caneBeanList[] = new CaneBean($result);
            }
            return json_encode($this->caneBeanList);
        }

        public function getContestDogs($idGara) {
            $this->caneBeanList = array();
            $query = "SELECT idCane
                        FROM Candidato
                       WHERE idGara = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            while($result = $stmt->fetch()){
                $idCaneTemp = $result['idCane'];
                $caneTemp = json_decode($this->getDog($idCaneTemp));
				$this->caneBeanList[] = $caneTemp;
            }
            return json_encode($this->caneBeanList);
        }
        
//----------------------------------------------------

        public function insert($numeroChip, $username, $nome, $dataNascita, $idRazza, $peso, $altGarrese, $altCoscia) {
            require_once("Proprietario.php");
            $proprietari = new Proprietari($this->pdo);
            
            $codProprietario = $proprietari->getCF($username);
            
            $query = "INSERT INTO Cane 
                                (numeroChip
                                ,codProprietario
                                ,nome
                                ,dataNascita
                                ,razza
                                ,peso
                                ,altGarrese
                                ,altCoscia)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            return $this->pdo->prepare($query)->execute([$numeroChip, $codProprietario, $nome, $dataNascita, $idRazza, $peso, $altGarrese, $altCoscia]);
        }
        
        public function update($action, $idCane, $parameters){
            $parameters = json_decode($parameters, true);
            
            switch($action){
                case 'edit':
                    $altCoscia  = $parameters['altCoscia' ];
                    $altGarrese = $parameters['altGarrese'];

                    $query = "UPDATE Cane
                                 SET altCoscia  = ?
                                    ,altGarrese = ?
                               WHERE numeroChip = ?";
                    return $this->pdo->prepare($query)->execute([$altCoscia, $altGarrese, $idCane]);
                break;
                case 'transfer':
                    $username = urldecode($parameters['username']);

                    require_once("Proprietario.php");
                    $proprietari = new Proprietari($this->pdo);
                    $codProprietario = $proprietari->getCF($username);

                    print_r($codProprietario);

                    if($codProprietario == null) {
                        return false;
                    }

                    $query = "UPDATE Cane
                                 SET codProprietario = ?
                               WHERE numeroChip = ?";
                    return $this->pdo->prepare($query)->execute([$codProprietario, $idCane]);
                break;
            }
        }

        public function delete($idCane) {
            $query = "DELETE FROM cane WHERE numeroChip = ?";
            return $this->pdo->prepare($query)->execute([$idCane]);
        }       
    }

?>