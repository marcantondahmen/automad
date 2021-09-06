<?php

namespace Automad\Engine;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Pipe
 */
class PipeTest extends TestCase {
	public function dataForTestProcessIsEqual() {
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

	/**
	 * @dataProvider dataForTestProcessIsEqual
	 * @param mixed $value
	 * @param mixed $functions
	 * @param mixed $expected
	 */
	public function testProcessIsEqual($value, $functions, $expected) {
		$this->assertEquals(Pipe::process($value, $functions), $expected);
	}
}
