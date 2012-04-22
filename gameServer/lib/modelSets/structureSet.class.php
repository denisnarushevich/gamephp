<?php
class structureSet extends modelSet {
	private $mapByTileId;
	
	function add($item){
		/*if(!isset($this->mapByTileId[$item->getTileId()])){
			$this->mapByTileId[$item->getTileId()] = new structureSet();
		}
		$this->mapByTileId[$item->getTileId()]->add($item);
		*/
		parent::add($item);
	}
	
	function toArray(){
		$array = array();
		foreach($this as $structure){
			$array[] = $structure->toArray();
		}
		return $array;
	}
	
	function getByTileId($id){
		/*if($list = isset($this->mapByTileId[$id])){
			return $list;
		}else{
			return new structureSet();
		}*/
		
		
		$list = new structureSet();
		foreach($this as $item){
			if($item->getTileId() == $id){
				$list->add($item);
			}
		}
		return $list;
	}
	
	function save(){
		if(!$this->count()) return false;
		
		return structure::saveMany($this);
	}
}
?>
