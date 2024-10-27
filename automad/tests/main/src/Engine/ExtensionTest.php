<?php

namespace Automad\Engine;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Extension
 */
class ExtensionTest extends TestCase {
	public function dataForTestGetAssetsIsEqual() {
		return array(
			array(
				array(
					'.css' => array(
						AM_DIR_PACKAGES . '/vendor/extension/styles.css' => AM_DIR_PACKAGES . '/vendor/extension/styles.css'
					),
					'.js' => array(
						AM_DIR_PACKAGES . '/vendor/extension/script.js' => AM_DIR_PACKAGES . '/vendor/extension/script.js'
					)
				)
			)
		);
	}

	/**
	 * @dataProvider dataForTestGetAssetsIsEqual
	 * @testdox getAssets() matches: $expected
	 * @param array $expected
	 */
	public function testGetAssetsIsEqual(array $expected) {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();

		$Extension = new Extension('Vendor/Extension', array('parameter' => ''), $AutomadMock);
		$assets = $Extension->getAssets();

		$this->assertEquals(json_encode($assets), json_encode($expected));
	}

	/**
	 * @testdox getAssets() matches: $expected
	 */
	public function testGetOutputIsEqual() {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();

		$Extension = new Extension('Vendor/Extension', array('parameter' => 'Test'), $AutomadMock);

		$this->assertEquals($Extension->getOutput(), 'Test');
	}
}
