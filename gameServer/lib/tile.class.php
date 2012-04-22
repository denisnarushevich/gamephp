<?php
class tile extends tileModel {	
	private $type;
	private $owner;
	//private $structures;
	public $structures;
	
	/**
	 *
	 * @return tileType 
	 */
	function getType(){
		if(!$this->type){
			if($this->getZ() < world::$seaLevel){
				$typeId = 1;
			}else{
				$typeId = $this->getTypeId();
			}
			
			$this->type = new tileType($typeId);
		}
		return($this->type);
	}
	
	function getOwner(){
		if(!$this->owner){
			$this->owner = user::findById($this->getOwnerId());
		}
		return $this->owner;
	}
	
	function getStructures(){
		if(!$this->structures){
			$this->structures = structure::findByTileId($this->getId());
		}
		
		return $this->structures;
	}
	
	function getNeighbors($radius = 1){
		$xys = array();
		for($x = $this->getX() - $radius; $x <= $this->getX() + $radius; $x++){
			for($y = $this->getY() - $radius; $y <= $this->getY() + $radius; $y++){
				if ($x == $this->getX() && $y == $this->getY()) continue;
				
				$xys[] = array('x'=>$x, 'y'=>$y);
			}
		}
		return tile::findByXYs($xys);
	}
	
	function getIncome(){
		$income = 0;
		foreach($this->getStructures() as $structure){
			$income += $structure->getIncome();
		}
		return $income;
	}
	
	/**
	 * Returns value of current tile.
	 * Values is calculated by summing price and income of all strucutres in current tile.
	 * @return integer
	 */
	function getValue(){
		$value = 0;
		foreach($this->getStructures() as $structure){
			$value += $structure->getPrice();
			$value += $structure->getIncome();
		}	
		return($value); //0 or value
	}
	
	/**
	 * Return price of tile.
	 * Price is calculated from value of current tile + values of all 8 neighbour tiles divided by two.
	 * @return integer 
	 */
	function getPrice(){
		$price = $this->getValue();
		foreach($this->getNeighbors() as $neighbor){
			$price += $neighbor->getValue()/2;
		}
		return($price); //0 or price
	}
	
	function getBuildingList(){
		$list = new structureSet();
		if($this->getStructures()->count() <= 0){
			foreach(structure::getClassList() as $id => $item){
				if($id > 0 && $id != 7){ //excluding "none" building
					$building = new $item(array(
						'tile_id' => $this->getId()
					));
					$list->add($building);
				}
			}
		}
		return $list;
	}
	
	function setType(tileType $type){
		$this->setTypeId($type->getId());
		$this->type = $type;
	}
	
	function setOwner(user $owner){
		$this->setOwnerId($owner->getId());
		$this->owner = $owner;
	}
	
	function setStructures(structureSet $structures){
		if(!$this->structures){
			$this->structures = $structures;
			return $this->structures;
		}else{
			return false;
		}
	}
	
	function buy(){
		$owner = $this->getOwner();
		$buyer = user::findById(game::$playerId);
		if($buyer->getId() != $owner->getId()){
			if($buyer->moveCash($owner, $this->getPrice())){
				$this->setOwner($buyer);
				
				$users = new userSet();
				$users->add($owner)->add($buyer);
				
				return ($users->save() && $this->save() ? $buyer->getUsername() : false);
			}
		}
		return false;
	}
	
	function build($structure){
		if($this->getStructures()->count() <= 0){
			if(game::$playerId == $this->getOwnerId()){
				if($this->getOwner()->subCash($structure->getPrice())){
					$this->getStructures()->add($structure);
					$structure->setTileId($this->getId());
					$structure->setCreatedAt(time());

					return $structure->save() && $this->getOwner()->save();
				}
			}
		}
		return false;
	}
	
	
	/**
	 *
	 * @return structure
	 */
	/*
	function plantTree(){
		$x = $this->getX();
		$y = $this->getY();
		$z = $this->getZ();

		$rad = pi()/180;
		
		if($z <= world::$seaLevel)return false;

		$onHills = pow($z-world::$seaLevel, 0.75)/pow(255-world::$seaLevel, 0.75);
		$sinSpots =  pow(sin($x*$rad*4), 2)*pow(sin($y*$rad*4), 2);

		$r = rand(1,10000)/10000;

		if($r <= 0.1 || $r <= $onHills || $r <= $sinSpots*0.7){
			$classId = rand(1,5); //random tree structure
			$className = structure::getClassName($classId);

			$structure = new $className(array(
				'tile_id' => $this->getId(),
				'created_at' => time()
			));

			return $structure;
		}
		
		return(false);
	}*/
	
	function toArray(){	
		$array = array(
			'id' => $this->getId(),
			'x' => $this->getX(),
			'y' => $this->getY(),
			'z' => $this->getZ(),
			'type' => $this->getType()->getName(),
			'owner' => $this->getOwner()->getUsername(),
			'value' => $this->getValue(),
			'price' => $this->getPrice(),
			'structures' => $this->getStructures()->toArray(),
		);
		return $array;
	}
	
	function save(){
		return parent::saveOne($this);
	}
}
?>
