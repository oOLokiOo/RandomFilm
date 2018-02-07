var http = require("http");
var static = require("node-static");
var file = new static.Server(".");

function accept(req, res) {

	res.writeHead(200, {
		'Content-Type': 'text/plain',
		'Cache-Control': 'no-cache'
	});

	res.end("Random movie that you would like to revise (c) Script was made by Ivan Volkov aka oOLokiOo");
}

http.createServer(accept).listen(8080);

console.log("Server running on port 8080");