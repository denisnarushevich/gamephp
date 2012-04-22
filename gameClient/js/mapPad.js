var mapPad = new Object();
	mapPad.mouseVector = [0, 0] //default cordinates of mouse
	mapPad.mouseIsoVector = [0, 0] //isometric cordinates of mouse
	mapPad.mouseOverTile = [0, 0] //local map coordinates of tile, on which mouse is pointing
	mapPad.tempVector = [0, 0] //vector for temporary values, to share them between events
	
	mapPad.isClick = true; //defines if event is click or map scroll
	
	mapPad.rad = (Math.PI/180)*(45); //-45 degree angle in radians

	mapPad.init = function(){		
		//draw overlay		
		var mapPadOverlay = document.createElement('div');
			mapPadOverlay.id = 'mapPad';
			mapPadOverlay.style.width = map.width*(map.tileWidth+2)-2+'px'; //2 is "horizontal gap" between tiles
			mapPadOverlay.style.height = (map.height-1)*map.tileHeight+'px';
			mapPadOverlay.style.position = 'absolute';
			mapPadOverlay.style.top = '0px';
			mapPadOverlay.style.left = '0px';
			mapPadOverlay.style.zIndex = '2';
			
			
		//mapPadContainer.appendChild(mapPadOverlay);
		map.parentNode.appendChild(mapPadOverlay);
		
		
		
		//default mouse coordinates transformations in the isometric coordinates
		$('#mapPad').mousemove(function(e){
			var pageX = e.pageX-$(this).offset().left - ((map.tileWidth+2)*Math.ceil(map.width/2));
			var pageY = e.pageY-$(this).offset().top - (map.tileHeight*Math.floor(map.height/2));

			mapPad.mouseIsoVector[0] = [pageX*Math.sin(mapPad.rad)/2 + pageY*Math.cos(mapPad.rad)];
			mapPad.mouseIsoVector[1] = [pageX*Math.cos(mapPad.rad)/2 - pageY*Math.sin(mapPad.rad)];
			
			mapPad.mouseOverTile[0] = Math.ceil(mapPad.mouseIsoVector[0]/69); //69 is length of one side of tile, in isometric coords
			mapPad.mouseOverTile[1] = Math.ceil(mapPad.mouseIsoVector[1]/69); //69 is length of one side of tile, in isometric coords
			
			//$('#topBar div').html(pageX+';'+pageY);
			//$('#topBar div').html(mapPad.mouseIsoVector[0]+';'+mapPad.mouseIsoVector[1]);
			//$('#topBar div').html(mapPad.mouseOverTile[0]+';'+mapPad.mouseOverTile[1]);
		});
		
		
		
		//bind mouse move when mouse is pressed
		$('#mapPad').mousedown(function(e){
			if(tileMenu.menu){
				tileMenu.menu.close();
			}
			
			//writing vector root coordinates.
			mapPad.tempVector[0] = mapPad.mouseOverTile[0];
			mapPad.tempVector[1] = mapPad.mouseOverTile[1];
			
			$(this).bind('mousemove', mapPad.scrolling);
			
			return(false); //to avoid text selection
		});
		
		//unbind when not scrolling
		$('#mapPad').mouseup(function(){
			$(this).unbind('mousemove', mapPad.scrolling);
			map.fill().draw(); //draw map and load all the tiles
		});
		
		
		
		$('#mapPad').click(function(e){
			if(mapPad.isClick){
				tileMenu.open(mapPad.mouseOverTile[0]+map.x, mapPad.mouseOverTile[1]+map.y, e);
			}
			mapPad.isClick = true;
		});
	}
	
	//on mouse move, we take default mouse coordinates and transform them by adding 45degree angle,
	//and decrease Y coordinate by 50%. Then coordinates should be isometric.
	mapPad.scrolling = function(e){
			var dX = mapPad.tempVector[0] - mapPad.mouseOverTile[0];
			var dY = mapPad.tempVector[1] - mapPad.mouseOverTile[1];

			if( (dX != 0) || (dY != 0) ){
				mapPad.isClick = false; //if map was scrolled for at least 1 tile, then it's definetely is not click ;D

				map.move(map.x+dX, map.y+dY)
				mapPad.tempVector[0] = mapPad.mouseOverTile[0];
				mapPad.tempVector[1] = mapPad.mouseOverTile[1];
			}
	}