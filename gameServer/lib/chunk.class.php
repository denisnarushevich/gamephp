<?php
class chunk {
	//it being assumed that min height is 0 and max 255.
	
	//TODO on synchronous request for same chunk, there may apper problems, like double generation of one chunk. Have to do something like DB Lock while generating. 
	//TODO to accomplish that, it is better to make chunk table, and make chunk model and so on.
	
	private static $x;
	private static $y;
	private static $len = 32;
	
	static function generate($x, $y){
		self::$x = floor($x/self::$len)*self::$len; //calculating "head" tile of chunk
		self::$y = floor($y/self::$len)*self::$len; //calculating "head" tile of chunk
		
		//very lazy check if chunk exists
		//TODO: mby it's worth to make a memory table with all chunks, that way chunks wont rewrite.
		if(tile::findByXY(self::$x, self::$y)){
			return false;
		}
		
		$tiles = self::getHeightMap();
		$tiles->save();
		
		
		$trees = new structureSet();
		
		foreach($tiles as $tile){
			if($plant = self::plantTree($tile)){
				$trees->add($plant);
			}
		}

		$trees->save();
		
		return $tiles;
	}
	
	/**
	 *
	 * @return tileSet
	 */
	static private function getHeightMap() {
		$tiles = new tileSet();
		
		for($i = self::$x; $i < self::$x + self::$len; $i++){
			for($j = self::$y; $j < self::$y + self::$len; $j++){
				$s1 = simplex::noise2d($i/512, $j/512); //noisemap of continets
				$s2 = simplex::noise2d($i/256, $j/256); //of smaler lands
				$s3 = simplex::noise2d($i/128, $j/128);  //...
				$s4 = simplex::noise2d($i/64, $j/64); //islands sizes
				$s5 = simplex::noise2d($i/32, $j/32); //...
				$s6 = simplex::noise2d($i/16, $j/16); //...
				$s7 = simplex::noise2d($i/8, $j/8); //small details
				
				$mainland = $s1*0.5 + $s2*0.25 + $s3*0.125 + $s4*0.0625 + $s5*0.03125 + $s6*0.015625 + $s7*0.015625; //weights is sum should be = 1;
				$islands = $s4*0.5 + $s5*0.25 + $s6*0.125 + $s7*0.125; //weights sum should be = 1;
				$z = 128+127*($mainland*0.8 + $islands*0.2);
				
				$tile = new tile(array(
						'x' => $i,
						'y' => $j,
						'z' => $z
				));
				
				$tiles->add($tile);
			}
		}
		
		return $tiles;	
	}
	
	static private function plantTree(tile $tile) {
		$x = $tile->getX();
		$y = $tile->getY();
		
		if($tile->getType()->getId() == 1) return false;
		
		$s5 = simplex::noise2d($x/32, $y/32); //forest chunks
		$s6 = simplex::noise2d($x/16, $y/16); //...
		$s7 = simplex::noise2d($x/8, $y/8); //small details
		$s8 = simplex::noise2d($x/4, $y/4); //small details
		$s9 = simplex::noise2d($x, $y); //white noise
		
		$forests = ($s5*0.5 + $s6*0.25 + $s7*0.125 + $s8*0.125) - $s9;

		if($forests > 0){
			$trees = new structureSet();

			$classId = rand(1,5); //random tree structure
			$className = structure::getClassName($classId);

			$tree = new $className(array(
					'tile_id' => $tile->getId()
			));
			$tree->setCreatedAt(time()-rand(0, $tree->getReadyIn()));

			$trees->add($tree);

			$tile->setStructures($trees);
			
			return $tree;
		}
		return false;
	}
}
?>
