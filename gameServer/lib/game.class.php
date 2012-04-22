<?php
class game {
	static $playerId;
	
	static function init($playerId){
		self::$playerId = $playerId;
		
		//routines
		//...
	}
	
	static function getTiles($params){
		if(sizeof($params)){
			$tiles = world::getTiles($params['coords'])->toArray();
			return $tiles;
		}
		return false;
	}
	
	static function buyTile($params){
		if(isset($params['x']) && isset($params['y'])){
			$x = $params['x'];
			$y = $params['y'];
			$tile = world::getTiles(array('x' => $x, 'y' => $y))->getByXY($x, $y);
			return $tile->buy();
		}
		return false;
	}
	
	static function getBuildingList($params){
		if(isset($params['x']) && isset($params['y'])){
			$x = $params['x'];
			$y = $params['y'];

			$tile = world::getTiles(array('x' => $x, 'y' => $y))->getByXY($x, $y);

			$list = $tile->getBuildingList()->toArray();

			return $list;
		}
		return false;
	}
	
	static function buildStructure($params){
		if(isset($params['x']) && isset($params['y']) && isset($params['cid'])){
			$x = $params['x'];
			$y = $params['y'];
			$classId = $params['cid'];

			$tile = world::getTiles(array(array('x' => $x, 'y' => $y)))->getByXY($x, $y);
			$className = structure::getClassName($classId);
			$building = new $className(array());
			return $tile->build($building);
		}
		return false;
	}
	
	static function getUser($params){
    if(isset($params['uid'])){
      $user = user::findById($params['uid']);
      return $user->toArray();
    }
    return false;
	}
}
?>
