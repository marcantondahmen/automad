<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\Pipe
 */

class Pipe_Test extends TestCase {

	
	/**
	 *	@dataProvider dataForTestProcessIsEqual
	 */
	
	public function testProcessIsEqual($value, $functions, $expected) {
		
		$this->assertEquals(Pipe::process($value, $functions), $expected);
		
	}
	
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
	
	
}