<?php
class mysqli2 extends mysqli {
	static $aac;
	public function query($query){
		if(isset(self::$aac[$query])){
			self::$aac[$query] = self::$aac[$query] + 1;
		}else{
			self::$aac[$query] = 1;
		}
		return parent::query($query, $resultmode = null);
	}
}

class db {
	static private $instance = null;
	static $n = 0;
	
	/**
	 *
	 * @return mysqli
	 */
	static function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new mysqli2('localhost', 'root', 'root', 'game');
		}
		
		db::$n++;
		
		return self::$instance;
	}
}
?>