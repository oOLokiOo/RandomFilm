// Includes
var express = require("express");
var http 	= require("http");
//var ejs 	= require('ejs');
//var path	= require('path');


// Config
var config = {
		show_large_image: true,

	}

var film = {};


// Start server - Express
var app = express();
app.set("port", 8080);

app.use(express.static(__dirname + '/public'));
app.set('view engine', 'ejs');

http.createServer(app).listen(app.get("port"), function(){
	console.log("Express server listening on port " + app.get("port"));
});


// Routes
app.get('/', function(req, res) {
	res.render('pages/index');
});

/*
app.use(function(req, res, next) {
	switch (req.url) {
		case '/':
			res.end("Index page");
			break;
	}

	next();
});
*/

app.use(function(req, res) {
	res.send(404, res.render('pages/404'));
});
