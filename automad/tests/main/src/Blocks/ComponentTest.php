<?php

namespace Automad\Blocks;

use Automad\Models\Search\Replacement;
use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'test',
				'test replaced',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "component",
					"data": {
						"id": "fV1gSH5wHXJcRjlPxyfxJskhzYCYvQdb"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'Component test',
				'Component test replaced'
			)
		);
	}

	#[DataProvider('dataForTestSearchAndReplaceIsSame')]
	public function testSearchAndReplaceIsSame(
		string $search,
		string $replace,
		bool $isRegex,
		bool $isCaseInsensitive,
		string $blockJson,
		string $expectedString,
		string $expectedReplacedString
	) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$block = json_decode($blockJson, true);

		/** @disregard */
		$this->assertSame(
			$expectedString,
			Component::toString(
				$block,
				$Automad->ComponentCollection
			)
		);

		$blockReplaced = Component::replace(
			$block,
			$Automad->ComponentCollection,
			Replacement::buildRegex($search, $isRegex, $isCaseInsensitive),
			$replace,
			false
		);

		/** @disregard */
		$this->assertSame(
			$expectedReplacedString,
			Component::toString(
				$blockReplaced,
				$Automad->ComponentCollection
			)
		);

		// Restore components file to original state.
		$blockRestored = Component::replace(
			$block,
			$Automad->ComponentCollection,
			Replacement::buildRegex($replace, $isRegex, $isCaseInsensitive),
			$search,
			false
		);

		/** @disregard */
		$this->assertSame(
			$expectedString,
			Component::toString(
				$blockRestored,
				$Automad->ComponentCollection
			)
		);
	}
}
