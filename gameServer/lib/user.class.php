<?php
class user extends userModel {
	function getTiles(){
		return tile::findByOwnerId($this->id);
	}
	
	function subCash($amount){
		if($this->getCash() >= $amount){
			return $this->setCash($this->getCash() - $amount);
		}
		
		return false;
	}
	
	function addCash($amount){
		return $this->setCash($this->getCash()+$amount);
	}
	
	function moveCash(user $to, $amount){
		if($to->getId() != $this->getId()){
			if($this->subCash($amount)){
				$to->addCash($amount);
				return true;
			}
		}
		return false;
	}
	
	function toArray(){
		$array = array(
			'id' => $this->getId(),
			'username' => $this->getUsername(),
			'cash' => $this->getCash()
		);
		return $array;
	}
	
	function save(){
		return parent::saveOne($this);		
	}
}
?>
