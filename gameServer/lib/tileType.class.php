<?php
class tileType {
	private $id;
	private $typeNames = array(
		1 => 'water',
		2 => 'grass',
		3 => 'sand',
		4 => 'road1',
		5 => 'road2',
		6 => 'roadx',
	);
	
	function __construct($id){
		$this->id = $id;
	}
	
	function getId(){
		return($this->id);
	}
	
	function getName(){
		return($this->typeNames[$this->id]);
	}
}
?>
