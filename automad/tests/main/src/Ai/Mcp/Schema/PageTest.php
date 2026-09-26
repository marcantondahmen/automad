<?php

namespace Automad\Ai\Mcp\Schema;

use PHPUnit\Framework\TestCase;

class PageTest extends TestCase {
	public function testPageCreateSchemaIsSame() {
		/** @disregard */
		$this->assertEquals(
			json_encode(json_decode(file_get_contents(__DIR__ . '/PageTest.json'), true), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),
			json_encode(PageSchema::create(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
		);
	}
}
