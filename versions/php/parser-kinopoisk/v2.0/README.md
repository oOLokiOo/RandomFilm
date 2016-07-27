# Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер

_This is updated version of my DEPRECATED script - https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v1.0_
Lasted version - https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0

<hr />
- **curl.php** library - https://github.com/RubtsovAV/php-curl-lib ( By Alexander Rubtsov <RubtsovAV@gmail.com> )
- **simple_html_dom.php** library - http://simplehtmldom.sourceforge.net ( By S.C. Chen <me578022@gmail.com>, John Schlick, Rus Carroll)
- **kinopoisk_parser.class.php** - PHP class for parsing ( By Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com> )
<hr />


**Integrate the class into your project:**
```php
require_once %PATH_TO_kinopoisk_parser.class.php_DIRECTORY%.'/inc/kinopoisk_parser.class.php';

$parser = new KinopoiskParser();
// ... setup $parser methods
```
**_NOTICE*:_** _Replace %PATH_TO_kinopoisk_parser.class.php_DIRECTORY% with real path!_


<hr />
**BASIC METHODS:**
* $parser->search_query
* $parser->direct_url
* $parser->parse_all_nonstop
* $parser->web_version
* $parser->save_result

**BASIC USAGE:**

* Web version - get Film by "search_query":
```php
$search_query = ((isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') ? $_REQUEST['search_query'] : '');

$parser = new KinopoiskParser();
$parser->web_version = true;
$parser->search_query = $search_query;
$parser->process();

require_once %PATH_TO_kinopoisk_parser.class.php_DIRECTORY%.'/tpl/index.tpl';
exit();
```

* Web version - get Film by "direct_url" to Film:
```php
$parser = new KinopoiskParser();
$parser->web_version = true;
$parser->direct_url = 'http://www.kinopoisk.ru/film/61237/';
$parser->process();

require_once %PATH_TO_kinopoisk_parser.class.php_DIRECTORY%.'/tpl/index.tpl';
exit();
```

* NOT Web version - get array with Film info by "direct_url" to Film:
```php
$parser = new KinopoiskParser();
//$parser->web_version = false; /* false - by default */
$parser->direct_url = 'http://www.kinopoisk.ru/film/61237/';
$parser->process();

echo '<pre>'; print_r($parser->result); echo '</pre>';
```

* NOT Web version - get only image url by "direct_url" to Film:
```php
$parser = new KinopoiskParser();
//$parser->web_version = false; /* false - by default */
$parser->direct_url = 'http://www.kinopoisk.ru/film/61237/';
$imge_src = $parser->findImage()->src;

echo $imge_src;
```

* NOT Web version - MAIN mode! Parse all site kinopoisk.ru and save all info to DB and all pictures to HDD:
```php
$parser = new KinopoiskParser();
//$parser->web_version = false; /* false - by default */
//$parser->direct_url = ''; /* '' - by default */
$parser->parse_all_nonstop = true;
$parser->save_result = true;
$parser->process();
```
