<?php 

/**
 * Библиотека для удобной работы с CURL на PHP 5.3 >=
 * 
 * @author Alexander Rubtsov <RubtsovAV@gmail.com>
 * @version 1.1
 * @license http://opensource.org/licenses/LGPL-3.0 LGPL
 */

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
	die('PHP Curl requires PHP 5.3 or higher');

define('CURLE_CONTENT_TYPE_NOT_ALLOWED', 100);
define('CURLE_CONTENT_LENGTH_EXCEEDS', 101);

class Curl
{
	protected $ch;
	protected $default_options = array();
	protected $request_method = '';
	protected $headers = '';
	protected $content = '';
	protected $content_length = 0;
	protected $max_content_length = 0;
	protected $error = FALSE;
	protected $allow_content_type = false;
	protected $is_manage_cookies = false;

	/**
	 * Хранилище кук
	 * @var CurlCookieStorage|null NULL если ни разу не было вызвано manage_cookies(true)
	 */
	public $cookie_storage;

	/**
	 * Создание нового экземпляра
	 * @param array $default_options Параметры CURL, которые будут использоваться для каждого запроса по умолчанию
	 */
	public function __construct($default_options = array())
	{
		$this->default_options = $this->merge_headers((array)$default_options, array(
			CURLOPT_HEADER 			=> FALSE,
			CURLOPT_POST 			=> FALSE,
			CURLOPT_FOLLOWLOCATION 	=> TRUE,
			CURLOPT_ENCODING		=> "",	//auto set header Accept-Encoding if curl has builed with zlib
			CURLOPT_USERAGENT		=> "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
			CURLOPT_HTTPHEADER		=> array("Expect: "),
			CURLOPT_AUTOREFERER    	=> TRUE,         // set referer on redirect 
			CURLOPT_CONNECTTIMEOUT 	=> 10,         	 // timeout on connect 
			CURLOPT_TIMEOUT        	=> 120,          // timeout on response 
			CURLOPT_MAXREDIRS      	=> 10,           // stop after 10 redirects 
			CURLOPT_SSL_VERIFYHOST 	=> 0,            // don't verify ssl 
			CURLOPT_SSL_VERIFYPEER 	=> FALSE,    
			CURLOPT_HEADERFUNCTION	=> array($this, 'read_header'),               
			CURLOPT_WRITEFUNCTION	=> array($this, 'body_callback'),   
			CURLINFO_HEADER_OUT 	=> TRUE,            
		));
	}

	/**
	 * Выполняет GET запрос
	 *
	 * @throws В случае ошибки выбрасывает исключение CurlException(curl_error(), curl_errno())
	 *
	 * @param  string $url     Url запроса
	 * @param  array  $options Параметры CURL (заменяют параметры по умолчанию) 
	 * @return string          Возвращает тело ответа
	 */
	public function get($url, $options = array())
	{
		$this->request_method = 'get';
		$options[CURLOPT_POST] = FALSE;
		return $this->send($url, $options);
	}

	/**
	 * Выполняет POST запрос
	 *
	 * @throws В случае ошибки выбрасывает исключение CurlException(curl_error(), curl_errno())
	 *
	 * @param  string $url     Url запроса
	 * @param  array  $data    Данные post (см. CURLOPT_POSTFIELDS http://www.php.net/manual/ru/function.curl-setopt.php)
	 * @param  array  $options Параметры CURL (заменяют параметры по умолчанию) 
	 * @return string          Возвращает тело ответа
	 */
	public function post($url, $data = array(), $options = array())
	{
		$this->request_method = 'post';
		$options[CURLOPT_POST] = TRUE;
		$options[CURLOPT_POSTFIELDS] = $data;
		return $this->send($url, $options);
	}

	/**
	 * Выполняет GET запрос отбрасывая тело ответа
	 *
	 * @throws В случае ошибки выбрасывает исключение CurlException(curl_error(), curl_errno())
	 * 	 
	 * @param  string $url     Url запроса
	 * @param  array  $options Параметры CURL (заменяют параметры по умолчанию) 
	 * @return string          Возвращает HTTP заголовки ответа
	 */
	public function head($url, $options = array())
	{
		$this->request_method = 'head';
		return $this->send($url, $options);
	}

	/**
	 * Выполняет кастомный запрос
	 *
	 * @throws В случае ошибки выбрасывает исключение CurlException(curl_error(), curl_errno())
	 * 
	 * @param  string $url     Url запроса
	 * @param  array  $options Параметры CURL (заменяют параметры по умолчанию) 
	 * @return string          Возвращает тело ответа
	 */
	public function send($url, $options = array())
	{
		if(! $this->ch) $this->ch = curl_init();

		$options = $this->merge_headers((array)$options, $this->default_options);
		$options[CURLOPT_URL] = $this->curl_encode($url);

		if(!isset($options[CURLOPT_COOKIE]) && $this->is_manage_cookies)
			$options[CURLOPT_COOKIE] = $this->cookie_storage->getcookies_as_string($url);

		curl_setopt_array($this->ch, $options);

		$this->headers = '';
		$this->content = '';
		$this->content_length = 0;
		$this->error = false;

		curl_exec($this->ch);	

		if($errornum = curl_errno($this->ch)){
			if($errornum != CURLE_WRITE_ERROR) 
				$this->error = new CurlException(curl_error($this->ch), $errornum);
		}

		if($this->error) throw $this->error;
		if($this->request_method == 'head') return $this->headers;
		return $this->content;
	}

	/**
	 * Завершает сеанс СURL
	 */
	public function close()
	{
		if($this->ch) curl_close($this->ch);
	}


	/**
	 * Устанавлиет разрешенные mime типы для ответов сервера.
	 *
	 * При получении ответа от сервера происходит проверка заголовка Content-Type на наличие разрешенных mime типов.
	 * Если mime тип отсутсвиет в списке разрешенных, чтение ответа прерывается и выбрасывается исключение CurlException с номером ошибки CURLE_CONTENT_TYPE_NOT_ALLOWED.
	 * При отсутсвии заголовка Content-Type, mime тип считается разрешенным.
	 * Примечание: Тип проверяется на начальное вхождение.
	 *
	 * Пример:
	 * <code>
	 * $curl = new Curl();
	 * $curl->allow_content_type(array('text/html', 'image/'));
	 * $response = $curl->get('http://www.anydomain.ru/any');
	 * </code>
	 *
	 * Пример не выбросит исключения, если получит любой из следующих заголовков:
	 *  Content-Type: text/html
	 *  Content-Type: text/html; encode=utf-8
	 *  Content-Type: image/jpeg
	 *  Content-Type: image/bmp
	 *  Content-Type: image/любой_текст
	 *
	 * 
	 * @param  string[]|string|false|null $types Mime типы, которые будут разрешены. False - разрешает любые mime типы. По умолчанию null - ничего не изменяет (используется, чтобы получить текущее значение allow_content_type).
	 * @return array|false    Возвращает установленные mime типы или false если таковых нет.
	 */
	public function allow_content_type($types = null)
	{
		if(is_null($types)) return $this->allow_content_type;

		if($types === FALSE) $this->allow_content_type = FALSE;
		else{
			if(!is_array($types)) $types = array($types);
			$this->allow_content_type = $types;
		}
		return $this->allow_content_type;
	}


	/**
	 * Устанвливает максимально допустимый размер ответа (с учётом HTTP заголовков).
	 * 
	 * @param  int|null      Максимально допустимый ответ сервера в байтах. 0 - снимает ограничение. По умолчанию null - ничего не изменяет (используется, чтобы получить текущее значение max_content_length).
	 * @return int           Текущее значение max_content_length
	 */
	public function max_content_length($limit=null)
	{
		if(is_null($limit)) return $this->max_content_length;

		$this->max_content_length = (int)$limit;
		return $this->max_content_length;
	}


	/**
	 * Возвращает информацию о полученном ответе.
	 * @see    http://www.php.net/manual/ru/function.curl-getinfo.php
	 * 
	 * @param  string|null $key Если указан, то будет возвращен не весь массив, а элемент массива с таким ключом.
	 * @return array|string 
	 */
	public function getinfo($key = null)
	{
		if(! $this->ch) return array();
		$info = curl_getinfo($this->ch);
		if(is_null($key)) return $info;
		return $info[$key];
	}

	/**
	 * Включает отслеживание и возможность управления куками.
	 * 
	 * @param  boolean $mode True - включает, false - отключает.
	 * @return void
	 */
	public function manage_cookies($mode = true)
	{
		$this->is_manage_cookies = (bool) $mode;
		if($this->is_manage_cookies && !$this->cookie_storage)
			$this->cookie_storage = new CurlCookieStorage();
	}

	/**
	 * Кодирует русские символы (utf-8) и пробелы в соотвествии с RFC 3986
	 * @param  string $url Исходный url
	 * @return string Закодированный url
	 */
	public function curl_encode($url)
	{ 
		return strtr($url,array(" "=> "%20", "а"=>"%D0%B0", "А"=>"%D0%90","б"=>"%D0%B1", "Б"=>"%D0%91", "в"=>"%D0%B2", "В"=>"%D0%92", "г"=>"%D0%B3", "Г"=>"%D0%93", "д"=>"%D0%B4", "Д"=>"%D0%94", "е"=>"%D0%B5", "Е"=>"%D0%95", "ё"=>"%D1%91", "Ё"=>"%D0%81", "ж"=>"%D0%B6", "Ж"=>"%D0%96", "з"=>"%D0%B7", "З"=>"%D0%97", "и"=>"%D0%B8", "И"=>"%D0%98", "й"=>"%D0%B9", "Й"=>"%D0%99", "к"=>"%D0%BA", "К"=>"%D0%9A", "л"=>"%D0%BB", "Л"=>"%D0%9B", "м"=>"%D0%BC", "М"=>"%D0%9C", "н"=>"%D0%BD", "Н"=>"%D0%9D", "о"=>"%D0%BE", "О"=>"%D0%9E", "п"=>"%D0%BF", "П"=>"%D0%9F", "р"=>"%D1%80", "Р"=>"%D0%A0", "с"=>"%D1%81", "С"=>"%D0%A1", "т"=>"%D1%82", "Т"=>"%D0%A2", "у"=>"%D1%83", "У"=>"%D0%A3", "ф"=>"%D1%84", "Ф"=>"%D0%A4", "х"=>"%D1%85", "Х"=>"%D0%A5", "ц"=>"%D1%86", "Ц"=>"%D0%A6", "ч"=>"%D1%87", "Ч"=>"%D0%A7", "ш"=>"%D1%88", "Ш"=>"%D0%A8", "щ"=>"%D1%89", "Щ"=>"%D0%A9", "ъ"=>"%D1%8A", "Ъ"=>"%D0%AA", "ы"=>"%D1%8B", "Ы"=>"%D0%AB", "ь"=>"%D1%8C", "Ь"=>"%D0%AC", "э"=>"%D1%8D", "Э"=>"%D0%AD", "ю"=>"%D1%8E", "Ю"=>"%D0%AE", "я"=>"%D1%8F", "Я"=>"%D0%AF"));
	}
	
	/**
	 * Декодирует русские символы (utf-8) и пробелы в соотвествии с RFC 3986
	 * @param  string $url Исходный url
	 * @return string Декодированный url
	 */
	public function curl_decode($url)
	{ 
		return strtr($url,array("%20"=>" ", "%D0%B0"=>"а", "%D0%90"=>"А", "%D0%B1"=>"б", "%D0%91"=>"Б", "%D0%B2"=>"в", "%D0%92"=>"В", "%D0%B3"=>"г", "%D0%93"=>"Г", "%D0%B4"=>"д", "%D0%94"=>"Д", "%D0%B5"=>"е", "%D0%95"=>"Е", "%D1%91"=>"ё", "%D0%81"=>"Ё", "%D0%B6"=>"ж", "%D0%96"=>"Ж", "%D0%B7"=>"з", "%D0%97"=>"З", "%D0%B8"=>"и", "%D0%98"=>"И", "%D0%B9"=>"й", "%D0%99"=>"Й", "%D0%BA"=>"к", "%D0%9A"=>"К", "%D0%BB"=>"л", "%D0%9B"=>"Л", "%D0%BC"=>"м", "%D0%9C"=>"М", "%D0%BD"=>"н", "%D0%9D"=>"Н", "%D0%BE"=>"о", "%D0%9E"=>"О", "%D0%BF"=>"п", "%D0%9F"=>"П", "%D1%80"=>"р", "%D0%A0"=>"Р", "%D1%81"=>"с", "%D0%A1"=>"С", "%D1%82"=>"т", "%D0%A2"=>"Т", "%D1%83"=>"у", "%D0%A3"=>"У", "%D1%84"=>"ф", "%D0%A4"=>"Ф", "%D1%85"=>"х", "%D0%A5"=>"Х", "%D1%86"=>"ц", "%D0%A6"=>"Ц", "%D1%87"=>"ч", "%D0%A7"=>"Ч", "%D1%88"=>"ш", "%D0%A8"=>"Ш", "%D1%89"=>"щ", "%D0%A9"=>"Щ", "%D1%8A"=>"ъ", "%D0%AA"=>"Ъ", "%D1%8B"=>"ы", "%D0%AB"=>"Ы", "%D1%8C"=>"ь", "%D0%AC"=>"Ь", "%D1%8D"=>"э", "%D0%AD"=>"Э", "%D1%8E"=>"ю", "%D0%AE"=>"Ю", "%D1%8F"=>"я", "%D0%AF"=>"Я"));
	}

	/**
	 * Возвращает HTTP заголовки ответа
	 * @return string
	 */
	public function headers()
	{
		return $this->headers;
	}

	protected function merge_headers($h1, $h2)
	{
		$result = $h1 + $h2;
		if(isset($result[CURLOPT_HTTPHEADER]) && !in_array("Expect: ", $result[CURLOPT_HTTPHEADER]))
			$result[CURLOPT_HTTPHEADER][] = "Expect: ";
		return $result;
	}

	protected function body_callback($ch, $string)
	{
		$length = strlen($string);
		$this->content_length += $length;
		$this->content .= $string;
			
		if($this->max_content_length > 0 
			&& $this->content_length > $this->max_content_length
		){
			$this->error = new CurlException("Content length exceeds the maximum size {$this->max_content_length} bytes", CURLE_CONTENT_LENGTH_EXCEEDS);
		}
			
		if($this->error) return -1;
		return $length;
	}

	protected function read_header($ch, $string)
	{
	    $length = strlen($string);
		$this->content_length += $length;
		$this->headers .= $string;

		if($this->max_content_length > 0 
			&& $this->content_length > $this->max_content_length
		){
			$this->error = new CurlException("Content length exceeds the maximum size {$this->max_content_length} bytes", CURLE_CONTENT_LENGTH_EXCEEDS);
			return -1;
		}

	    if(($content_type = $this->get_content_type($string)) !== FALSE){
	    	if(!$this->is_allow_content_type($content_type))
	    		$this->error = new CurlException("Content-Type '$content_type' is not allowed", CURLE_CONTENT_TYPE_NOT_ALLOWED);
	    }

	    if($this->is_manage_cookies)
	    {
	    	$current_url = $this->getinfo('url');
	    	$is_setcookie = $this->cookie_storage->parse_cookies($current_url, $string);
	    	if($is_setcookie || $string == "\r\n")
	    		curl_setopt($ch, CURLOPT_COOKIE, $this->cookie_storage->getcookies_as_string($current_url));
	    }

	    if($string == "\r\n" && $this->request_method == 'head') return -1;
	    return $length;
	}

	protected function get_content_type($string)
	{
		if(stripos($string, 'Content-Type:') !== 0) return FALSE;

		$content_type = substr($string, strpos($string, ':') + 1);
    	if($p = strpos($content_type, ';')) 
    		$content_type = substr($content_type, 0, $p);

    	return trim($content_type);
	}

	protected function is_allow_content_type($mime_type)
	{
		if(empty($this->allow_content_type)) return TRUE;

		$is_allow = FALSE;
    	foreach($this->allow_content_type as $allow_content_type){
    		if(stripos($allow_content_type, $mime_type) === 0){
    			$is_allow = TRUE;
    			break;
    		}
    	}
    	return $is_allow;
	}
}

class CurlException extends Exception{}


/**
 * Хранилище кук
 * @uses  Curl
 */
class CurlCookieStorage 
{
	protected $storage = array();

	/**
	 * Устанавливает значение куки.
	 * 
	 * Создает новую или заменяет куку если name, domain и path совпадают.
	 * 
	 * @param  array  $cookie Параметры куки
	 * array(
	 * 	'name' 		=> (string),		//Имя (обязательное)
	 * 	'value' 	=> (string), 		//Значение
	 * 	'domain'	=> (string),		//Домен (обязательное)
	 * 	'path'		=> (string),		//Пустое значение равно /
	 * 	'expires'	=> (string|int),	//Период хранения куки (unix timestamp или date format)
	 * 	'secure'	=> (boolean), 		//Передавать только по https протоколу
	 * 	'httponly' 	=> (boolean), 		//Запретить доступ из js
	 * )
	 * 	
	 * @return void
	 */
	public function setcookie($cookie = array())
	{
		$cookie['name'] = (string) $cookie['name'];
		$cookie['value'] = (string) $cookie['value'];	
		$cookie['secure'] = (bool) $cookie['secure'];
		$cookie['httponly'] = (bool) $cookie['httponly'];

		if(!is_numeric($cookie['expires']))
		{
			$timestamp = @strtotime($cookie['expires']);
			if($timestamp) $cookie['expires'] = $timestamp;
			else $cookie['expires'] = 0;
		}

		if(empty($cookie['path'])) $cookie['path'] = '/';
		$cookie['domain'] = strtolower(trim($cookie['domain'], '.'));

		$this->storage[$cookie['domain']][$cookie['path']][$cookie['name']] = $cookie;
	}

	/**
	 * Возвращает куки (или куку, при указанном параметре $name) для страницы $url
	 * 
	 * @param  string $url  адрес страницы
	 * @param  string $name название куки
	 * @return array[]|array  куки (или куку) для данного адреса
	 */
	public function getcookie($url, $name = null)
	{
		$parsed_url = parse_url($url);
		$is_https = $parse_url['scheme'] === 'https';
		$domain = strtolower(trim($parsed_url['host'], '.'));
		$path = $parsed_url['path'];
		if(empty($path)) $path = '/';

		$match_domains = array_filter(array_keys($this->storage), function($cookie_domain) use ($domain) 
		{
			$need_pos = strlen($domain) - strlen($cookie_domain);
			if($need_pos < 0) return false;

			if(strpos($domain, $cookie_domain) === $need_pos)
			{
				if($need_pos === 0) return true;
				elseif($domain[$need_pos-1] === '.') return true;
			}
			return false;
  		});

		sort($match_domains);

		$now_timestamp = time();
		$needle_cookies = array();
		foreach($match_domains as $cookie_domain)
		{
			$match_paths = array_filter(array_keys($this->storage[$cookie_domain]), function($cookie_path) use ($path) 
			{
				return strpos($path, $cookie_path) === 0;
	  		});

	  		sort($match_paths);

	  		foreach($match_paths as $cookie_path)
	  			foreach($this->storage[$cookie_domain][$cookie_path] as $cookie_name => $cookie_data)
	  			{
	  				$expires = $cookie_data['expires'];
	  				if($expires !== 0 && $expires < $now_timestamp)
	  				{
	  					unset($this->storage[$cookie_domain][$cookie_path][$cookie_name]);
	  					continue;
	  				}
	  				if($cookie_data['secure'] && !$is_https) continue;
	  				$needle_cookies[$cookie_name] = $cookie_data;
	  			}
		}

		if(!empty($name)) return $needle_cookies[$name];
		return $needle_cookies;
	}

	/**
	 * Возвращает куки для страницы $url в формате HTTP заголовка Cookie
	 * @param  string $url адрес страницы
	 * @return string значние для HTTP заголовка Cookie
	 */
	public function getcookies_as_string($url)
	{
		$cookies = $this->getcookie($url);
		$cookies_string = '';
		foreach($cookies as $cookie)
			$cookies_string .= $cookie['name']. '='. $cookie['value']. '; ';
		
		return substr($cookies_string, 0, -2);
	}

	/**
	 * Парсит HTTP заголовки на наличие Set-Cookie и добавляет их в своё хранилище
	 * @param  string $url          адрес текущей страницы (нужен для дефолтных значений domain и path)
	 * @param  string $http_headers HTTP заголовки
	 * @return int    кол-во добавленных кук
	 */
	public function parse_cookies($url, $http_headers)
	{
		if($url)
		{
			$parsed_url = parse_url($url);

			$default_domain = $parsed_url['host'];
			$default_path = $parsed_url['path'];
		}

		$cookie_parsed_count = 0;

		preg_match_all('/^Set-Cookie: (.*)/im', $http_headers, $set_cookie_headers);
		foreach ($set_cookie_headers[1] as $set_cookie_header_value)
		{
			$params = explode(';', $set_cookie_header_value);

	        $cookies_data = array();
	        $domain = $default_domain;
	        $path = $default_path;
	        $expires = 0;
	        $secure = false;
	        $httponly = false;
	        foreach($params as $param) 
	        {
	            list($name, $value) = explode('=', $param, 2);
	            $name = trim($name);
	            $value = trim($value);

	            $lower_name = strtolower($name);
	            switch ($lower_name) 
	            {
	            	case 'domain':
	            		if(!empty($value)) 
	            			$domain = $value;
	            		break;

	            	case 'path':
	            		if(!empty($value)) 
	            			$path = $value;
	            		break;

	            	case 'expires':
	            		if(!empty($value)) 
	            			$expires = $value;
	            		break;

	            	case 'max-age':
	            		$expires = time() + (int) $value;
	            		break;

	            	case 'secure':
	            		$secure = true;
	            		break;

	            	case 'httponly':
	            		$httponly = true;
	            		break;

	            	case 'version':
	            		$version = (int) $value;
	            		break;

	            	case 'comment':
	            		$comment = $value;
	            		break;
	            	
	            	default:
	            		$cookies_data[$name] = $value;
	            		break;
	            }
	        }

	        foreach($cookies_data as $cookie_name => $cookie_value)
	        {
	        	$this->setcookie(array(
	        		'name'	 =>	$cookie_name, 
	        		'value'	 =>	$cookie_value,
	        		'domain' => $domain, 
	        		'path'	 =>	$path,  
	        		'expires'=>	$expires, 
	        		'secure' =>	$secure, 
	        		'httponly'=> $httponly,
	        		'version'=> $version,
	        		'comment'=> $comment,
	        	));
	        	++$cookie_parsed_count;
	        }
		}

		return $cookie_parsed_count;
	}
}
