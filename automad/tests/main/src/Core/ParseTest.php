<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase {
	public static function dataForTestCsvIsSame() {
		return array(
			array(
				'jpg, png, gif',
				array('jpg', 'png', 'gif')
			)
		);
	}

	public static function dataForTestJsonOptionsIsSame() {
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

	#[DataProvider('dataForTestCsvIsSame')]
	public function testCsvIsSame($str, $expected) {
		/** @disregard */
		$this->assertSame(Parse::csv($str), $expected);
	}

	#[DataProvider('dataForTestJsonOptionsIsSame')]
	public function testJsonOptionsIsSame($str, $expected) {
		/** @disregard */
		$this->assertSame(Parse::jsonOptions($str), $expected);
	}
}
