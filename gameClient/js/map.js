map = new Object();
	map.tileWidth = 194;
	map.tileHeight = 98;
	map.undergroundHeight = 10;
	map.airHeight = 158;
	
	map.rootNode;
	map.parentNode;
	
	map.tileList = new Array();
	
	map.width = 0;
	map.height = 0;
	
	map.x = 0;
	map.y = 0;

	/*	
	 *	Function Init() creates matrix of empty tiles and calculates absolute positioning coordinates of 
	 *	each tile.
	 *	Param "parent" (type string) - element  id where map will be appended.
	 */	
	map.init = function(parentNode){
		map.parentNode = parentNode;
		
		map.width = Math.ceil( (map.parentNode.offsetWidth + 2) / (map.tileWidth + 2) ); //2 is space of 2px between each  horizontaly relative tile
		map.height = Math.ceil( map.parentNode.offsetHeight / map.tileHeight ) + 1; //1 is to fill empty "half-diamond" places
		
		if(! (map.width % 2)){
			map.width++
		}
		
		if(! (map.height % 2)){
			map.height++
		}
		
		var x = -(map.width+map.height-2)/2; //coordinates of very first (top left) tile
		var y = (map.height-map.width)/2; //coordinates of very first (top left) tile
		
		for(var i = 0; i < map.height+map.height-1; i++){
			var top = i * (map.tileHeight / 2) - map.airHeight - map.tileHeight/2;
			
			for(var j = 0; j < ( map.width + 1 * (i % 2) ); j++){
				var left = j * (map.tileWidth+2) - (map.tileWidth+2)/2 * (i % 2);
				
				var tileData = new Array();
					tileData['offsetX'] = x+j;
					tileData['offsetY'] = y+j;
					tileData['top'] = top;
					tileData['left'] = left;
					tileData['tileInstance'] = null;
					tileData['projectionIndex'] = null;
					
				map.tileList.push(tileData);				
			}
			
			if( i % 2 ){ //if true than next row is "short", and x should decrease
				x++;
			}else{
				y--;
			}
		}
		
		map.rootNode = document.createElement('div');
			map.rootNode.id = 'map';
			map.rootNode.style.width = map.width*(map.tileWidth+2)-2+'px'; //2 is "horizontal gap" between tiles
			map.rootNode.style.height = (map.height-1)*map.tileHeight+'px';
			map.rootNode.style.position = 'relative';
			//map.rootNode.style.margin = '0 auto';
			map.rootNode.style.overflow = 'hidden';
			map.rootNode.style.backgroundColor = '#41390f';
		
		map.parentNode.appendChild(map.rootNode);
		
		return(map);
	}
	
	/*
	 *  move map 
	 */
	map.move = function(x, y){
		map.x = x;
		map.y = y;
		map.fill(false); //false to not to load data, data will be loaded separatelly on mousekeyup
		map.draw();
	}
	
	/*
	 *	Function fill() fill empty cells with data - tiles of given coordinates.
	 *	Param "load" - to fetch data from server or not to. 
	 *	If false, the tile is empty(grey), and without cachind. It's used only on map scroll, when you 
	 *	don't need to load tiles on every mouse movement, but only on mousekeyup.
	 */
	map.fill = function(load){
		if(load == undefined){
			load = true;
		}
		
		map.unfill();
		
		if(load){
			var tiles = tileCollection.getTiles(map.tileList);
		}

		for(var key in map.tileList){
			var tileData = map.tileList[key];
			
			var x = tileData['offsetX']+map.x;
			var y = tileData['offsetY']+map.y;

			if(tileCollection.hasTile(x, y) || load){
				var tileInstance =  tileCollection.getTile(x, y);
			}else{
				var tileInstance =  new tile(x, y);
			}
			
			tileData['tileInstance'] = tileInstance;
			tileData['projectionIndex'] = tileInstance.createProjection();
		}
		
		return(map);
	}
	
	/*
	 *	Reverse of fill()
	 */
	map.unfill = function(){
		for(var key in map.tileList){
			var tileData = map.tileList[key];
			
			if(tileData['tileInstance']){
				var tile = tileData['tileInstance'];
				var index = tileData['projectionIndex'];
				
				tile.deleteProjection(index);
				
				tileData['tileInstance'] = null;
			}
		}
		
		return(map);		
	}
	
	/*
	 *	Just shows all filled tiles. (Appends tile nodes in the map rootNode)
	 */
	map.draw = function(){
		if(map.rootNode.childNodes.length){
			map.flushScreen();
		}
		
		for(var key in map.tileList){
			var tileData = map.tileList[key];
			
			var tile = tileData['tileInstance'];
			var pIndex = tileData['projectionIndex'];
			
			var tileNode = tile.projections[pIndex];
				tileNode.style.top = tileData['top']+'px';
				tileNode.style.left = tileData['left']+'px';	
			
			map.rootNode.appendChild(tileNode);
		}
	}
	
	/*
	 *	Reverse of draw().
	 */
	map.flushScreen = function(){
		while(map.rootNode.firstChild){
			var child = map.rootNode.firstChild;
			map.rootNode.removeChild(child);
		}
		
		return(map);
	}
