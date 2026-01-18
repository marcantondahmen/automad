<?php

namespace Automad\Models;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

class SelectionTest extends TestCase {
	public function testFilterBreadcrumbsIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Selection = new Selection($Automad->getPages());
		$Selection->filterBreadcrumbs('/page/subpage/breadcrumbs-test');

		/** @disregard */
		$this->assertEquals(
			array_keys($Selection->getSelection()),
			array('/', '/page', '/page/subpage', '/page/subpage/breadcrumbs-test')
		);
	}
}
