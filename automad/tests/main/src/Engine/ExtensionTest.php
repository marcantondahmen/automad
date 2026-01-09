<?php

namespace Automad\Engine;

use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase {
	public static function dataForTestGetAssetsIsEqual() {
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

	#[DataProvider('dataForTestGetAssetsIsEqual')]
	public function testGetAssetsIsEqual(array $expected) {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();

		$Extension = new Extension('Vendor/Extension', array('parameter' => ''), $AutomadMock);
		$assets = $Extension->getAssets();

		/** @disregard */
		$this->assertEquals(json_encode($assets), json_encode($expected));
	}

	public function testGetOutputIsEqual() {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();

		$Extension = new Extension('Vendor/Extension', array('parameter' => 'Test'), $AutomadMock);

		/** @disregard */
		$this->assertEquals($Extension->getOutput(), 'Test');
	}
}
