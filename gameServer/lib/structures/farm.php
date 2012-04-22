<?php
class farm extends structure {
	protected $class_id = 7;
	
	protected $price = 1000;
	protected $income = 10;
	protected $product = array(
		1 => 1
	); 
	
	protected $frameCount = 1;
	protected $readyIn = 86400; //building time: 24h
	protected $name = 'farm';
}
?>
