<?php
/*
 * This class abstracts database table "Structures".
 */
class structureModel {
	
	static private $cache;
	
	protected $id;
	protected $class_id;
	protected $tile_id;
	protected $created_at;
	
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
	
	function getClassId(){
		return $this->class_id;
	}
	
	function getTileId(){
		return $this->tile_id;
	}
	
	function getCreatedAt(){
		return $this->created_at;
	}
	
	function setId($id){
		$this->id = $id;
		return $this;
	}

	function setClassId($id){
		$this->class_id = $id;
		return $this;
	}
	
	function setTileId($id){
		$this->tile_id = $id;
		return $this;
	}
	
	function setCreatedAt($timestamp){
		$this->created_at = $timestamp;
		return $this;
	}
	
	static function findById($id) {
		if(!isset(self::$cache)){
			self::$cache = new structureSet();
		}
		
		if($model = self::$cache->getById($id)){
			return $model;
		}
		
		$db = db::getInstance();

		$q = 'SELECT * FROM structures WHERE id='.$id;
		$data = $db->query($q)->fetch_array(MYSQLI_ASSOC);
		
		if($data){
			$classname = structure::getClassName($data['class_id']);
			$model = new $classname($data);
			
			self::$cache->add($model);
			
			return $model;
		}else{
			return null;
		}
	}
	
	static function findByTileId($id){
		$models = new structureSet();
		
		if(!isset(self::$cache)){
			self::$cache = new structureSet();
		}
				
		$db = db::getInstance();
		
		$q = 'SELECT * FROM structures WHERE tile_id = '.$id;
		$rows = $db->query($q)->fetch_all(MYSQLI_ASSOC);
		
		foreach($rows as $data){
			$classname = structure::getClassName($data['class_id']);
			$model = new $classname($data);
			$models->add($model);
			
			self::$cache->add($model);
		}
		
		return $models;
	}
	
	static function findByTileIds($idList){
		$models = new structureSet();
		
		if(!isset(self::$cache)){
			self::$cache = new structureSet();
		}
		
		foreach(self::$cache as $model){
			foreach($idList as $key => $id){
				if($model->getTileId() == $id){
					unset($idList[$key]);
					$models->add($model);
				}
			}
		}
		
		if(sizeof($idList) > 0){
			
			$db = db::getInstance();

			$idIn = implode(',', $idList);

			$q = "SELECT * FROM structures WHERE tile_id IN ($idIn)";
			$structuresData = $db->query($q)->fetch_all(MYSQLI_ASSOC);

			foreach($structuresData as $data){
				$classname = structure::getClassName($data['class_id']);
				$model = new $classname($data);
				$models->add($model);
				
				self::$cache->add($model);
			}
			
		}
		
		return $models;
	}
	
	static function saveOne(structure $structure){
		$db = db::getInstance();
		 
		if($structure->isNew()){
			$q = 'INSERT INTO structures (tile_id, class_id, created_at) VALUES ('.$structure->getTileId().','.$structure->getClassId().','.$structure->getCreatedAt().')';
		}else{
			$q = 'UPDATE structures SET tile_id='.$structure->getTileId().', class_id='.$structure->getClassId().', created_at='.$structure->getCreatedAt().' WHERE id='.$structure->getId();
		}
		
		$result = $db->query($q);
		
		if($result && $structure->isNew()){
			$structure->setId($db->insert_id);
		}
		
		return($result);		
	}
	
	static function saveMany(structureSet $structures){
		$db = db::getInstance();
		
		$q = 'INSERT INTO structures (tile_id, class_id, created_at) VALUES ';
		
		$comma = '';
		foreach($structures->ksort() as $structure){
			$q .= $comma.'('.$structure->getTileId().','.$structure->getClassId().','.$structure->getCreatedAt().')';
			$comma = ',';
		}

		$q .= ' ON DUPLICATE KEY UPDATE tile_id=VALUES(tile_id), class_id=VALUES(class_id), created_at=VALUES(created_at)';
		
		$result = $db->query($q);
		
		if($result){
			//setting newly generated DB id, for new tiles
			$lastInsertId = $db->query('SELECT max(id) FROM structures')->fetch_row();
			$lastInsertId = $lastInsertId[0];
			
			foreach($structures->krsort() as $model){
				if($model->isNew()){
					$model->setId($lastInsertId--);
				}
			}
		}
		
		return $result;
	}
}
?>
