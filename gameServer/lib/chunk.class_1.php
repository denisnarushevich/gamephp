<?php
class chunk {
	//it being assumed that min height is 0 and max 255.
	
	//TODO on synchronous request for same chunk, there may apper problems, like double generation of one chunk. Have to do something like DB Lock while generating. 
	//TODO to accomplish that, it is better to make chunk table, and make chunk model and so on.
	
	private static $x;
	private static $y;
	private static $len = 33;
	
	static function generate($x, $y){
		self::$x = floor($x/(self::$len-1))*(self::$len-1); //calculating "head" tile of chunk
		self::$y = floor($y/(self::$len-1))*(self::$len-1); //calculating "head" tile of chunk
		
		//very lazy check if chunk exists
		//TODO: mby it's worth to make a memory table with all chunks, that way chunks wont rewrite.
		if(tile::findByXY(self::$x+1, self::$y+1)){
			return false;
		}
		
		$tiles = self::getHeightMap();
		$tiles->save();
		
		$trees = new structureSet();
		
		foreach($tiles as $tile){
			if($plant = $tile->plantTree()){
				$plant->setCreatedAt(time()-rand(0, $plant->getReadyIn()));
				$trees->add($plant);
			}
		}

		$trees->save();
		
		return $tiles;
	}
	
	/**
	 *
	 * @return type array
	 */
	static private function getHeightMap() {
		$len = self::$len;
		$x = self::$x;
		$y = self::$y;
		
		$borderXYs = array();
		$tiles = new tileSet();
		
		for($i = $x; $i < $x+$len; $i++){
			for($j = $y; $j < $y+$len; $j++){
				if($i != $x && $i != $x+$len-1 && $j != $y && $j != $y+$len-1){ //not-border tiles
					$tiles->add(new tile(array('x' => $i, 'y' => $j))); //pustiwki v centre chunka, kotorije pozzhe budut nagenereni i zapisani v bd.
				}else{
					$borderXYs[] = array('x' => $i, 'y' => $j); 
				}
			}
		}
		
		$borderTiles = tile::findByXYs($borderXYs);
				
		foreach($borderXYs as $xy){
			if($borderTiles->getByXY($xy['x'],$xy['y'])){
				$tiles->add($borderTiles->getByXY($xy['x'],$xy['y']));
			}else{
				$tiles->add(new tile(array('x' => $xy['x'], 'y' => $xy['y'])));
			}
		}
		
		//map array will be referencing to same tiles from $tiles.
		foreach($tiles as $tile){
			$map[$tile->getX()][$tile->getY()] = $tile;
		}

		//references to four corners
		$corners['north'] = $map[$x][$y];
		$corners['south'] = $map[$x+$len-1][$y+$len-1];
		$corners['east'] = $map[$x][$y+$len-1];
		$corners['west'] = $map[$x+$len-1][$y];

		//random values for not set corners
		foreach($corners as $tile){
			if($tile->isNew()){
				$z = world::$seaLevel+rand(-$len, $len);
				$tile->setZ($z);
			}
		}
		
		//recursion
		self::heightMapRecursion($map);

		//ready
		return($tiles);
	}
	
	static private function heightMapRecursion($mapPart){
		$square = array();
		ksort($mapPart);
		foreach($mapPart as $row){
			$tiles = array();
			ksort($row);
			foreach($row as $tile){
				$tiles[] = $tile;
			}
			$square[] = $tiles;
		}

		$len = sizeof($square);

		//references to four corners
		$north = $square[0][0];
		$south = $square[$len-1][$len-1];
		$east = $square[0][$len-1];
		$west = $square[$len-1][0];
		
		//calculating square's center point
		$center = ($len-1)/2;
		$centerTile = $square[$center][$center];
		$z = round( ( $north->getZ() + $south->getZ() + $east->getZ() + $west->getZ() )/4) + rand(-($len), $len);
		$centerTile->setZ($z);
				
		//calc diamond's center point
		$ne = $square[0][$center];
		$nw = $square[$center][0];
		$se = $square[$len-1][$center];
		$sw = $square[$center][$len-1];
		
		if(!$ne->getZ()){
			$z = round(($north->getZ() + $east->getZ() + $centerTile->getZ())/3) + rand(-($len), $len);
			$ne->setZ($z);
		}

		if(!$nw->getZ()){
			$z = round(($north->getZ() + $west->getZ() + $centerTile->getZ())/3) + rand(-($len), $len);
			$nw->setZ($z);
		}
		
		if(!$se->getZ()){
			$z = round(($south->getZ() + $east->getZ() + $centerTile->getZ())/3) + rand(-($len), $len);
			$se->setZ($z);
		}
		
		if(!$sw->getZ()){
			$z = round(($south->getZ() + $west->getZ() + $centerTile->getZ())/3) + rand(-($len), $len);
			$sw->setZ($z);
		}
		
		//exit if recursion reached it's limit of 3 tiles for chunk.
		if($len<=3)return 1;
		
		//passing little chunks to recursion
		for($i = 0; $i <= ($len-1)/2; $i++){
			for($j = 0; $j <= ($len-1)/2; $j++){
				$northPart[$i][$j] = $square[$i][$j];
			}
		}
		
		for($i = 0; $i <= ($len-1)/2; $i++){
			for($j = ($len-1)/2; $j <= $len-1; $j++){
				$westPart[$i][$j] = $square[$i][$j];
			}
		}
		
		for($i = ($len-1)/2; $i <= $len-1; $i++){
			for($j = 0; $j <= ($len-1)/2; $j++){
				$eastPart[$i][$j] = $square[$i][$j];
			}
		}
		
		for($i = ($len-1)/2; $i <= $len-1; $i++){
			for($j = ($len-1)/2; $j <= $len-1; $j++){
				$southPart[$i][$j] = $square[$i][$j];
			}
		}
		
		self::heightMapRecursion($northPart);
		self::heightMapRecursion($eastPart);
		self::heightMapRecursion($southPart);
		self::heightMapRecursion($westPart);
	}
}
?>
