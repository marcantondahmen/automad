<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Core\FileUtils
 */
class FileUtilsTest extends TestCase {
	public function dataForTestFileIsImageIsFalse() {
		return array(array('path/to/file.pdf'));
	}

	public function dataForTestFileIsImageIsTrue() {
		return array(array('path/to/image.png'), array('file.gif'));
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
}
