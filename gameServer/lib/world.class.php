<?php
class world {
	static $seaLevel = 127;
	
	/**
	 *
	 * @param array $tileCoordinatesList
	 * @return tileSet
	 */
	static function getTiles($tileCoordinatesList){
		if(!is_array(reset($tileCoordinatesList))){
			$tileCoordinatesList = array($tileCoordinatesList);
		}
		
		$tiles = tile::findByXYs($tileCoordinatesList);
		
		//mass load of structures for tiles
		$idList = array();
		foreach($tiles as $tile){
			if(!$tile->structures){
				$idList[] = $tile->getId();
			}
		}
		$trees = structure::findByTileIds($idList);
		foreach($tiles as $tile){
			if(!$tile->structures){
				$tile->setStructures($trees->getByTileId($tile->getId()));
			}
		}
		
		//create chunk
		foreach($tileCoordinatesList as $coord){
			if(!$tiles->getByXY($coord['x'], $coord['y'])){
				chunk::generate($coord['x'], $coord['y']);
			}
		}
		
		$tiles = tile::findByXYs($tileCoordinatesList);
		
		return $tiles;
	}
}
?>
