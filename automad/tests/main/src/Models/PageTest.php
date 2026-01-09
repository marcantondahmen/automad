<?php

namespace Automad\Models;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase {
	public function testSharedBlocksDefaultIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Page = $Automad->Context->get();
		$default = $Page->get('+default', true);

		/** @disregard */
		$this->assertEquals($default['blocks'][0]['data']['text'], 'test');
	}
}
