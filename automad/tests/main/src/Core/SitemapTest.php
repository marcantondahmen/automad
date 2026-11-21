<?php

namespace Automad\Core;

use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase {
	public function testGetRobots(): void {
		$robots = (new Sitemap())->getRobots();

		/** @disregard */
		$this->assertStringContainsString('Sitemap: http://localhost' . AM_BASE_INDEX . '/sitemap.xml', $robots);
	}

	public function testGetXml(): void {
		$Shared = new Shared();
		$collection = array(
			'/public' => new Page(array(Fields::URL => '/public', Fields::ORIG_URL => '/public'), $Shared),
			'/private' => new Page(array(Fields::URL => '/private', Fields::ORIG_URL => '/private', Fields::PRIVATE => true), $Shared),
			'/alias' => new Page(array(Fields::URL => '/alias', Fields::ORIG_URL => '/public'), $Shared)
		);

		$xml = (new Sitemap())->getXml($collection);

		/** @disregard */
		$this->assertSame(
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
			'<url><loc>http://localhost' . AM_BASE_INDEX . '/public</loc></url>' .
			'</urlset>',
			$xml
		);
	}
}
