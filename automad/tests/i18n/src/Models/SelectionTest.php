<?php

namespace Automad\Models;

use Automad\Core\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class SelectionTest extends TestCase {
	public static function dataForTestFilterBreadcrumbsIsEqual() {
		return array(
			array('en', '/en/page/subpage', array('/en', '/en/page', '/en/page/subpage')),
			array('de', '/de/page', array('/de', '/de/page'))
		);
	}

	public static function dataForTestFilterCurrentLanguageIsEqual() {
		return array(
			array('en', array('/', '/en', '/en/page', '/en/page/subpage')),
			array('de', array('/', '/de', '/de/page'))
		);
	}

	#[DataProvider('dataForTestFilterBreadcrumbsIsEqual')]
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

	#[DataProvider('dataForTestFilterCurrentLanguageIsEqual')]
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
