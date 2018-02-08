app.get('/', function(req, res) {
	res.render('pages/index', {
		config: config,
		film: film,
		h1_title: h1_title,
		image_url: '#'
	});
});