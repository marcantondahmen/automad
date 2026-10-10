<?php

namespace Automad\Ai\Mcp\Schema;

use Automad\System\FileSystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageSchemaTest extends TestCase {
	public static function dataForTestOutputSchemaIsObject() {
		return array(
			array(PageSchema::outputCreate()),
			array(PageSchema::outputDuplicate()),
			array(PageSchema::outputMove()),
			array(PageSchema::outputPublish()),
			array(PageSchema::outputRead()),
			array(PageSchema::outputReorderChildren()),
			array(PageSchema::outputSearch()),
			array(PageSchema::outputSearchByTag()),
			array(PageSchema::outputTree()),
			array(PageSchema::outputUpdate())
		);
	}

	public static function dataForTestPageSchemaIsSame() {
		return array(
			array(
				PageSchema::inputCreate(),
				'inputCreate'
			),
			array(
				PageSchema::inputUpdate(),
				'inputUpdate'
			)
		);
	}

	#[DataProvider('dataForTestOutputSchemaIsObject')]
	public function testOutputSchemaIsObject(array $schema) {
		// The root of an output schema has to be an object schema.
		/** @disregard */
		$this->assertSame('object', $schema['type']);
		/** @disregard */
		$this->assertArrayHasKey('properties', $schema);
	}

	#[DataProvider('dataForTestPageSchemaIsSame')]
	public function testPageSchemaIsSame(array $schema, string $expected) {
		/** @disregard */
		$this->assertEquals(
			json_encode(FileSystem::readJson(__DIR__ . "/PageSchema/$expected.json", true), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),
			json_encode($schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
		);
	}
}
