/*
 * GET home page.
 */

app.index = function(req, res){
  res.render('index', { title: 'Random movie that you would like to revise (c) Script was made by Ivan Volkov aka oOLokiOo' });
};
