<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Core\Parse
 */
class ParseTest extends TestCase {
	public function dataForTestCsvIsSame() {
		return array(
			array(
				'jpg, png, gif',
				array('jpg', 'png', 'gif')
			)
		);
	}

	public function dataForTestFileIsImageIsFalse() {
		return array(array('path/to/file.pdf'));
	}

	public function dataForTestFileIsImageIsTrue() {
		return array(array('path/to/image.png'), array('file.gif'));
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

	/**
	 * @dataProvider dataForTestCsvIsSame
	 * @testdox csv("$str")
	 * @param mixed $str
	 * @param mixed $expected
	 */
	public function testCsvIsSame($str, $expected) {
		$this->assertSame(Parse::csv($str), $expected);
	}

	/**
	 * @dataProvider dataForTestFileIsImageIsFalse
	 * @testdox fileIsImage("$str") is false
	 * @param mixed $str
	 */
	public function testFileIsImageIsFalse($str) {
		$this->assertFalse(FileUtils::fileIsImage($str));
	}

	/**
	 * @dataProvider dataForTestFileIsImageIsTrue
	 * @testdox fileIsImage("$str") is true
	 * @param mixed $str
	 */
	public function testFileIsImageIsTrue($str) {
		$this->assertTrue(FileUtils::fileIsImage($str));
	}

	/**
	 * @dataProvider dataForTestJsonOptionsIsSame
	 * @testdox jsonOptions($str)
	 * @param mixed $str
	 * @param mixed $expected
	 */
	public function testJsonOptionsIsSame($str, $expected) {
		$this->assertSame(Parse::jsonOptions($str), $expected);
	}
}
