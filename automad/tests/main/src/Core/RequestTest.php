<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {
	public static function dataForTestTrailingSlashRedirectIsLocal() {
		return array(
			array('/page/', '/page'),
			array('/a/b/', '/a/b'),
			array('//evil.com/', '/evil.com'),
			array('/\\evil.com/', '/evil.com'),
			array("/\t/evil.com/", '/evil.com'),
			array('///evil.com//', '/evil.com'),
			array('///', '/')
		);
	}

	#[DataProvider('dataForTestTrailingSlashRedirectIsLocal')]
	public function testTrailingSlashRedirectIsLocal(string $request, string $expected) {
		$url = Request::trailingSlashRedirect($request);

		/** @disregard */
		$this->assertEquals(AM_BASE_INDEX . $expected, $url);
		/** @disregard */
		$this->assertFalse(str_starts_with($url, '//'));
		/** @disregard */
		$this->assertFalse(str_starts_with($url, '/\\'));
	}
}
