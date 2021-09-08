<?php

namespace Automad\Engine;

use Automad\Core\SessionData;
use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Toolbox
 */
class ToolboxTest extends TestCase {
	public function dataForTestSetSessionIsEqual() {
		return array(
			array('%key1', 'Some Session Value', 'Some Session Value'),
			array('key2', 'Some other value', null)
		);
	}

	public function dataForTestSetSharedIsEqual() {
		return array(
			array('key1', 'Some Shared Value', 'Some Shared Value'),
			array('%key2', 'Some Session Value', null)
		);
	}

	/**
	 * @dataProvider dataForTestSetSessionIsEqual
	 * @testdox set session $key: $value = $expected
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testSetSessionIsEqual($key, $value, $expected) {
		$Mock = new Mock();
		$Toolbox = new Toolbox($Mock->createAutomad());
		$Toolbox->set(array($key => $value));
		$this->assertEquals(SessionData::get($key), $expected);
	}

	/**
	 * @dataProvider dataForTestSetSharedIsEqual
	 * @testdox set shared $key: $value = $expected
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testSetSharedIsEqual($key, $value, $expected) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Toolbox = new Toolbox($Automad);
		$Shared = $Automad->Shared;
		$Toolbox->set(array($key => $value));
		$this->assertEquals($Shared->get($key), $expected);
	}
}
