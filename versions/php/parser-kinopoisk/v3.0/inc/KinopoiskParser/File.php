<?php

namespace Inc\KinopoiskParser;

class File {

	public function __construct() {
		// do something...
	}


	private function checkPath($path = '') {
		return (isset($path) && is_readable($path)) ? true : false;
	}


	public function writeToFile($path = '', $str = '') {
		if ($this->checkPath($path) === true) {
			return file_put_contents($path, 
				$str, 
				FILE_APPEND);
		}

		throw new Exception('Can\'t writeToFile(); - '.$path);
	}

	private function getFromFile($path = '') {
		if ($this->checkPath($path) === true) file_put_contents($path);

		throw new Exception('Can\'t getFromFile(); - '.$path);
	}
}
