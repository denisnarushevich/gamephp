<?php
/*
 * This class abstracts database table "Users"
 */
class userModel {
	
	static private $cache;
	
	protected $id;
	protected $username;
	protected $password;
	protected $cash;
	protected $last_profit_time;

	function __construct($properties) {
		foreach($properties as $property => $value){
			$this->$property = $value;
		}
	}
	
	function isNew(){
		return((bool) !$this->id);
	}
	
	function getId(){
		return $this->id;
	}
	
	function getUsername(){
		return($this->username);
	}
	
	function getPassword(){
		return($this->password);
	}
	
	function getCash(){
		return($this->cash);
	}
	
	function getLpt(){
		return $this->last_profit_time;
	}
	
	function setId($id){
		$this->id = $id;
	}
	
	function setUsername($name){
		return $this->username = $name;
	}
	
	function setPassword($password){
		return $this->password = $password;
	}
	
	function setCash($cash){
		return $this->cash = $cash;
	}
	
	function setLpt($last_profit_time){
		return $this->last_profit_time = $last_profit_time;
	}
	
	static function findById($id) {
		if(!isset(self::$cache)){
			self::$cache = new userSet();
		}
		
		if($model = self::$cache->getById($id)){
			return $model;
		}
		
		$db = db::getInstance();

		$q = 'SELECT * FROM users WHERE id='.$id;
		$data = $db->query($q)->fetch_array(MYSQLI_ASSOC);
		
		if($data){
			$model = new user($data);
			
			self::$cache->add($model);
			
			return $model;
		}else{
			return null;
		}
	}
	
	static function findByIds($idList){
		$models = new userSet();

		if(!isset(self::$cache)){
			self::$cache = new userSet();
		}
		
		foreach($idList as $key => $id){
			if($model = self::$cache->getById($id)){
				unset($idList[$key]);
				$models->add($model);
			}
		}
		
		if(sizeof($idList) > 0){
		
			$db = db::getInstance();

			$idIn = implode(',', $idList);

			$q = "SELECT * FROM users WHERE id IN ($idIn)";
			$usersData = $db->query($q)->fetch_all(MYSQLI_ASSOC);

			foreach($usersData as $data){
				$model = new user($data);
				$models->add($model);
				
				self::$cache->add($model);
			}
			
		}
		
		return $models;
	}
	
	static function saveOne(user $user){
		$db = db::getInstance();
		 
		if($user->isNew()){
			$q = 'INSERT INTO users (username, password, cash, last_profit_time) VALUES ("'.$user->getUsername().'", "'.$user->getPassword().'", '.$user->getCash().', '.$user->getLpt().')';
		}else{
			$q = 'UPDATE users SET cash='.$user->getCash().', last_profit_time='.$user->getLpt().' WHERE id='.$user->getId();
		}
		
		$result = $db->query($q);
		
		if($result && $user->isNew()){
			$user->setId($db->insert_id);
		}
		
		return($result);		
	}
	
	
	static function saveMany(userSet $users){
		$db = db::getInstance();
		
		$q = 'INSERT INTO users (username, password, cash, last_profit_time) VALUES ';
		
		$comma = '';
		foreach($users->ksort() as $user){
			$q .= $comma.'("'.$user->getUsername().'","'.$user->getPassword().'",'.$user->getCash().','.$user->getLpt().')';
			$comma = ',';
		}

		$q .= ' ON DUPLICATE KEY UPDATE cash=VALUES(cash), last_profit_time=VALUES(last_profit_time)';
		$result = $db->query($q);
		
		if($result){
			//setting newly generated DB id, for new tiles
			$lastInsertId = $db->query('SELECT max(id) FROM users')->fetch_row();
			$lastInsertId = $lastInsertId[0];
			
			foreach($users->krsort() as $model){
				if($model->isNew()){
					$model->setId($lastInsertId--);
				}
			}
		}
		
		return $result;
	}
}
?>
