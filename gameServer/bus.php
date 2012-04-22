<?php
include('./lib/db.php');
include('./lib/models/userModel.class.php');
include('./lib/models/tileModel.class.php');
include('./lib/models/structureModel.class.php');

include('./lib/modelSets/modelSet.class.php');
include('./lib/modelSets/userSet.class.php');
include('./lib/modelSets/tileSet.class.php');
include('./lib/modelSets/structureSet.class.php');

include('./lib/user.class.php');
include('./lib/tile.class.php');
include('./lib/structure.class.php');

include('./lib/structures/lumbermill.php');
include('./lib/structures/farm.php');
include('./lib/structures/tree.php');
include('./lib/structures/tree1.php');
include('./lib/structures/tree2.php');
include('./lib/structures/tree3.php');
include('./lib/structures/tree4.php');
include('./lib/structures/tree5.php');
include('./lib/structures/flats1.php');

include('./lib/tileType.class.php');
include('./lib/simplex.class.php');
include('./lib/chunk.class.php');
include('./lib/world.class.php');
include('./lib/game.class.php');

$outputData = NULL;

if(isset($_REQUEST['json_request'])){
	$request = json_decode($_REQUEST['json_request'], TRUE); 

	if(isset($request['pid'], $request['action'], $request['params'])){
		game::init($request['pid']); //game initialization, routines.
		
		if(in_array($request['action'], get_class_methods('game'))){ //if requested action exists
			$methodName = $request['action'];
			$outputData = game::$methodName($request['params']); 
		}
	}
}

print json_encode($outputData);
//var_dump(db::$n);
//var_dump(mysqli2::$aac);
?>
