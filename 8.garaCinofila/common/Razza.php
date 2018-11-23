<?php
require_once("Common.php");

    Class RazzaBean{
        public $refAltGarrese;
        public $refAltCoscia;
        public $idCategoria;
        public $nomeRazza;
        public $idRazza;
        public $refPeso;

        public function __construct($razza){
            $this->refAltGarrese = $razza['refAltGarrese'];
            $this->refAltCoscia  = $razza['refAltCoscia' ];
            $this->idCategoria   = $razza['idCategoria'  ];
            $this->nomeRazza     = $razza['nomeRazza'    ];
            $this->idRazza       = $razza['idRazza'      ];
            $this->refPeso       = $razza['refPeso'      ];
        }

    }

    Class Razze{
        public $pdo;

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function getRazza($idRazza) {
            $query = "SELECT refAltGarrese
                            ,refAltCoscia
                            ,nomeRazza
                            ,idCategoria
                            ,idRazza
                            ,refPeso
                        FROM Razza
                       WHERE idRazza = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idRazza]);
            $result = $stmt->fetch();
            return json_encode(new RazzaBean($result));
        }

    }

?>