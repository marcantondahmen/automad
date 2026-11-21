<?php

namespace Automad;

use Automad\Core\Router;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase {
	public function testReservedUrlsKeepFileExtensions(): void {
		Routes::init(new Router());

		$reserved = Routes::getReserved();

		/** @disregard */
		$this->assertContains('/sitemap.xml', $reserved);
		/** @disregard */
		$this->assertContains('/robots.txt', $reserved);
		/** @disregard */
		$this->assertNotContains('/sitemap', $reserved);
		/** @disregard */
		$this->assertNotContains('/robots', $reserved);
	}
}
