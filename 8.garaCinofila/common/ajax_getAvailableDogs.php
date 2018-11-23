<?php
    function wrapContent($resultsDogs) {
        $resultWrapped = "";
        if(sizeof($resultsDogs) > 0){
            foreach($resultsDogs as $cane){
                $idTemp = $cane['numeroChip'];
                $resultWrapped .= "
                    <div class='card' id='".$idTemp."' onclick='chooseDog(".$idTemp.")'>
                        <div class='darken'></div>
                        <span class='nome'>".$cane['nome']."</span>
                    </div>
                ";
            }
        }else $resultWrapped = "Nessun cane disponibile";
        return $resultWrapped;
    }

    require_once("Common.php");
    require_once("Cane.php");
    
    //establish a connection to DBserver
    $pdo = Common::createPDO();
    
    $cani = new Cani($pdo);
    
    $username = $_POST['username'];
    $idGara   = $_POST['idGara'  ];
    
    $resultWrapped = wrapContent(json_decode($cani->getMyDogs($username, $idGara), true));
    
    echo $resultWrapped;
    
    //closes pdo connection
    $pdo = null;
?>