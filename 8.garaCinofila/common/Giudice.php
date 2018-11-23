<?php
require_once("Common.php");

    Class GiudiceBean{
        public $idGiudice;
        public $username ;
        public $cognome  ;
        public $electro  ;
        public $nome     ;

        public function __construct($giudice){
            $this->idGiudice = $giudice['idGiudice'];
            $this->username  = $giudice['username' ];
            $this->cognome   = $giudice['cognome'  ];
            $this->nome      = $giudice['nome'     ];
            if(isset($giudice['electro'])) $this->electro = $giudice['electro'];
        }
    }
    Class GiudiceElettronicoBean{
        public $idSessione;
        public $idGiudice ;
        public $electro   ;
        public $stato     ;
        
        public function __construct($giudice){
            $this->idSessione = $giudice['idSessione'];
            $this->idGiudice  = $giudice['idGiudice' ];
            $this->electro    = $giudice['electro'   ];
            $this->stato      = $giudice['stato'     ];
        }
    }

    Class Giudici{
        public static $STATO_READY = 1;
        public static $STATO_VOTING = 5;
        public static $STATO_OFFLINE = 0;
        
        public static $ELECTR_NO  = 8;
        public static $ELECTR_YES = 4;


        
        public $pdo;
        private $giudiceBeanList;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getList() {
            $this->giudiceBeanList = array();
            $query = "SELECT idGiudice
                            ,username
                            ,cognome
                            ,nome
                        FROM Giudice
                    ORDER BY nome, cognome DESC";
            $results = $this->pdo->query($query);

			foreach($results as $result){
				$this->giudiceBeanList[] = new GiudiceBean($result);
            }

            return json_encode($this->giudiceBeanList);
        }
        
        public function getBean($username) {
            $query = "SELECT idGiudice
                            ,username
                            ,nome
                            ,cognome
                        FROM Giudice
                       WHERE username = ? ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return json_encode($stmt->fetch());
        }
        
        public function getName($username) {
            $query = "SELECT nome FROM Giudice WHERE username = ? ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$username]);
            return $stmt->fetch()['nome'];
        }

        public function getContestJudgesList($idGara) {
            $this->giudiceBeanList = array();
            $query = "SELECT g.idGiudice
                            ,g.username
                            ,g.cognome
                            ,g.nome
                            ,e.electro
                        FROM Giudice            as g
                            ,GiudiceElettronico as e
                       WHERE e.idGara    = ?
                         AND g.idGiudice = e.idGiudice
                    ORDER BY g.nome, g.cognome DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);

			while($result = $stmt->fetch()){
				$this->giudiceBeanList[] = new GiudiceBean($result);
            }

            return json_encode($this->giudiceBeanList);
        }

        public function getContestJudgesStatus($idGara) {
            $this->giudiceBeanList = array();
            $query = "SELECT idGiudice
                            ,idSessione
                            ,electro
                            ,stato
                        FROM GiudiceElettronico
                       WHERE idGara    = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);

			while($result = $stmt->fetch()){
				$this->giudiceBeanList[] = new GiudiceElettronicoBean($result);
            }

            return json_encode($this->giudiceBeanList);
        }
        
//--------------------------------------------------------------------------------------------------
        
        public function login($idSessione, $idGiudice, $idGara) {
            $query = "UPDATE GiudiceElettronico
                         SET idSessione = ?
                            ,stato      = ?
                       WHERE idGara     = ?
                         AND idGiudice  = ?";
            return $this->pdo->prepare($query)->execute([$idSessione, $this::$STATO_READY, $idGara, $idGiudice]);
        }
        
        public function logout($idGiudice, $idGara) {
            $query = "UPDATE GiudiceElettronico
                         SET idSessione = ?
                            ,stato      = ?
                       WHERE idGara     = ?
                         AND idGiudice  = ?";
            return $this->pdo->prepare($query)->execute([null, $this::$STATO_OFFLINE, $idGara, $idGiudice]);
        }

//--------------------------------------------------------------------------------------------------

        public function updateJudges($idGara, $jsonJudges) {
            $judges = json_decode($jsonJudges, true);
            $query = "INSERT INTO GiudiceElettronico 
                                 (idGiudice
                                 ,idGara
                                 ,idSessione
                                 ,stato
                                 ,electro) 
                           VALUES (?, ?, NULL, ?, ?)";
            $stmt = $this->pdo->prepare($query);

            if(count($judges) > 0) {
                foreach($judges as $idGiudice) {
                    $stmt->execute([$idGiudice, $idGara, $this::$STATO_OFFLINE, $this::$ELECTR_NO]);
                }
                return true;
            }else return false;
        }

        public function setOnline($idGara, $idGiudice, $idSessione){
            $query = "UPDATE GiudiceElettronico
                         SET stato     = ?
                       WHERE idGara    = ?
                         AND idGiudice = ?";
            $stmt = $this->pdo->prepare($query);
            
            if(count($elecJudges) > 0) {
                foreach($elecJudges as $idGiudice) {
                    $stmt->execute([$this::$STATO_READY, $idGara, $idGiudice]);
                }
                return true;
            }else return false;
        }

        public function updateStatus($idGara, $idGiudice, $status){
            $query = "UPDATE GiudiceElettronico
                         SET stato     = ?
                       WHERE idGara    = ?
                         AND idGiudice = ?";
            return $this->pdo->prepare($query)->execute([$status, $idGara, $idGiudice]);
        }

        public function updateElectronicJudges($idGara, $jsonElecJudges) {
            $elecJudges = json_decode($jsonElecJudges, true);

            $query = "UPDATE GiudiceElettronico
                         SET electro   = ?
                       WHERE idGara    = ?
                         AND idGiudice = ?";
            $stmt = $this->pdo->prepare($query);
            
            if(count($elecJudges) > 0) {
                foreach($elecJudges as $idGiudice) {
                    $stmt->execute([$this::$ELECTR_YES, $idGara, $idGiudice]);
                }
                return true;
            }else return false;
        }
    }

?>