<?php

namespace Automad\Models\Search;

use Automad\Test\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReplacementTest extends TestCase {
	public static function dataForTestReplaceInDataIsSame() {
		return array(
			// Blocks, no regex, not case sensitive.
			array(
				'simple',
				'replaced',
				false,
				false,
				array('+main'),
				Data::load('/blocks'),
				Data::load('/blocks-replaced')
			),
			// Blocks, regex, not case sensitive.
			array(
				'si.p.e',
				'replaced',
				true,
				false,
				array('+main'),
				Data::load('/blocks'),
				Data::load('/blocks-replaced')
			),
			// Blocks, regex, not case sensitive, invalid property.
			array(
				'left',
				'right',
				false,
				false,
				array('+main'),
				Data::load('/blocks'),
				Data::load('/blocks')
			),
			// Text, no regex, not case sensitive.
			array(
				'/Url/to/Page',
				'/replaced/url/to/page',
				false,
				false,
				array('text'),
				Data::load('/text'),
				Data::load('/text-replaced')
			),
			// Text, no regex, case sensitive.
			array(
				'/url/To/page',
				'/replaced/url/to/page',
				false,
				true,
				array('text'),
				Data::load('/text'),
				Data::load('/text')
			),
			// Text, regex, case sensitive.
			array(
				'/url/.*/page',
				'/replaced/url/to/page',
				true,
				true,
				array('text'),
				Data::load('/text'),
				Data::load('/text-replaced')
			)
		);
	}

	#[DataProvider('dataForTestReplaceInDataIsSame')]
	public function testReplaceInDataIsSame($searchValue, $replaceValue, $isRegex, $isCaseSensitive, $keys, $data, $expected) {
		$ReplacementReflection = new \ReflectionClass(Replacement::class);

		$replaceInData = $ReplacementReflection->getMethod('replaceInData');

		$Replacement = new Replacement(
			$searchValue,
			$replaceValue,
			$isRegex,
			$isCaseSensitive
		);

		$replacedData = $replaceInData->invokeArgs($Replacement, array($data, $keys));

		/** @disregard */
		$this->assertSame(
			json_encode($expected, JSON_PRETTY_PRINT),
			json_encode($replacedData, JSON_PRETTY_PRINT)
		);
	}
}
