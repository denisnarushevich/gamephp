tileCollection = Object();
	/*
	 *	Local collection of all tiles(instances), cache
	 */
	tileCollection.tiles = new Array();
	
	tileCollection.hasTile = function(x, y){
		if(tileCollection.tiles[x] == undefined){
			return(false);
		}else if(tileCollection.tiles[x][y] == undefined){
			return(false);
		}else{
			return(true);
		}
	}	
	
	tileCollection.setTile = function(x, y, tileInstance){
		if(tileCollection.tiles[x] == undefined){
			tileCollection.tiles[x] = new Array();
		}
		
		return(tileCollection.tiles[x][y] = tileInstance);
	}
	
	tileCollection.getTile = function(x, y){
		var tileInstance;
		
		if(tileCollection.hasTile(x, y)){
			tileInstance = tileCollection.tiles[x][y];
		}else{
			tileInstance = tileCollection.setTile(x, y, new tile(x, y));
			
			action.getTile(x, y, function(data){
				var instance = tileCollection.getTile(x, y);
				//instance.loaded = 1;
				instance.setTileData(data);
				instance.refreshProjections();
			});
		}
		
		return(tileInstance);
	}
	
	tileCollection.getTiles = function(tilesList){
		var tilesCoordPairs = new Array();
		var instance;
		var instances = new Array();
		
		for(var key in tilesList){
			var tileData = tilesList[key];
			var x = tileData['offsetX']+map.x;
			var y = tileData['offsetY']+map.y;
			
			if(!tileCollection.hasTile(x, y)){
				tilesCoordPairs.push({'x': x, 'y': y});
				instance = tileCollection.setTile(x, y, new tile(x, y));
			}else{
				instance = tileCollection.getTile(x, y);
			}
			
			instances.push(instance);
		}
		
		//get them from server and fill in instances
		if(tilesCoordPairs.length > 0){
			action.getTiles(tilesCoordPairs, function(data){
				for(var key in data){
					var tile = data[key];

					var instance = tileCollection.getTile(tile['x'], tile['y']);
					//instance.loaded = 1;
					instance.setTileData(data[key]);
					instance.refreshProjections();
				}
			});
		}
		
		return(instances);
	}