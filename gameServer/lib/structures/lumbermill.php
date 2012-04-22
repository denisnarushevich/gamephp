<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lumbermill
 *
 * @author Waynez
 */
class lumbermill extends structure {
	protected $class_id = 13;
	
	protected $price = 1000;
	protected $income = 0;
	protected $product = array(
		2 => 0
	); 
	
	protected $frameCount = 5;
	protected $readyIn = 86400; //building time: 24h
	protected $name = 'lumbermill';
	
	function getProduct(){
		//get current tile
		$t = $this->getTile();
		
		//foreach neighbour structure
		foreach($t->getNeighbors() as $key => $n){
			$rs = $n->getStructures();
			$rs = $rs->item(0);
			
			if($rs && in_array($rs->getClassId(), array(1, 2, 3, 4, 5))){ //check if id is Tree(_1,_2...)
				$this->product[2] += 1;
			}
		}
		
		return(parent::getProduct());
	}
}

?>
