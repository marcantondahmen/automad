<?php 

namespace Automad\Core;

use Automad\Tests\Mock;
use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\Toolbox
 */

class Toolbox_Test extends TestCase {
	
	
	/**
	 *	@dataProvider dataForTestSetSessionIsEqual
	 *	@testdox set session $key: $value = $expected
	 */
	
	public function testSetSessionIsEqual($key, $value, $expected) {
		
		$Mock = new Mock();
		$Toolbox = new Toolbox($Mock->createAutomad());
		$Toolbox->set(array($key => $value));
		$this->assertEquals(SessionData::get($key), $expected);
		
	}
	
	public function dataForTestSetSessionIsEqual() {
		
		return array(
			array('%key1', 'Some Session Value', 'Some Session Value'),
			array('key2', 'Some other value', NULL)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestSetSharedIsEqual
	 *	@testdox set shared $key: $value = $expected
	 */
	
	public function testSetSharedIsEqual($key, $value, $expected) {
		
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Toolbox = new Toolbox($Automad);
		$Shared = $Automad->Shared;
		$Toolbox->set(array($key => $value));
		$this->assertEquals($Shared->get($key), $expected);
		
	}
	
	public function dataForTestSetSharedIsEqual() {
		
		return array(
			array('key1', 'Some Shared Value', 'Some Shared Value'),
			array('%key2', 'Some Session Value', NULL)
		);
		
	}
	
	
}