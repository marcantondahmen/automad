<?php

namespace Automad\Models;

use Automad\Core\Session;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Selection
 */
class SelectionTest extends TestCase {
	public function dataForTestFilterBreadcrumbsIsEqual() {
		return array(
			array('en', '/en/page/subpage', array('/en', '/en/page', '/en/page/subpage')),
			array('de', '/de/page', array('/de', '/de/page'))
		);
	}

	public function dataForTestFilterCurrentLanguageIsEqual() {
		return array(
			array('en', array('/', '/en', '/en/page', '/en/page/subpage')),
			array('de', array('/', '/de', '/de/page'))
		);
	}

	/**
	 * @dataProvider dataForTestFilterBreadcrumbsIsEqual
	 * @testdox Test filterBreadcrumbs method
	 * @runInSeparateProcess
	 * @param string $lang
	 * @param string $url
	 * @param array $expected
	 */
	public function testFilterBreadcrumbsIsEqual(string $lang, string $url, array $expected): void {
		$_SESSION[Session::I18N_LANG] = 'en';

		$Shared = new Shared();
		$PageCollection = new PageCollection($Shared);

		$Selection = new Selection($PageCollection->get());
		$Selection->filterBreadcrumbs('/en/page/subpage');

		/** @disregard */
		$this->assertEquals(
			array_keys($Selection->getSelection()),
			array('/en', '/en/page', '/en/page/subpage')
		);
	}

	/**
	 * @dataProvider dataForTestFilterCurrentLanguageIsEqual
	 * @testdox Test filterCurrentLanguage method
	 * @runInSeparateProcess
	 * @param string $lang
	 * @param array $expected
	 */
	public function testFilterCurrentLanguageIsEqual($lang, $expected): void {
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
		$this->assertEquals($expected, $actual);
	}
}
