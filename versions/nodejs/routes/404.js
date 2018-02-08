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
