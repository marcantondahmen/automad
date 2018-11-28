<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\Parse
 */

class Parse_Test extends TestCase {


	/**
	 *	@dataProvider dataForTestCsvIsSame
	 *	@testdox csv("$str")
	 */
	
	public function testCsvIsSame($str, $expected) {
		
		$this->assertSame(Parse::csv($str), $expected);
		
	}
	
	public function dataForTestCsvIsSame() {
		
		return array(
			array(
				'jpg, png, gif',
				array('jpg', 'png', 'gif')
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestFileIsImageIsTrue
	 *	@testdox fileIsImage("$str") is true
	 */

	public function testFileIsImageIsTrue($str) {
		
		$this->assertTrue(Parse::fileIsImage($str));
		
	}
	
	public function dataForTestFileIsImageIsTrue() {
		
		return array(array('path/to/image.png'), array('file.gif'));
		
	}
	
	
	/**
	 *	@dataProvider dataForTestFileIsImageIsFalse
	 *	@testdox fileIsImage("$str") is false
	 */
	
	public function testFileIsImageIsFalse($str) {
		
		$this->assertFalse(Parse::fileIsImage($str));
		
	}
	
	public function dataForTestFileIsImageIsFalse() {
		
		return array(array('path/to/file.pdf'));
		
	}
	
	
	/**
	 *	@dataProvider dataForTestJsonOptionsIsSame
	 *	@testdox jsonOptions($str)
	 */
	
	public function testJsonOptionsIsSame($str, $expected) {
		
		$this->assertSame(Parse::jsonOptions($str), $expected);
		
	}
	
	public function dataForTestJsonOptionsIsSame() {
		
		return array(
			array(
				'{ key1: "Value", key2: 10 }',
				array(
					'key1' => 'Value',
					'key2' => 10
				)
			),
			array(
				'{ key1: "Value", "key2": \'Single quotes\' }',
				array(
					'key1' => 'Value',
					'key2' => 'Single quotes'
				)
			)
		);
		
	}
	
	
}