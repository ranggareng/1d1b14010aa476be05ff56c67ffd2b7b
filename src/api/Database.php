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
				echo "Konek";
			}else{
				echo "Koneksi database pgsql dan php GAGAL !";
				exit();
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
			exit();
		}
	}
}

?> 