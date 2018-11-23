<?php
require_once("Common.php");

    Class GaraBean{
        public $data  ;
        public $nome  ;
        public $luogo ;
        public $stato ;
        public $idGara;

        public function __construct($gara){
            $this->data   = $gara['data'  ];
            $this->nome   = $gara['nome'  ];
            $this->luogo  = $gara['luogo' ];
            $this->stato  = $gara['stato' ];
            $this->idGara = $gara['idGara'];
        }
    }

    Class Gare{
        public static $GARA_PROGRAMMATA = 0;
        public static $GARA_APERTA      = 5;
        public static $GARA_INCORSO     = 2;
        public static $GARA_TERMINATA   = 6;

        public $pdo;
        private $garaBeanList;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function getList() {
            $this->garaBeanList = array();
            $query = "SELECT idGara
                            ,stato
                            ,nome
                            ,data
                            ,luogo
                        FROM Gara
                    ORDER BY idGara DESC";
            $results = $this->pdo->query($query);

			foreach($results as $result){
				$this->garaBeanList[] = new GaraBean($result);
            }

            return json_encode($this->garaBeanList);
        }
        
        public function getSpecificList($filter) {
            $this->garaBeanList = array();
            $query = "SELECT idGara
                            ,stato
                            ,nome
                            ,data
                            ,luogo
                        FROM Gara
                       WHERE stato = ?
                    ORDER BY idGara DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$filter]);

			while($result = $stmt->fetch()){
				$this->garaBeanList[] = new GaraBean($result);
            }

            return json_encode($this->garaBeanList);
        }

        public function getGara($idGara) {
            $query = "SELECT idGara
                            ,stato
                            ,nome
                            ,data
                            ,luogo
                        FROM Gara
                       WHERE idGara = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            return json_encode(new GaraBean($stmt->fetch()));
        }

        public function getStatus($idGara) {
            $query = "SELECT stato
                        FROM Gara
                       WHERE idGara = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            return $stmt->fetch()['stato'];
        }

        public function getParticipants($idGara) {
            $query = "SELECT COUNT(idCane) AS partecipanti
                        FROM Candidato
                       WHERE idGara = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            return $stmt->fetch()['partecipanti'];
        }

        public function getLastDog($idGara){
            $query = "SELECT idCane
                        FROM Candidato
                       WHERE idGara = ?
                         AND idCane NOT IN (
                             SELECT DISTINCT idCane
                               FROM Votazione
                              WHERE idGara = ?)
                       LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara, $idGara]);
            return $stmt->fetch()['idCane'];
        }

        /*public function getMaxPoints($idGara) {
            $query = "SELECT (COUNT(idGiudice) * 10) AS maxPoints
                        FROM GiudiceElettronico
                       WHERE idGara = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idGara]);
            return $stmt->fetch()['maxPoints'];
        }*/

//-----------------------------------------------------------------------------------------

        public function insert($nome, $luogo, $data) {
            $query = "INSERT INTO Gara 
                                 (nome
                                 ,data
                                 ,luogo
                                 ,stato) 
                           VALUES (?, ?, ?, ?)";
            return $this->pdo->prepare($query)->execute([$nome, $data, $luogo, $this::$GARA_PROGRAMMATA]);
        }

        public function update($idGara, $nome, $luogo, $data) {
            $query = "UPDATE Gara
                         SET nome  = ?
                            ,data  = ?
                            ,luogo = ?
                       WHERE idGara = ?";
            return $this->pdo->prepare($query)->execute([$nome, $data, $luogo, $idGara]);
        }

        public function updateStatus($idGara, $status) {
            $query = "UPDATE Gara
                         SET stato = ?
                       WHERE idGara = ?";
            return $this->pdo->prepare($query)->execute([$status, $idGara]);
        }

        public function delete($idGara) {
            $query = "DELETE FROM Gara WHERE idGara = ?";
            return $this->pdo->prepare($query)->execute([$idGara]);
        }


        
    }

?>