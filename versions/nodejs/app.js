// Includes
var express = require("express");
var http 	= require("http");
//var ejs 	= require('ejs');
//var path	= require('path');
var fs 		= require('fs');
var xml2js 	= require('xml2js');


// Config
var config = {
		show_large_image: true,

	}

var film = {};


// Start server - Express
var app = express();
var parser = new xml2js.Parser();


app.set("port", 8080);
app.set('view engine', 'ejs');

app.use(express.static(__dirname + '/public'));

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


// Get user XML
var user_xml_path = "/public/users/1/films.xml";
fs.readFile(__dirname + user_xml_path, function(err, data) {
	parser.parseString(data, function (err, result) {
		console.dir(result.movies["film"][0].ru);
		console.dir(result.movies["film"][0].en);
		console.dir(result.movies["film"][0].kinopoisk);
		console.log("Done!");
	});
});