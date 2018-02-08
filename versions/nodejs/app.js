// Includes
var express = require("express");
var http 	= require("http");
//var ejs 	= require('ejs');
//var path	= require('path');
var fs 		= require('fs');
var xml2js 	= require('xml2js');


// Config
var config = {
		show_large_image: true
	}


// Get user XML
var film,
	h1_title,
	image_url,
	parser = new xml2js.Parser(),
	user_xml_path = "/public/users/1/films.xml"; // TODO:: add user loggined ID here 

fs.readFile(__dirname + user_xml_path, function(err, data) {
	parser.parseString(data, function (err, result) {
		let rand = Math.floor(Math.random() * result.movies["film"].length);
		film = result.movies["film"][rand];

		h1_title = (film.ru ? film.ru + " | " : "")
								+ (film.en ? film.en + " | " : "")
								+ (film.year ? film.year : "");
							
		if (h1_title.slice(-2) == "| ") h1_title = h1_title.slice(0, -2);

		// image_url = 

		console.log(film);
	});
});


// Start server - Express
var app = express();
//var router = express.Router();

app.set("port", 8080);
app.set('view engine', 'ejs');

app.use(express.static(__dirname + '/public'));
//app.use(require('./routes'));
//app.use(require('./helpers'));

http.createServer(app).listen(app.get("port"), function(){
	console.log("Express server listening on port " + app.get("port"));
});


// Routes
app.get('/', function(req, res) {
	res.render('pages/index', {
		config: config,
		film: film,
		h1_title: h1_title,
		image_url: '#'
	});
});

app.use(function(req, res, next) {
	/*
	switch (req.url) {
		case '/':
			res.end("Index page");
			break;
		default:
			res.send(404, res.render('pages/404'));
	}
	*/
	//next();

	res.send(404, res.render('pages/404'));
});
