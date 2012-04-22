<?php
class userSet extends modelSet {
	function toArray(){
		$array = array();
		foreach($this as $user){
			$array[] = $user->toArray();
		}
		return $array;
	}
	
	function save(){
		if (!$this->count()) return false;

		return user::saveMany($this);
	}
}
?>
