# Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер

_This is updated version of my DEPRECATED script - https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0_


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
$result = $parser->getFilmBySearchQuery('%SEARCH_QUERY%');
```
**_NOTICE*:_** _Replace %PATH_TO_kinopoisk_parser.class.php_DIRECTORY% with real path!_

* getFilmByDirectUrl public method:
```php
$result = $parser->getFilmByDirectUrl('%URL%');
```
**_NOTICE*:_** _Replace %PATH_TO_kinopoisk_parser.class.php_DIRECTORY% with real path!_
