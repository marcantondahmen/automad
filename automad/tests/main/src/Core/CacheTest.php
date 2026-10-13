<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase {
	private array $server = array();

	public function setUp(): void {
		$this->server = $_SERVER;
	}

	public function tearDown(): void {
		$_SERVER = $this->server;
	}

	public static function dataForTestPageCacheKeyIgnoresHostHeaders() {
		return array(
			array(array()),
			array(array('HTTP_X_FORWARDED_SERVER' => 'real.com', 'HTTP_X_FORWARDED_HOST' => 'evil.com')),
			array(array('HTTP_HOST' => 'evil.com'))
		);
	}

	#[DataProvider('dataForTestPageCacheKeyIgnoresHostHeaders')]
	public function testPageCacheKeyIgnoresHostHeaders(array $headers) {
		$_SERVER = array_merge(
			array(
				'HTTP_HOST' => 'real.com',
				'SERVER_NAME' => 'real.com',
				'SERVER_PORT' => '80'
			),
			$headers
		);

		$CacheReflection = new \ReflectionClass(Cache::class);
		$Cache = $CacheReflection->newInstanceWithoutConstructor();
		$file = $CacheReflection->getMethod('getPageCacheFilePath')->invoke($Cache);

		// AM_SERVER is defined as 'http://localhost' in the test bootstrap.
		/** @disregard */
		$this->assertEquals(Cache::DIR_PAGES . '/localhost' . AM_REQUEST . '/page', $file);
	}
}
