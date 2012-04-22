<?php
/*
 * This class abstracts database table "Tiles".
 */
class tileModel {
	
	static private $cache;
	
	protected $id;
	protected $x;
	protected $y;
	protected $z;
	protected $owner_id = 1; //default: goverment
	protected $type_id = 2; //default: grass
	
	//protected $cache; //caching object, have to do it later, but anyway it's not needed, because data is being updated frequently
	
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
	
	function getX(){
		return($this->x);
	}
	
	function getY(){
		return($this->y);
	}
	
	function getZ(){
		return($this->z);
	}
	
	function getTypeId(){
		return $this->type_id;
	}
	
	function getOwnerId(){
		return $this->owner_id;
	}
	
	function setId($id){
		$this->id = $id;
		return $this;
	}
	
	function setX($x){
		$this->x = $x;
		return $this;
	}
	
	function setY($y){
		$this->y = $y;
		return $this;
	}
	
	function setZ($z){
		$this->z = $z;
		return $this;
	}
	
	function setTypeId($id){
		$this->type_id = $id;
		return $this;
	}
	
	function setOwnerId($id){
		$this->owner_id = $id;
		return $this;
	}
	
	static function findById($id) {
		if(!isset(self::$cache)){
			self::$cache = new tileSet();
		}
		
		if($model = self::$cache->getById($id)){
			return $model;
		}
		
		$db = db::getInstance();

		$q = 'SELECT * FROM tiles WHERE id='.$id;
		$data = $db->query($q)->fetch_array();
		
		if($data){
			$model = new tile($data);
			
			self::$cache->add($model);
			
			return $model;
		}else{
			return null;
		}
	}
	
	static function findByIds($idList){
		$models = new tileSet();
		
		if(!isset(self::$cache)){
			self::$cache = new tileSet();
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

			$q = "SELECT * FROM tiles WHERE id IN ($idIn)";
			$tilesData = $db->query($q)->fetch_all(MYSQLI_ASSOC);

			foreach($tilesData as $data){
				$model = new tile($data);
				$models->add($model);
				
				self::$cache->add($model);
			}
			
		}
		
		return $models;
	}
	
	static function findByXY($x, $y) {
		if(!isset(self::$cache)){
			self::$cache = new tileSet();
		}

		if($model = self::$cache->getByXY($x, $y)){
			return $model;
		}
		
		$db = db::getInstance();

		$q = 'SELECT * FROM tiles WHERE x = '.$x.' AND y = '.$y;
		$data = $db->query($q)->fetch_array(MYSQLI_ASSOC);
		
		if($data){
			$model = new tile($data);
			
			self::$cache->add($model);
			
			return $model;
		}else{
			return null;
		}
	}
	
	static function findByXYs($xyList){	
		$models = new tileSet();
		
		if(!isset(self::$cache)){
			self::$cache = new tileSet();
		}
		
		foreach($xyList as $key => $xy){
			if($model = self::$cache->getByXY($xy['x'], $xy['y'])){
				unset($xyList[$key]);
				$models->add($model);
			}
		}

		if(sizeof($xyList) > 0){
		
			$db = db::getInstance();

			$xs = array();
			$ys = array();

			foreach($xyList as $key => $xy){
				$xs[] = $xy['x'];
				$ys[] = $xy['y'];
			}

			$xs = array_unique($xs);
			$ys = array_unique($ys);

			$xIn = implode(',', $xs);
			$yIn = implode(',', $ys);

			$q = "SELECT * FROM tiles WHERE x IN ($xIn) AND y IN ($yIn)";

			$tilesData = $db->query($q)->fetch_all(MYSQLI_ASSOC); //NOTICE that this result may contain even tiles, which were not requested, because data is being fetched by bloks.

			foreach($tilesData as $data){
				if(in_array(array('x' => $data['x'], 'y' => $data['y']), $xyList)){
					$model = new tile($data);
					$models->add($model);
					
					self::$cache->add($model);
				}
			}
		}
		
		return $models;
	}
	
	static function findByOwnerId($id){
		$models = new tileSet();
		
		if(!isset(self::$cache)){
			self::$cache = new tileSet();
		}
		
		$db = db::getInstance();
		
		$q = 'SELECT * FROM tiles WHERE owner_id = '.$id;
		$rows = $db->query($q)->fetch_all(MYSQLI_ASSOC);
		
		foreach($rows as $data){
			$model = new tile($data);
			$models->add($model);
			
			self::$cache->add($model);
		}
		
		return $models;
	}
	
	static function saveOne(tile $tile){
		$db = db::getInstance();
		 
		if($tile->isNew()){
			$q = 'INSERT INTO tiles (x, y, z, owner_id, type_id) VALUES ('.$tile->getX().', '.$tile->getY().', '.$tile->getZ().', '.$tile->getOwnerId().', '.$tile->getTypeId().')';
		}else{
			$q = 'UPDATE tiles SET z='.$tile->getZ().', owner_id='.$tile->getOwnerId().', type_id='.$tile->getTypeId().' WHERE id='.$tile->getId();
		}

		$result = $db->query($q);
		
		if($result && $tile->isNew()){
			$tile->setId($db->insert_id);
		}
		
		return($result);			
	}
	
	static function saveMany(tileSet $tiles){
		$db = db::getInstance();
		
		$q = 'INSERT INTO tiles (x, y, z, owner_id, type_id) VALUES ';
		
		$comma = '';
		foreach($tiles->ksort() as $tile){
			$q .= $comma.'('.$tile->getX().','.$tile->getY().','.$tile->getZ().','.$tile->getOwnerId().','.$tile->getTypeId().')';
			$comma = ',';
		}

		$q .= ' ON DUPLICATE KEY UPDATE z=VALUES(z), owner_id=VALUES(owner_id), type_id=VALUES(type_id)';
		
		$result = $db->query($q);
		
		if($result){
			//setting newly generated DB id, for new tiles
			$lastInsertId = $db->query('SELECT max(id) FROM tiles')->fetch_row();
			$lastInsertId = $lastInsertId[0];
			
			foreach($tiles->krsort() as $model){
				if($model->isNew()){
					$model->setId($lastInsertId--);
				}
			}
		}
		
		return $result;
	}
}
?>
