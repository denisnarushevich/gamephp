To get it running, you need  to have a running php server, then configure:
1.First setup your Mysql database, and make a schema out of provided dump file 
"gameServer/data/game.sql", simple examples: http://www.patrickpatoray.com/?Page=30
default schema name should be "game", but you can change it in "gameServer/lib/db.php"

2.When your php server and database is running, you should edit server url in game client.
It can be done in gameClient/js/action.js . 
Server url should point on a bus.php file on your server, bus.php is request\response bus of this game, where all ajax request from client are sent.

*Your php server must have mysqli module enabled.

At this point you should have it running.

To check it out, just open index.html in client.

In case of something went wrong, and game tiles wont load - you can check browser console, every game request and response or server error is logged there.

You can email me d.narushevich at inbox dot lv