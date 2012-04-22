<?php
class farmland implements structure {
	private $link;
	private $income;
	private $requesting;
	private $producing;
	private $r_amount; //request amount
	private $p_amount; //production amount
	private $imageName;
	static private $price;
	
	function __construct($sid) {
		$db = new db();
		$this->link = $db->GetLink();
		
		$this->income = 0;
		$this->requesting = null;
		$this->producing = null;
		$this->r_amount = 0;
		$this->p_amount = 0;
		$this->imageName = 'farmland.png';
		self::$price = 100;
	}
	
	function GetIncome(){
		return($this->income);
	}
	
	function GetRequesting(){
		return($this->requesting);
	}
	
	function GetProducing(){
		return($this->producing);
	}
	
	function GetP_amount(){
		return($this->p_amount);
	}
	
	function GetR_amount(){
		return($this->r_amount);
	}
	
	static function GetPrice() {
		return(self::$price);
	}
	
	public function Out(){
		$out['income'] = $this->income;
		$out['requesting'] = $this->requesting;
		$out['producing'] = $this->producing;
		$out['p_amount'] = $this->p_amount;
		$out['r_amount'] = $this->r_amount;
		$out['imageName'] = $this->imageName;
		$out['price'] = self::$price;
		
		return($out);
	}
}
?>
