<?php

namespace Automad\Engine;

use Automad\Core\SessionData;
use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ToolboxTest extends TestCase {
	public static function dataForTestSetSessionIsEqual() {
		return array(
			array('%key1', 'Some Session Value', 'Some Session Value'),
			array('key2', 'Some other value', null)
		);
	}

	public static function dataForTestSetSharedIsEqual() {
		return array(
			array('key1', 'Some Shared Value', 'Some Shared Value'),
			array('%key2', 'Some Session Value', null)
		);
	}

	#[DataProvider('dataForTestSetSessionIsEqual')]
	public function testSetSessionIsEqual($key, $value, $expected) {
		$Mock = new Mock();
		$Toolbox = new Toolbox($Mock->createAutomad());
		$Toolbox->set(array($key => $value));

		/** @disregard */
		$this->assertEquals(SessionData::get($key), $expected);
	}

	#[DataProvider('dataForTestSetSharedIsEqual')]
	public function testSetSharedIsEqual($key, $value, $expected) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Toolbox = new Toolbox($Automad);
		$Shared = $Automad->Shared;
		$Toolbox->set(array($key => $value));

		/** @disregard */
		$this->assertEquals($Shared->get($key), $expected);
	}
}
