<?php

Class Db{

	protected static $connection;

	public function connect() { 
		if(!isset(self::$connection)) { 
			$config = parse_ini_file('config.ini'); 
			 self::$connection = new mysqli($config['host'],$config['username'],$config['password'],$config['dbname']);
		}

		if(self::$connection === false) {
	        return mysqli_connect_error(); 
	    }
	    return self::$connection;
	}


	public function query($query) {
		$connection = $this -> connect();
		$result = $connection -> query($query);
		return $result;
	}

	public function select($query) { 
		$rows = array();
		$result = $this -> query($query);
		if($result === false) {
			return false;
		}
		while ($row = $result -> fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}

	public function escape_string($value) {
		$connection = $this -> connect();
		return "'" . $connection -> real_escape_string($value) . "'";
	}

}
/*
$db = new Dd(); 
$connection = $db->connect();
$res = $db->select("SELECT * FROM user_tbl");

echo '<pre>'; print_r($res); exit;
*/