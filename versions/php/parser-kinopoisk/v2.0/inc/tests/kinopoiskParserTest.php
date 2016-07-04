<?php

require_once __DIR__.'/../kinopoisk_parser.class.php';


class KinopoiskParserTest extends PHPUnit_Framework_TestCase {
	public function setUp() { }
	public function tearDown() { }

	public function testFindImage_WithNotSettedDirectUrl() {
		$parser = new KinopoiskParser();
		$this->assertFalse($parser->findImage());
	}

	public function testFindImage_ObjectNotNull() {
		$parser = new KinopoiskParser();
		$parser->direct_url = 'http://www.kinopoisk.ru/film/61237/';
		$this->AssertNotNull($parser->findImage());
	}

	public function testFindImage_SrcExist() {
		$parser = new KinopoiskParser();
		$parser->direct_url = 'http://www.kinopoisk.ru/film/61237/';
		$this->assertGreaterThan('1', $parser->findImage()->src);
	}
}