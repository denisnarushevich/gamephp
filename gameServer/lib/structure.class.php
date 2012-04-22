<?php
class structure extends structureModel {
	private static $classNames = array(
		0 => 'none',
		1 => 'tree1',
		2 => 'tree2',
		3 => 'tree3',
		4 => 'tree4',
		5 => 'tree5',
		7 => 'farm', //generates wheat depending on empty field count around farm
		13 => 'lumbermill', //generates wood depending on empty field count around lumbermill
		20 => 'flats1'
	);

	protected $name = 'none';
	protected $income = 0;
	protected $price = 0;
	protected $require = array(); //product_id => amount
	protected $product = array(); //product_id => amount
	protected $frameCount = 1; //number of frames
	protected $readyIn = 86400; //building time, default: 24 hours
	
	protected $tile;
	
	function isEnabled(){
		$is = ( time() - $this->getCreatedAt() ) >= $this->getReadyIn();
		return($is);
	}
	
	function getTile(){
		if(!$this->tile){
			$this->tile = tile::findById($this->tile_id);
		}
		return($this->tile);
	}	
	
	function getName(){
		return $this->name;
	}
	
	function getIncome(){
		return $this->income;
	}
	
	function getPrice(){
		return $this->price;
	}
	
	function getRequire(){
		return $this->require;
	}
	
	function getProduct(){
		return $this->product;
	}
	
	function getFrameCount(){
		return $this->frameCount;
	}
	
	function getReadyIn(){
		return $this->readyIn;
	}
	

	function getImageName(){
		$frameTime = $this->getReadyIn() / $this->getFrameCount(); //time for each one frame.
		$age = time() - $this->getCreatedAt(); //how much time passed from moment when struct was placed
		$frameNum = min( ceil($age / $frameTime), $this->getFrameCount() ); //gives actuall and existent  frame number
		
		$imageName = self::getClassName($this->getClassId()).'_'.$frameNum.'.png';
		
		return($imageName);	
	}
	
	function setTile(tile $tile){
		if($tile->isNew()){
			throw new Exception('Tile ('.$tile->getX().','.$tile->getY().') is new. Id is not set.');
		}
		
		return $this->setTileId($tile->getId());
	}
	
	function toArray(){
		$array = array(
			'id' => $this->getId(),
			'classId' => $this->getClassId(),
			'name' => $this->getName(),
			'price' => $this->getPrice(),
			'income' => $this->getIncome(),
			'product' => $this->getProduct(),
			'require' => $this->getRequire(),
			'imageName' => $this->getImageName(),
			'createdAt' => $this->getCreatedAt()
		);
		return $array;
	}
	
	static function getClassName($classId){
		if(isset(self::$classNames[$classId])){
			return self::$classNames[$classId];
		}
		return false;
	}
	
	static function getClassList(){
		return self::$classNames;
	}
	
	function save(){
		return parent:: saveOne($this);
	}
}
?>
