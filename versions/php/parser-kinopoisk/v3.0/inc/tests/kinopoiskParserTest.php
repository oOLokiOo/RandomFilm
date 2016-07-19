<?php

require_once __DIR__.'/../kinopoisk_parser.class.php';


class KinopoiskParserTest extends PHPUnit_Framework_TestCase {
	public function setUp() { }
	public function tearDown() { }


	// Test: $KinopoiskParser->FindImage();
	public function testFindImage_WithNotSettedDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNull($parser->findImage());
	}

	public function testFindImage_WithNormalDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNotNull($parser->findImage('http://www.kinopoisk.ru/film/61237/'));
	}

	public function testFindImage_WithBadDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNull($parser->findImage('BAD_URL'));
	}

	public function testFindImage_SrcExist() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$res = $parser->findImage('http://www.kinopoisk.ru/film/61237/');
		$this->assertTrue((isset($res->src) && strlen($res->src)));
	}


	// Test: $KinopoiskParser->findStarring();
	public function testFindStarring_WithNotSettedDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNull($parser->findStarring());
	}

	public function testFindStarring_WithNormalDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNotNull($parser->findStarring('http://www.kinopoisk.ru/film/61237/'));
	}

	public function testFindStarring_WithBadDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->AssertNull($parser->findStarring('BAD_URL'));
	}


	// Test: $KinopoiskParser->getFilmBySearchQuery();
	public function testGetFilmBySearchQuery_WithNotSettedQuery() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$res = $parser->getFilmBySearchQuery();
		$this->assertTrue((isset($res['error']) && $res['error'] != ''));
	}

	public function testGetFilmBySearchQuery_NothingWasFoundInTopSearchResult() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$res = $parser->getFilmBySearchQuery('qqqwwweeezzz');
		$this->assertTrue((isset($res['error']) && $res['error'] != ''));
	}

	public function testGetFilmBySearchQuery_FoundedFilmIdIsNumeric() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->assertTrue(is_numeric($parser->getFilmBySearchQuery('test')['id']));
	}


	// Test: $KinopoiskParser->getFilmByDirectUrl();
	public function testGetFilmByDirectUrl_WithNotSettedDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$res = $parser->getFilmByDirectUrl();
		$this->assertTrue((isset($res['error']) && $res['error'] != ''));
	}

	public function testGetFilmByDirectUrl_WithBadDirectUrl() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$res = $parser->getFilmByDirectUrl('BAD_URL');
		$this->assertTrue((isset($res['error']) && $res['error'] != ''));
	}

	public function testGetFilmByDirectUrl_FoundedFilmIdIsNumeric() {
		echo "\n... ".__FUNCTION__." ...\n";
		$parser = new KinopoiskParser();
		$this->assertTrue(is_numeric($parser->getFilmByDirectUrl('http://www.kinopoisk.ru/film/61237/')['id']));
	}


	// Test: $KinopoiskParser->parseAllSite();
}