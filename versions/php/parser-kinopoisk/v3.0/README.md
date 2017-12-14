# Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер

_This is updated version of previous version - https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v2.0_


<hr />
* <b>curl.php</b> - library https://github.com/RubtsovAV/php-curl-lib ( By Alexander Rubtsov <RubtsovAV@gmail.com> ) <br />
  * <b>simple_html_dom.php</b> library http://simplehtmldom.sourceforge.net ( By S.C. Chen <me578022@gmail.com>, John Schlick, Rus Carroll ) <br />

// TODO: ...
<hr />


**Integrate the class into your project:**
```php
require_once %PATH_TO_Parser.php_DIRECTORY%.'/KinopoiskParser/Parser.php';
use \KinopoiskParser\Parser;

$parser = new Parser();
```
**_NOTICE*:_** _Replace %PATH_TO_Parser.php_DIRECTORY% with real path!_



<hr />
<b>BASIC METHODS:</b> <br />
* $parser->setLogging(); <br />
* $parser->setLogErrorPath(); <br />
* $parser->getFilmByDirectUrl(); <br />
* $parser->getFilmBySearchQuery(); <br /><hr />

**BASIC USAGE:**

* getFilmBySearchQuery public method:
```php
$result = $parser->getFilmBySearchQuery('%SEARCH_QUERY%');
```
**_NOTICE*:_** _Replace %SEARCH_QUERY% with real film title!_

* getFilmByDirectUrl public method:
```php
$result = $parser->getFilmByDirectUrl('%URL%');
```
**_NOTICE*:_** _Replace %URL% with real full url with to detail kinopoisk.ru film page!_
