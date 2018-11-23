<?php
require_once("Common.php");

Class MyPDOConnection
{
	private $serverName ;
	private $dbName     ;
	private $username   ;
	private $password   ;
	private $charset	;
	private $dsn	 	;
	private $opt	 	;

	public function __construct() {
		$this->serverName = Common::$DBSERVER  ;
		$this->dbName     = Common::$DBNAME    ;
		$this->username   = Common::$DBUSERNAME;
		$this->password   = Common::$DBPASSWORD;
		$this->charset	= 'utf8mb4';

		$this->dsn = "mysql:host=$this->serverName;dbname=$this->dbName;charset=$this->charset";
		$this->opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_SILENT,	//ERRMODE_EXCEPTION
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
	}
	
	public function connect() {
		try{
			$pdo = new PDO($this->dsn, $this->username, $this->password, $this->opt);
		}
		catch (PDOException $err) {  
			echo "harmless error message if the connection fails";
			$err->getMessage() . "<br/>";
			die();
		}
		return $pdo;
	}
}
?>