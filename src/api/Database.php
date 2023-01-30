<?php
	
class Database{
	private $dbHost = 'db';
	private $dbName = 'devdb';
	private $dbUser = 'devuser';
	private $dbPass = 'devsecret';
	
	public function connect(){
		try {
			$conn = new PDO('pgsql:host='.$this->dbHost.';dbname='.$this->dbName, $this->dbUser, $this->dbPass, array(PDO::ATTR_PERSISTENT => true));
	 
			if($conn){
				return $conn;
			}else{
				return false;
				exit();
			}
		} catch (PDOException $e) {
			return $e->getMessage();
			exit();
		}
	}
}

?> 