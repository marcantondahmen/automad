<?php

namespace Automad\Models;

use Automad\Core\Session;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Selection
 */
class SelectionTest extends TestCase {
	public function dataForTestSelectionIsEqual() {
		return array(
			array('en', array('/', '/en', '/en/page')),
			array('de', array('/', '/de', '/de/page'))
		);
	}

	/**
	 * @dataProvider dataForTestSelectionIsEqual
	 * @testdox render $lang: $expected
	 * @runInSeparateProcess
	 * @param string $lang
	 * @param array $expected
	 */
	public function testSelectionIsEqual($lang, $expected) {
		$_SESSION[Session::I18N_LANG] = $lang;

		$Shared = new Shared();
		$PageCollection = new PageCollection($Shared);

		$Selection = new Selection($PageCollection->get());
		$Selection->filterCurrentLanguage();

		$pages = $Selection->getSelection();
		$actual = array();

		foreach ($pages as $Page) {
			$actual[] = $Page->url;
		}

		/** @disregard */
		$this->assertEquals(json_encode($expected), json_encode($actual));
	}
}
