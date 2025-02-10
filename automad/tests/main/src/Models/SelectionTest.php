<?php

namespace Automad\Models;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Selection
 */
class SelectionTest extends TestCase {
	/**
	 * @testdox Test filterBreadcrumbs method
	 */
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
