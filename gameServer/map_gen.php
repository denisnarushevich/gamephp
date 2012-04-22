<?php
ignore_user_abort(); // продолжать выполнение скрипта после закрытия браузера - скрипт работает в background режиме
set_time_limit(0); // убираем ограничение по времени выполнение скрипта
ini_set("memory_limit","1024M");

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
include('./lib/structures/tree.php');
include('./lib/structures/tree1.php');
include('./lib/structures/tree2.php');
include('./lib/structures/tree3.php');
include('./lib/structures/tree4.php');
include('./lib/structures/tree5.php');

include('./lib/tileType.class.php');
include('./lib/chunk.class.php');
include('./lib/world.class.php');
include('./lib/game.class.php');


$t1 = microtime(1);



$db = db::getInstance();
$q = 'select max(x) as maxx, max(y) as maxy, min(x) as minx, min(y) as miny from tiles';
$sizes = $db->query($q)->fetch_all(MYSQLI_ASSOC);
$sizes = $sizes[0];
$im = imagecreatetruecolor($sizes['maxx']+abs($sizes['minx']),$sizes['maxy']+abs($sizes['miny']));
$tiles = new tileSet();
$q = 'select  id,x,y,z from tiles';
$datas = $db->query($q)->fetch_all(MYSQLI_ASSOC);
foreach($datas as $data){
	$tiles->add(new tile($data));
}
		/*
		$trees = new structureSet();
		$q = 'select id,tile_id,class_id from structures';
		$datas = $db->query($q)->fetch_all(MYSQLI_ASSOC);
		foreach($datas as $data){
			$trees->add(new structure($data));
		}
		foreach($tiles as $tile){
			if(!$tile->structures){
				$tile->structures = $trees->getByTileId($tile->getId());
			}
		}*/
		$q = 'select id,tile_id,class_id from structures';
		$datas = $db->query($q)->fetch_all(MYSQLI_ASSOC);
		foreach($datas as $data){
			$s = new structure($data);
			$tid = $s->getTileId();
			$tile = $tiles->getById($tid);
			$tile->setStructures(new structureSet());
			$tile->getStructures()->add($s);
		}
//die();
$t2 = microtime(1);
$t=$t2-$t1;
echo "Fetch time: $t";
$t1 = microtime(1);
		foreach ($tiles as $t){
			$x = $t->getX()+abs($sizes['minx']);
			$y = $t->getY()+abs($sizes['miny']);
			$z = $t->getZ();

				if($z>127){
					$r = 0;
					$g = $z;
					$b = 0;
				}else{
					$r = 0;
					$g = 0;
					$b = $z*2-1;
				}

				$s = $t->getStructures();
				if($s = $s->item(0)){
					$cid = $s->getClassId();
					if($cid < 5){
						$r = 0;
						$g = 100;
						$b = 0;
					}

					/*if($cid <= 5 and $t->getTypeId() == 1){
						$r = 255;
						$g = 0;
						$b = 0;
					}*/
				}

			imagesetpixel($im, $x, $y, imagecolorallocate($im, $r, $g, $b));	
		}






imagesetpixel($im, abs($sizes['minx']), abs($sizes['miny']), imagecolorallocate($im, 255, 0, 0));
imagesetpixel($im, abs($sizes['minx']+1), abs($sizes['miny']), imagecolorallocate($im, 255, 0, 0));
imagesetpixel($im, abs($sizes['minx']-1), abs($sizes['miny']), imagecolorallocate($im, 255, 0, 0));
imagesetpixel($im, abs($sizes['minx']), abs($sizes['miny']+1), imagecolorallocate($im, 255, 0, 0));
imagesetpixel($im, abs($sizes['minx']), abs($sizes['miny']-1), imagecolorallocate($im, 255, 0, 0));

imagepng($im, 'map.png');	



$t2 = microtime(1);
$t=$t2-$t1;
echo "Draw time: $t";
?>
