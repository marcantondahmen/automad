<?php

namespace Automad\Ai\Mcp\Schema;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageSchemaTest extends TestCase {
	public static function dataForTestPageSchemaIsSame() {
		return array(
			array(
				PageSchema::create(),
				'create'
			),
			array(
				PageSchema::update(),
				'update'
			)
		);
	}

	#[DataProvider('dataForTestPageSchemaIsSame')]
	public function testPageSchemaIsSame(array $schema, string $expected) {
		/** @disregard */
		$this->assertEquals(
			json_encode(json_decode(file_get_contents(__DIR__ . "/PageSchema/$expected.json"), true), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),
			json_encode($schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
		);
	}
}
