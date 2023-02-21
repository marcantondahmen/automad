<?php

namespace Automad\Models;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Page
 */
class PageTest extends TestCase {
	/**
	 * @testdox Test shared default blocks field
	 */
	public function testSharedBlocksDefaultIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Page = $Automad->Context->get();
		$default = $Page->get('+default', true);

		$this->assertEquals($default->blocks[0]->data->text, 'test');
	}
}
