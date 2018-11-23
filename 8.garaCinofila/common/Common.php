<?php
require_once("PDOConnection.php");
//number of results to be displated on a page

Class Common{

	//Definisce i numeri identificativi di ogni tipo di credenziale(che poi verranno salvati nel DB)
	public static $USER_PROPRIETARIO = 7;
	public static $USER_MANAGER 	 = 2;
	public static $USER_GIUDICE 	 = 5;
	/* 
	public static $dbGiudiceNnEl  = "GiudiceNonElettronico";
	public static $dbCredenziale  = "Credenziale"		   ;
	public static $dbProprietario = "Proprietario"		   ;
	public static $dbCandidato 	  = "Candidato"			   ;
	public static $dbVotazione    = "Vatazione"			   ;
	public static $dbManager 	  = "Manager"			   ;
	public static $dbGiudice 	  = "Giudice"			   ;
	public static $dbRazza		  = "Razza"				   ;
	public static $dbCane 		  = "Cane"				   ;
	public static $dbGara 		  = "Gara"				   ; */

	public static $DBNAME     = "gara_cinofila";
	public static $DBSERVER   = "localhost"    ;
	public static $DBUSERNAME = "root"	       ;
	public static $DBPASSWORD = ""		       ;

	static public $RESULTNUMBER = 30;
	
	public static function createPDO() {		//creates a pdo connection
		$pdo = new MyPDOConnection();
		return $pdo->connect();
	}

	public static function convDate($date){		//converts a yyyy/mm/dd format to text
		$parts = array();
		$parts = explode("-", $date);
		
		switch($parts[1]){
		case '01':
			$month = "Jan";
			break;
		case '02':
			$month = "Feb";
			break;
		case '03':
			$month = "Mar";
			break;
		case '04':
			$month = "Apr";
			break;
		case '05':
			$month = "May";
			break;
		case '06':
			$month = "Jun";
			break;
		case '07':
			$month = "Jul";
			break;
		case '08':
			$month = "Aug";
			break;
		case '09':
			$month = "Sep";
			break;
		case '10':
			$month = "Oct";
			break;
		case '11':
			$month = "Nov";
			break;
		case '12':
			$month = "Dec";
			break;
		}
		$result = $parts[2]." ".$month." ".$parts[0];
		return $result;
	}

	public static function convHour($hour){			//converts 24h to 12h
		if($hour<13) $type = "AM";
		else {
			$type = "PM";
			$hour -= 12;
		}
		return $hour." ".$type;

	}


}
?>