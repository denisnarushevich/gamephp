<?php
class modelSet implements IteratorAggregate {
	private $idMap;
	private $items = array();
	public $length = 0;
	
	function getIterator() {
		return new ArrayIterator($this->items);
	}
	
	function ksort(){
		ksort($this->items);
		return $this;
	}
	
	function krsort(){
		krsort($this->items);
		return $this;
	}
	
	function count(){
		return sizeof($this->items);
	}
	
	function item($key){
		if(isset($this->items[$key])){
			return $this->items[$key];
		}else{
			return null;
		}
	}
	
	function getById($id){
		if(isset($this->idMap[$id])){
			return $this->idMap[$id];
		}
		return null;		
	}
	
	function add($item){
		$this->items[] = $item;
		$this->idMap[$item->getId()] = $item;
		$this->length++;
		return $this;
	}
}
?>
