<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html14/strict.dtd">
<html>
	<head>
		<title>=^-^=</title>
		
		<meta name="author" content="Deniss Narushevich"/>
		
		<link rel="stylesheet" type="text/css" href="css/main.css">
		<link rel="stylesheet" type="text/css" href="css/topBar.css">
		<link rel="stylesheet" type="text/css" href="css/tileMenu.css">
		
		<script language="javascript" type="text/javascript" src="js/jquery-1.6.2.js"></script>
		<script language="javascript" type="text/javascript" src="js/map.js"></script>
		<script language="javascript" type="text/javascript" src="js/tile.js"></script>
		<script language="javascript" type="text/javascript" src="js/tileCollection.js"></script>
		<script language="javascript" type="text/javascript" src="js/tileMenu.js"></script>
		<script language="javascript" type="text/javascript" src="js/mapPad.js"></script>
		<script language="javascript" type="text/javascript" src="js/action.js"></script>
		<script language="javascript" type="text/javascript">			
			$(document).ready(function(){
				map.init(document.getElementById('screen')).fill().draw();
				mapPad.init();
				action.getUser(2,function(data){$('#topBar > div').html('Cash: '+data.cash+'$').css({"font-size": "14px", "font-weight": "bold", "text-indent": "10px", "line-height": "42px"})})
			});
		</script>
	</head>
	<body>
		<div id="topBar">
			<div></div>
		</div>
		<div id="screen">

		</div>
	</body>
</html>
