# Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер

_This is updated version of my DEPRECATED script - https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v2.0_


<hr />
- **curl.php** library - https://github.com/RubtsovAV/php-curl-lib ( By Alexander Rubtsov <RubtsovAV@gmail.com> )
- **simple_html_dom.php** library - http://simplehtmldom.sourceforge.net ( By S.C. Chen <me578022@gmail.com>, John Schlick, Rus Carroll)
// TODO: ...
<hr />


**Integrate the class into your project:**
```php
// TODO: ...
```


<hr />
**BASIC METHODS:**
* $parser->setLogging();
* $parser->setLogErrorPath();
* $parser->getFilmByDirectUrl();
* $parser->getFilmBySearchQuery();

**BASIC USAGE:**

* getFilmBySearchQuery public method:
```php
// TODO: ...
$result = $parser->getFilmBySearchQuery('%SEARCH_QUERY%');
```
**_NOTICE*:_** _Replace %SEARCH_QUERY% with real film title!_

* getFilmByDirectUrl public method:
```php
// TODO: ...
$result = $parser->getFilmByDirectUrl('%URL%');
```
**_NOTICE*:_** _Replace %URL% with real full url with to detail kinopoisk.ru film page!_
