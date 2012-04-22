function tile(x, y){
	this.x = x;
	this.y = y;
	this.landWidth = map.tileWidth;
	this.landHeight = map.tileHeight;
	this.undergroundHeight = map.undergroundHeight;
	this.airHeight = map.airHeight;
	
	this.elementHeight = this.landHeight + this.undergroundHeight + this.airHeight;
	this.elementWidth = this.landWidth;
	
	this.tileData = {"type":"none","structures":[]}; //array of tile data
	//this.loaded = 0;
	this.projections = new Array(); //array of tile nodes of current object. - Projection registry
	
	//this.onRefresh = function(){}

	this.setTileData = function(data){
		this.tileData = data;
		//this.onRefresh();
	}
	
	/*
	 *	Projection - node\element with tile.
	 *	Each projection is saved in projections array, so they all can be accessed 
	 *	at once if needed.
	 */	
	this.createProjection = function(){
		var tileBlock = document.createElement('div');
			tileBlock.className = 'tile';
			tileBlock.setAttribute('x', this.x);
			tileBlock.setAttribute('y', this.y);
			tileBlock.style.width = this.landWidth+'px';
			tileBlock.style.height = this.landHeight+'px';
			tileBlock.style.position = 'absolute';
			tileBlock.style.paddingTop = this.airHeight+'px';
			tileBlock.style.paddingBottom = this.undergroundHeight+'px';
			tileBlock.style.backgroundImage = 'url('+this.tileData['type']+'.png)';
			tileBlock.style.backgroundRepeat = 'no-repeat';
			tileBlock.style.backgroundPosition = '0px '+this.airHeight+'px';
		
		var structBlock = document.createElement('div');
		
			structBlock.style.bottom = this.undergroundHeight+'px';
			structBlock.style.width = this.landWidth+'px';
			structBlock.style.height = this.landHeight+'px';
			structBlock.style.position = 'absolute';
			structBlock.style.left = '0px';
			structBlock.style.paddingTop = this.airHeight+'px';
			structBlock.style.backgroundPosition = 'bottom left';
			structBlock.style.backgroundRepeat = 'no-repeat';
			
			if(this.tileData['structures'].length){
				structBlock.style.backgroundImage = 'url('+this.tileData['structures'][0]['name']+'/'+this.tileData['structures'][0]['imageName']+')';
			}
			
		
		tileBlock.appendChild(structBlock);
		
		this.projections.push(tileBlock);
		var index = this.projections.length-1;
		
		return(index);
	}
	
	this.refreshProjections = function(){
		for(var index in this.projections){
			this.refreshProjection(index);
		}
	}
	
	this.refreshProjection = function(oldIndex){
		var tileBlock = this.projections[oldIndex];
			tileBlock.style.backgroundImage = 'url('+this.tileData['type']+'.png)';
			
		var structBlock = tileBlock.childNodes[0];
			if(this.tileData['structures'].length){
				structBlock.style.backgroundImage = 'url('+this.tileData['structures'][0]['name']+'/'+this.tileData['structures'][0]['imageName']+')';
			}
	}	
	
	/*
	 *	deletes projection from array
	 */
	this.deleteProjection = function(index){
		this.projections.splice(index, 1);
		
		return(this);
	}
}

