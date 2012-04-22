talkee = function(){
	this.method = 'POST';
	this.target = '/gameServerCosmo/bus.php';
	this.onResponse = function(){};
	this.queryString = '';
	this.require = function(){
		var ajaxObject;
		
		try{
			// Chrome, Opera 8.0+, Firefox, Safari
			ajaxObject = new XMLHttpRequest();
		} catch (e){
			// Internet Explorer Browsers
			try{
				ajaxObject = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try{
					ajaxObject = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e){
					// Something went wrong
					alert("Your browser broke!");
				}
			}
		}

		ajaxObject.callerTalkee = this; //passing talkee's instance inside.

		ajaxObject.open(this.method, this.target);
		
		ajaxObject.onreadystatechange = function(){
			if(ajaxObject.readyState == 4){
				var response = ajaxObject.responseText;
			console.log(response);
				//console.log(response);
				response = JSON.parse(response);
				this.callerTalkee.onResponse(response);
			}
		}
		
		ajaxObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//console.log(this.queryString);
		ajaxObject.send("json_request="+this.queryString);
		console.log(this.queryString);
	}
}

query = function(){
	this.pid = 2; //TODO make user login/regs
	this.action = "";
	this.params = [];
}

action = new Object();
/*action.getTile = function(x, y, onResponse){
	var q = new query();
		q.action = "getTile";
		q.params = {'x': x, 'y': y};
	
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}*/

action.getTiles = function(coords, onResponse){
	var q = new query();
		q.action = "getTiles";
		q.params = {"coords": coords};
		
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}

action.buyTile = function(x, y, onResponse){
	var q = new query();
		q.action = "buyTile";
		q.params = {'x': x, 'y': y};
	
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}

action.getBuildingList = function(x, y, onResponse){
	var q = new query();
		q.action = "getBuildingList";
		q.params = {'x': x, 'y': y};
	
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}

action.build = function(x, y, cid, onResponse){
	var q = new query();
		q.action = "buildStructure";
		q.params = {'x': x, 'y': y, 'cid': cid};
	
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}

action.getUser = function(uid, onResponse){
	var q = new query();
		q.action = "getUser";
		q.params = {'uid': uid};
	
	var t = new talkee();
		t.onResponse = onResponse;
		t.queryString = JSON.stringify(q);
		t.require();
}