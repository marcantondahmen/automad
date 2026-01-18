<?php

namespace Automad\Engine;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PipeTest extends TestCase {
	public static function dataForTestProcessIsEqual() {
		return array(
			array(
				'',
				array(
					array(
						'name' => 'def',
						'parameters' => array('Some test string.')
					),
					array(
						'name' => '10',
						'parameters' => array()
					),
					array(
						'name' => 'sanitize',
						'parameters' => array(true)
					)
				),
				'some-test'
			),
			array(
				'10',
				array(
					array(
						'name' => '+',
						'parameters' => '10'
					),
					array(
						'name' => '*',
						'parameters' => 2
					)
				),
				40
			)
		);
	}

	#[DataProvider('dataForTestProcessIsEqual')]
	public function testProcessIsEqual($value, $functions, $expected) {
		/** @disregard */
		$this->assertEquals(Pipe::process($value, $functions), $expected);
	}
}
