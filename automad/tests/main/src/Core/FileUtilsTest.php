<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileUtilsTest extends TestCase {
	public static function dataForTestFileIsImageIsFalse() {
		return array(array('path/to/file.pdf'));
	}

	public static function dataForTestFileIsImageIsTrue() {
		return array(array('path/to/image.png'), array('file.gif'));
	}

	#[DataProvider('dataForTestFileIsImageIsFalse')]
	public function testFileIsImageIsFalse($str) {
		/** @disregard */
		$this->assertFalse(FileUtils::fileIsImage($str));
	}

	#[DataProvider('dataForTestFileIsImageIsTrue')]
	public function testFileIsImageIsTrue($str) {
		/** @disregard */
		$this->assertTrue(FileUtils::fileIsImage($str));
	}
}
