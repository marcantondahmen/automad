<?php

namespace Automad\Engine;

use Automad\Core\Automad;
use Automad\Core\Session;
use Automad\Models\PageCollection;
use Automad\Models\Shared;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\View
 */
class ViewTest extends TestCase {
	public function dataForTestRenderSharedIsEqual() {
		return array(
			array('en', '/en', 'shared en/home en/home'),
			array('en', '/en/page', 'shared en/home en/page'),
			array('de', '/de', 'shared de/home shared'),
			array('de', '/de/page', 'shared de/home de/page'),
			array('', '/', 'shared de/home shared')
		);
	}

	/**
	 * @dataProvider dataForTestRenderSharedIsEqual
	 * @testdox render $lang, $route, $expected
	 * @runInSeparateProcess
	 * @param string $lang
	 * @param string $route
	 * @param string $expected
	 */
	public function testRenderSharedIsEqual($lang, $route, $expected) {
		$_SESSION[Session::I18N_LANG] = $lang;

		$Shared = new Shared();
		$PageCollection = new PageCollection($Shared);
		$Automad = new Automad($PageCollection->get(), $Shared);
		$Automad->Context->set($Automad->getPage($route));
		$View = new View($Automad);
		$rendered = $View->render();
		$rendered = trim($rendered);

		$this->assertEquals($expected, $rendered);
	}
}
