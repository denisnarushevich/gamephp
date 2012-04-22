var tileMenu = new Object();
	tileMenu.menus = {}; //all menu instances
	tileMenu.menu;//current menu instance

	//generate elements of list and show it
	//if menu was previously opened , then close it
	tileMenu.open = function(x, y, event){
		if(tileMenu.menu){
			tileMenu.menu.close();
		}
		
		tileMenu.menu = new tileMenuProjection(tileMenu.generateStructure(x, y));
		$(tileMenu.menu.insert(event.pageX, event.pageY)).fadeIn(60);
	};
	
	tileMenu.close = function(){
		tileMenu.menu.close();
		tileMenu.menus = {};
		tileMenu.menu = null;
	}
	
	tileMenu.generateStructure = function(x, y){
		var tile = tileCollection.getTile(x, y);
		
		var menuStructure = [
			{
				'type': 'action',
				'name': 'Close',
				'image': 'closeIcon.png',
				'value': function(){
					tileMenu.close();
				}
			},
			{
				'type': 'submenu',
				'name': 'Info',
				'image': 'infoIcon.png',
				'value': [
					{
						'type': 'data',
						'name': 'X',
						'image': null,
						'value': tile.x
					},
					{
						'type': 'data',
						'name': 'Y',
						'image': null,
						'value': tile.y
					},
					{
						'type': 'data',
						'name': 'Z',
						'image': null,
						'value': tile.tileData.z
					},
					{
						'type': 'data',
						'name': 'Type',
						'image': null,
						'value': tile.tileData.type
					},
					{
						'type': 'data',
						'name': 'Owner',
						'image': null,
						'value': tile.tileData.owner
					},
					{
						'type': 'data',
						'name': 'Value',
						'image': null,
						'value': tile.tileData['value']
					},
					{
						'type': 'data',
						'name': 'Price',
						'image': null,
						'value': tile.tileData['price']
					}
				]
			},
			{
				'type': 'submenu',
				'name': 'Structures',
				'image': 'homeIcon.png',
				'value': 	(function(){
					var structuresMenuStructure = new Array;
					for(var key in tile.tileData.structures){
						var structure = tile.tileData.structures[key];

						var tpl = new Date(structure.createdAt*1000);
						structure.dateBuilt = tpl.getDate()+'.'+(tpl.getMonth()+1)+'.'+tpl.getFullYear();

						structuresMenuStructure.push({
							'type': 'submenu',
							'name': structure.name,
							'image': null,
							'value': [
								{
									'type': 'submenu',
									'name': 'Info',
									'image': 'infoIcon.png',
									'value': [
										{
											'type': 'data',
											'name': 'Income',
											'image': null,
											'value': structure.income
										},
										{
											'type': 'data',
											'name': 'Price',
											'image': null,
											'value': structure.price
										},
										{
											'type': 'data',
											'name': 'Built',
											'image': null,
											'value': structure.dateBuilt
										},
										{
											'type': 'submenu',
											'name': 'Product',
											'image': null,
											'value': (function(){
												var products = [];
												for(var key in structure.product){
													products.push({
														'type': 'data',
														'name': structure.product[key],
														'image': null,
														'value': key
													});
												}
												if(!products.length){
													products.push({
														'type': 'data',
														'name': null,
														'image': null,
														'value': 'nothing'
													});
												}
												return products;
											})()
										},
										{
											'type': 'submenu',
											'name': 'Require',
											'image': null,
											'value': (function(){
												var requires = [];
												for(var key in structure.require){
													requires.push({
														'type': 'data',
														'name': structure.require[key],
														'image': null,
														'value': key
													});
												}
												if(!requires.length){
													requires.push({
														'type': 'data',
														'name': null,
														'image': null,
														'value': 'nothing'
													});
												}
												return requires;
											})()
										}
									]
								},
								{
									'type': 'submenu',
									'name': 'Actions',
									'image': null,
									'value': [						
										{
											'type': 'action',
											'name': 'Close',
											'image': 'closeIcon.png',
											'value': function(){
												tileMenu.close();
											}
										}
									]
								}
							]
						});
					};
					return structuresMenuStructure;
				})()
			},
			{
				'type': 'submenu',
				'name': 'Actions',
				'image': null,
				'value': (function(){
					var actions = [];
					
					if(tile.tileData.owner != 'admin'){ //TODO user
						actions.push({
							'type': 'action',
							'name': 'buy',
							'image': 'buyIcon.png',
							'value': function(){
								action.buyTile(tile.x, tile.y, function(newOwnerName){
									if(newOwnerName){
										tile.tileData.owner = newOwnerName;
										tileMenu.close();
									}
								});
							}
						});
					}
					
					if(tile.tileData.owner == 'admin'){ //TODO user //kak bi nahuj nikuda ne goditsa ( ..
						actions.push({
							'type': 'action',
							'name': 'build',
							'image': 'buildIcon.png',
							'value': function(event){
								action.getBuildingList(tile.x, tile.y, function(data){
									var list = [];
									for(var key in data){
										var building = data[key];
										list.push({
											'type': 'action',
											'name': building.name,
											'image': null,
											'value': function(){
												action.build(tile.x, tile.y, building.classId, function(data){
													alert(data);
												});
											}
										});
									}

									var buildMenu = new tileMenuProjection(list);
									tileMenu.menu.submenus.push(buildMenu);
									$(buildMenu.insert(event.pageX, event.pageY)).fadeIn(60);
								});
							}
						});
					}
					
					return actions;
				})()
			}
		];

		return(menuStructure)
	}
	
var tileMenuProjection = function(menuStructure){
	
	this.id = 'uiMenu'+Math.round(new Date().getTime()*Math.random());
	
	tileMenu.menus[this.id] = this;
	
	this.submenus = new Array();

	this.menuHTMLElement = document.createElement('div');
	this.menuHTMLElement.id = this.id;
	this.menuHTMLElement.className = 'tileMenu';
	this.menuHTMLElement.style.display = 'none';

	for(var key in menuStructure){
		var itemElementContainer = document.createElement('a');
		var itemElement = document.createElement('div');
		var item = menuStructure[key];


		//setting icon
		if(item.image){
			itemElementContainer.style.backgroundImage = 'url(\'i/tileMenu/'+item.image+'\')';
			itemElementContainer.style.backgroundSize = '16px';
		}


		//if data
		if(item.type == 'data'){
			itemElementContainer.setAttribute('hover','false');
			
			if(item.name){
				itemElement.innerHTML = item.name+': '+item.value;
			}else{
				itemElement.innerHTML = item.value;
			}
		}


		//if action
		if(item.type == 'action'){
			itemElementContainer.setAttribute('hover','true');

			itemElement.innerHTML = item.name;
			$(itemElementContainer).click(item.value);
		}


		//if submenu
		if(item.type == 'submenu'){

			//skip item, if submenu has no items.
			if(!item.value.length) continue;


			itemElementContainer.setAttribute('hover','true');

			itemElement.innerHTML = item.name;


			var submenu = new tileMenuProjection(item.value);
			this.submenus.push(submenu);

			itemElementContainer.setAttribute('submenu', submenu.id);

			$(itemElementContainer).hover(
				function(){
					var id = $(this).attr('submenu');
					var top = $(this).offset().top-1; //-1 correction
					var left = $(this).offset().left + $(this).outerWidth()+4; //+4 correction

					//show current submenu
					var submenu = tileMenu.menus[id];
					$(submenu.insert(left, top)).fadeIn(60);

					//close all previously opened submenus
					var oldSubmenus = tileMenu.menus[$(this).parent().attr('id')].submenus;
					for(var key in oldSubmenus){
						oldSubmenu = oldSubmenus[key];

						if(oldSubmenu.id != id){ //save current submenu from closure
							oldSubmenu.outsert();
						}
					}
				},
				function(){}
			);
		}
		
		itemElementContainer.appendChild(itemElement);
		this.menuHTMLElement.appendChild(itemElementContainer);
	}
	
	//adding attribute "last" for last item in menu.
	if(this.menuHTMLElement.childNodes.length){
		this.menuHTMLElement.childNodes[this.menuHTMLElement.childNodes.length-1].setAttribute('last','true');
	}
   
	this.insert = function(left, top){
		var body = document.getElementsByTagName('body').item(0);
		var menu = this.menuHTMLElement;
			menu.style.left = left+'px';
			menu.style.top = top+'px';
			body.appendChild(menu);
		
		return(menu);	
	}
	
	this.outsert = function(){
		$(this.menuHTMLElement).fadeOut(60, function(){
			//$(this).remove(); //remove don't revert htmlelement to state as before insert(), so instead of removing element we are just hidding it.
		});
		
		for(var key in this.submenus){
			var submenu = this.submenus[key];
			submenu.outsert();
		}
	}
	
	this.close = function(){
		$(this.menuHTMLElement).fadeOut(60, function(){
			$(this).remove();
		});
		
		var submenu;
		while(submenu = this.submenus.pop()){
			submenu.close();
		}
	}
}