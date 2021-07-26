<?php

namespace Automad\UI\Models;

use Automad\Core\Parse;
use PHPUnit\Framework\TestCase;

/**
 *	@testdox Automad\UI\Models\Replacement
 */

class Replacement_Test extends TestCase {


	/**
	 *	@dataProvider dataForTestReplaceInDataIsSame
	 *	@testdox replaceInData()
	 */

	public function testReplaceInDataIsSame($searchValue, $replaceValue, $isRegex, $isCaseSensitive, $keys, $data, $expected) {

		$ReplacementReflection = new \ReflectionClass('\Automad\UI\Models\Replacement');

		$replaceInData = $ReplacementReflection->getMethod('replaceInData');
		$replaceInData->setAccessible(true);

		$Replacement = new Replacement(
			$searchValue,
			$replaceValue,
			$isRegex,
			$isCaseSensitive
		);

		$replacedData = $replaceInData->invokeArgs($Replacement, [$data, $keys]);

		$this->assertSame(
			json_encode($replacedData, JSON_PRETTY_PRINT), 
			json_encode($expected, JSON_PRETTY_PRINT)
		);

	}

	public function dataForTestReplaceInDataIsSame() {

		return array(
			// Blocks, no regex, not case sensitive.
			array(
				'simple',
				'replaced',
				false,
				false,
				array('+main'),
				Parse::textFile(__DIR__ . '/../../data/blocks.txt'),
				Parse::textFile(__DIR__ . '/../../data/blocks_replaced.txt')
			),
			// Blocks, regex, not case sensitive.
			array(
				'si.p.e',
				'replaced',
				true,
				false,
				array('+main'),
				Parse::textFile(__DIR__ . '/../../data/blocks.txt'),
				Parse::textFile(__DIR__ . '/../../data/blocks_replaced.txt')
			),
			// Text, no regex, not case sensitive.
			array(
				'/Url/to/Page',
				'/replaced/url/to/page',
				false,
				false,
				array('text'),
				Parse::textFile(__DIR__ . '/../../data/text.txt'),
				Parse::textFile(__DIR__ . '/../../data/text_replaced.txt')
			),
			// Text, no regex, case sensitive.
			array(
				'/url/To/page',
				'/replaced/url/to/page',
				false,
				true,
				array('text'),
				Parse::textFile(__DIR__ . '/../../data/text.txt'),
				Parse::textFile(__DIR__ . '/../../data/text.txt')
			),
			// Text, regex, case sensitive.
			array(
				'/url/.*/page',
				'/replaced/url/to/page',
				true,
				true,
				array('text'),
				Parse::textFile(__DIR__ . '/../../data/text.txt'),
				Parse::textFile(__DIR__ . '/../../data/text_replaced.txt')
			)
		);

	}


}