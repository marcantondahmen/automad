<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\Str
 */

class Str_Test extends TestCase {
	
	
	/**
	 *	@dataProvider dataForTestDefIsEqual
	 *	@testdox def("$str", "$defaultValue") equals "$expected" 
	 */
	
	public function testDefIsEqual($str, $defaultValue, $expected) {
		
		$this->assertEquals(Str::def($str, $defaultValue), $expected);
		
	}
	
	public function dataForTestDefIsEqual() {
		
		return array(
			array('', 'Some string.', 'Some string.'),
			array('Not empty.', 'Some string.', 'Not empty.')
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestSanitizeIsEqual
	 *	@testdox sanitize("$str", $removeDots, $maxChars) equals "$expected"
	 */
	
	public function testSanitizeIsEqual($str, $removeDots, $maxChars, $expected) {
		
		$this->assertEquals(Str::sanitize($str, $removeDots, $maxChars), $expected);
		
	}
	
	public function dataForTestSanitizeIsEqual() {
		
		return array(
			array('Some string.', true, 100, 'some-string'),
			array('Some very long sentence, with a comma.', true, 10, 'some-very'),
			array('Filename with space.jpg', false, 100, 'filename-with-space.jpg')
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestShortenIsEqual
	 *	@testdox shorten("$str", $maxChars, "$ellipsis") equals "$expected"
	 */
	
	public function testShortenIsEqual($str, $maxChars, $ellipsis, $expected) {
		
		$this->assertEquals(Str::shorten($str, $maxChars, $ellipsis), $expected);
		
	}
	
	public function dataForTestShortenIsEqual() {
		
		return array(
			array('Some long string.', 3, ' ...', '...'),
			array('Some long string.', 8, ' ...', 'Some ...'),
			array('Some long string.', 9, ' (more)', 'Some long (more)'),
			array('Some long string.', 16, ' ...', 'Some long ...'),
			array('Some long string.', 17, ' ...', 'Some long string.'),
			array('Some long string.', 18, ' ...', 'Some long string.')
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestStripEndIsEqual
	 *	@testdox stripEnd("$str", "$end") equals "$expected"
	 */
	
	public function testStripEndIsEqual($str, $end, $expected) {
		
		$this->assertEquals(Str::stripEnd($str, $end), $expected);
		
	}
	
	public function dataForTestStripEndIsEqual() {
		
		return array(
			array('some test test test', 'test', 'some test test ')
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestStripStartIsEqual
	 *	@testdox stripStart("$str", "$start") equals "$expected"
	 */
	
	public function testStripStartIsEqual($str, $start, $expected) {
		
		$this->assertEquals(Str::stripStart($str, $start), $expected);
		
	}
	
	public function dataForTestStripStartIsEqual() {
		
		return array(
			array('test test test string', 'test', ' test test string')
		);
		
	}

	
}