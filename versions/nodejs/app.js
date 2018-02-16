// Includes
var express = require("express");
var fs 		= require("fs");
var request = require("request");
var util	= require("util");
var http 	= require("http");
var opn 	= require("opn");
var cheerio = require("cheerio");
var xml2js 	= require("xml2js");
var path	= require("path");
var favicon = require("serve-favicon");


// Config
var config = {
		en_search_prefix: "kinopoisk.ru", // film poster
		ru_search_prefix: "kinopoisk.ru", // фильм постер
		show_large_image: false,
		user_xml_path: "/public/users/1/films.xml" // TODO:: add user loggined ID here 
	};

var index_page = {
		h1_title: "",
		google_image_url: "",
		kinopoisk_image_url: "",
		no_large_image: ""
	};

var film = {};
var parser = new xml2js.Parser();


// HELPERS - Get URL from Goole Images
function getUrlFromKinopoisk(search_query, callback) {
	let url = "https://google.com/search?q="+encodeURI(search_query)+"&dcr=0&source=lnms&tbm=isch&sa=X";

	request(url, function(error, response, html) {
		if (!error) {
			let $ = cheerio.load(html);
			
			let kinopoisk_image_url = $("#search img:first-child").parent().parent().find("a").attr("href");
			kinopoisk_image_url = (kinopoisk_image_url != undefined ? kinopoisk_image_url.slice(7, kinopoisk_image_url.length-1) : ""); // crop "/url?q=" from redirect url
			console.log("\n"+kinopoisk_image_url);

			request(kinopoisk_image_url, function(error, response, html) {
				if (!error) {
					//console.log("\n"+util.inspect(response, false, null));

					let $ = cheerio.load(html);
					let image_url = $("#photoBlock .popupBigImage img").attr("src");
					let ext = (image_url != undefined ? image_url.split(".").pop() : "");

					if (ext != "jpg") {
						index_page.no_large_image = true;
						return callback(null, error);

					 // opn(kinopoisk_image_url); // opn("https://www.kinopoisk.ru/404/");
					}
					else return callback(kinopoisk_image_url, false);
				} else {
					index_page.no_large_image = true;
					return callback(null, error);
		        }
			});
		} else {
			return callback(null, error);
        }
	});
}

// HELPERS - Get URL from Goole Images
function getUrlFromGoogleImages(search_query, callback) {
	let url = "https://google.com/search?q="+encodeURI(search_query)+"&dcr=0&source=lnms&tbm=isch&sa=X";

	request(url, function(error, response, html) {
		if (!error) {
			let $ = cheerio.load(html);
			let google_image_url = $("#search img:first-child").attr("src");

			return callback(google_image_url, false);
		} else {            
            return callback(null, error);
        }
	});
}

// HELPERS - Get User XML and prepare page variables
function prepareIndexPage() {
	fs.readFile(__dirname + config.user_xml_path, function(err, data) {
		parser.parseString(data, function (err, result) {
			// get random film from XLS
			// Math.round(min - 0.5 + Math.random() * (max - min + 1))
			// rand(0, result.movies["film"].films.length - 1)
			let rand = Math.floor(Math.random() * result.movies["film"].length);
			film = result.movies["film"][rand];

			// prepare H1 title
			index_page.h1_title = (film.ru ? film.ru + " | " : "")
									+ (film.en ? film.en + " | " : "")
									+ (film.year ? film.year : "");
								
			if (index_page.h1_title.slice(-2) == "| ") index_page.h1_title = index_page.h1_title.slice(0, -2);

			// prepare search query
			let search_title = ((film && film.en && film.en != "") ? film.en + " " : "")
						+ ((film && film.ru && film.ru != "") ? film.ru + " " : "")
						//+ (film.year ? film.year + " " : "") 
						+ config.en_search_prefix;

			// do search
			console.log(config);
			console.log(index_page);

			if (config.show_large_image === true) {
				getUrlFromKinopoisk(
					search_title,
					function(data, err) {
						console.log(" --- kinopoisk_image_url = "+data);
						index_page.kinopoisk_image_url = data;
					});
			} else {
				getUrlFromGoogleImages(
					search_title,
					function(data, err) {
						console.log(" --- google_image_url = "+data);
						index_page.google_image_url = data;
					});
			}

			console.log(film);
		});
	});
}


// Start server - Express
var app = express();
//var router = express.Router();

app.set("port", 8080);
app.set("view engine", "ejs");

app.use(express.static(__dirname + "/public"));
app.use(favicon(path.join(__dirname, "public/img", "favicon.ico")));

//app.use(require("./routes"));
//app.use(require("./helpers"));

http.createServer(app).listen(app.get("port"), function() {
	prepareIndexPage();

	console.log("Express server listening on port " + app.get("port"));
});


// Routes
app.get("/", function(req, res) {
	if (req.query.show_large_image == "false") { 
		config.show_large_image = false;
		index_page.no_large_image = false;
	}

	if (req.query.show_large_image == "true") { 
		config.show_large_image = true;
		index_page.no_large_image = true;
	}


	res.render("pages/index", {
		config: config,
		film: film,
		h1_title: index_page.h1_title,
		no_large_image: index_page.no_large_image,
		image_url: (config.show_large_image === true ? index_page.kinopoisk_image_url : index_page.google_image_url)
	}); // TODO:: add all to APP.*

	prepareIndexPage();
});

app.use(function(req, res, next) {
	/*
	switch (req.url) {
		case "/":
			res.end("Index page");
			break;
		default:
			res.send(404, res.render("pages/404"));
	}
	*/
	//next();

	res.send(404, res.render("pages/404"));
});
