<?php
class tileSet extends modelSet {
	private $tilesXYMap;
	
	function toArray(){
		$array = array();
		foreach($this as $tile){
			$array[] = $tile->toArray();
		}
		return $array;
	}
	
	function add($tile){
		$this->tilesXYMap[$tile->getX()][$tile->getY()] = $tile;
		return parent::add($tile);
	}
	
	/**
	 *
	 * @param integer $x
	 * @param integer $y
	 * @return tile 
	 */
	function getByXY($x, $y){
		if(isset($this->tilesXYMap[$x][$y])){
			return $this->tilesXYMap[$x][$y];
		}
		return null;
	}
	
	function save(){
		if (!$this->count()) return false;
		
		return tile::saveMany($this);
	}
}
?>
